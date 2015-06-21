<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Venyii\HipChatCommander\Api;

class Bot implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        /* @var $router Application */
        $router = $app['controllers_factory'];

        $router->post('/addon', array($this, 'addonBotAction'));
        $router->post('/simple', array($this, 'simpleBotAction'));

        return $router;
    }

    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function addonBotAction(Request $request, Application $app)
    {
        return $this->botAction(Api\Request::REQ_TYPE_ADDON, $request, $app);
    }

    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function simpleBotAction(Request $request, Application $app)
    {
        return $this->botAction(Api\Request::REQ_TYPE_SIMPLE, $request, $app);
    }

    /**
     * @param string      $type
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     *
     * @throws \Exception
     */
    private function botAction($type, Request $request, Application $app)
    {
        $apiRequest = new Api\Request($request, $app['hc.api_registry'], $type);

        if ($type === Api\Request::REQ_TYPE_ADDON) {
            try {
                $apiRequest->validate();
            } catch (\Exception $e) {
                $app['logger']->error('Failed validating the request: '.$e->getMessage());

                throw $e;
            }
        }

        /* @var \Venyii\HipChatCommander\Config\Room $room */
        $room = $app['hc.config']->getRoomById($apiRequest->getRoom()->getId());

        if ($room === null) {
            $app['logger']->warning(sprintf('Unsupported room: %s (%d)', $apiRequest->getRoom()->getName(), $apiRequest->getRoom()->getId()));

            return new Response();
        }

        /* @var \Venyii\HipChatCommander\Package\AbstractPackage $package */
        $package = $app['hc.package_locator']->getPackage($apiRequest->getPackage());

        if ($package === null) {
            $app['logger']->warning('Unknown package: '.$apiRequest->getPackage());

            return new Response();
        }

        $roomPackage = $room->getPackageByName($package->getName());

        if ($roomPackage === null) {
            $app['logger']->warning(sprintf('Unsupported package "%s" for room "%d"', $apiRequest->getPackage(), $room->getId()));

            return new Response();
        }

        $argument = empty($apiRequest->getArgs()) ? null : $apiRequest->getArgs()[0];
        $command = $package->getCommandByArgument($argument);

        if ($command === null) {
            $app['logger']->warning('Unsupported command: '.$argument);

            return $this->createJsonResponse(Api\Response::createError('Unsupported command!'));
        }

        if ($argument === null) {
            $apiRequest->setDefaultArg($package->getDefaultCommand());
        }

        if (!$roomPackage->isUserPermitted($apiRequest->getUser()->getId(), $command->getName())) {
            return $this->createJsonResponse(Api\Response::createError('You are not permitted to perform this action!'));
        }

        $methodName = $app['hc.method_builder']->generate($command);

        if (!method_exists($package, $methodName)) {
            $app['logger']->error(sprintf(
                'Tried to call non existing method %s on class %s (Package: %s)',
                $methodName, get_class($package), $package->getName()
            ));

            return $this->createJsonResponse(Api\Response::createError('Unsupported command!'));
        }

        $pkgCache = $app['hc.pkg_cache']($roomPackage->getCacheNs() ?: $apiRequest->getClientId(), $package->getName());
        $apiClient = $app['hc.api_client']($apiRequest->getClientId(), $apiRequest->getType());

        /** @var Api\Response $response */
        $response = $package
            ->setRequest($apiRequest)
            ->setCache($pkgCache)
            ->setApiClient($apiClient)
            ->setLogger($app['logger'])
            ->setOptions($roomPackage->getOptions())
            ->{$methodName}();

        if (!$response instanceof Api\Response) {
            return new Response(null, 200);
        }

        return $this->createJsonResponse($response);
    }

    /**
     * @param Api\Response $response
     *
     * @return JsonResponse
     */
    private function createJsonResponse(Api\Response $response)
    {
        return new JsonResponse($response->toArray());
    }
}
