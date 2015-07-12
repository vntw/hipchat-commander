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
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Venyii\HipChatCommander\Api;

class Bot implements ControllerProviderInterface
{
    /**
     * @var Application
     */
    private $app;

    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $this->app = $app;

        /* @var $router ControllerCollection */
        $router = $app['controllers_factory'];

        $router->post('/bot', [$this, 'botAction']);

        return $router;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function botAction(Request $request)
    {
        $apiRequest = new Api\Request($request);
        $this->validateApiRequest($apiRequest);

        /* @var \Venyii\HipChatCommander\Config\Room $room */
        $room = $this->app['hc.config']->getRoomById($apiRequest->getRoom()->getId());

        if ($room === null) {
            $this->app['logger']->warning(sprintf('Unsupported room: %s (%d)', $apiRequest->getRoom()->getName(), $apiRequest->getRoom()->getId()));

            return new Response();
        }

        /* @var \Venyii\HipChatCommander\Package\AbstractPackage $package */
        $package = $this->app['hc.package_loader']->getPackage($apiRequest->getPackage());

        if ($package === null) {
            $this->app['logger']->warning('Unknown package: '.$apiRequest->getPackage());

            return new Response();
        }

        $roomPackage = $room->getPackageByName($package->getName());

        if ($roomPackage === null) {
            $this->app['logger']->warning(sprintf('Unsupported package "%s" for room "%d"', $apiRequest->getPackage(), $room->getId()));

            return new Response();
        }

        $argument = empty($apiRequest->getArgs()) ? null : $apiRequest->getArgs()[0];
        $command = $package->getCommandByArgument($argument);

        if ($command === null) {
            $this->app['logger']->warning('Unsupported command: '.$argument);

            return $this->createJsonResponse(Api\Response::createError('Unsupported command!'));
        }

        if ($argument === null) {
            $apiRequest->setDefaultArg($package->getDefaultCommand()->getName());
        }

        if (!$roomPackage->isUserPermitted($apiRequest->getUser()->getId(), $command->getName())) {
            return $this->createJsonResponse(Api\Response::createError('You are not permitted to perform this action!'));
        }

        $methodName = $this->app['hc.method_builder']->generate($command);

        if (!method_exists($package, $methodName)) {
            $this->app['logger']->error(sprintf(
                'Tried to call non existing method %s on class %s (Package: %s)',
                $methodName, get_class($package), $package->getName()
            ));

            return $this->createJsonResponse(Api\Response::createError('Unsupported command!'));
        }

        $pkgCache = $this->app['hc.pkg_cache']($roomPackage->getCacheNs() ?: $apiRequest->getClientId(), $package->getName());
        $apiClient = $this->app['hc.api_client']($apiRequest->getClientId());

        /** @var Api\Response $response */
        $response = $package
            ->setRequest($apiRequest)
            ->setCache($pkgCache)
            ->setApiClient($apiClient)
            ->setLogger($this->app['logger'])
            ->setOptions($roomPackage->getOptions())
            ->{$methodName}();

        if (!$response instanceof Api\Response) {
            return new Response(null, 200);
        }

        return $this->createJsonResponse($response);
    }

    /**
     * @param Api\Request $apiRequest
     *
     * @throws \Exception
     */
    private function validateApiRequest(Api\Request $apiRequest)
    {
        try {
            $this->app['hc.api_request_validator']->validate($apiRequest);
        } catch (\Exception $e) {
            $this->app['logger']->error('Failed validating the request: '.$e->getMessage());
            throw $e;
        }
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
