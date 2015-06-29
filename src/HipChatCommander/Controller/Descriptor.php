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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Venyii\HipChatCommander\Descriptor\Builder;

class Descriptor implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        /* @var $router ControllerCollection */
        $router = $app['controllers_factory'];

        $router->get('/package.json', array($this, 'descriptorFileAction'));

        return $router;
    }

    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function descriptorFileAction(Request $request, Application $app)
    {
        $builder = new Builder(
            $request->getSchemeAndHttpHost().$request->getBaseUrl(),
            $app['hc.config'],
            $app['hc.package_loader']->getPackages()
        );

        return new JsonResponse($builder->build());
    }
}
