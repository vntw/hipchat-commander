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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Venyii\HipChatCommander\Api\Client\Registry;

class Callback implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        /* @var $router Application */
        $router = $app['controllers_factory'];

        $router->post('/install', array($this, 'installAction'));

        $router
            ->delete('/install/{clientId}', array($this, 'uninstallAction'))
            ->assert('clientId', '[a-zA-Z0-9\-]{36}');

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
    public function installAction(Request $request, Application $app)
    {
        $installer = json_decode($request->getContent(), true);

        $app['logger']->debug('Install Callback');

        if (json_last_error()) {
            $app['logger']->error('Install JSON Error: '.json_last_error_msg());
            throw new \Exception('JSON Error');
        }

        $oauthId = isset($installer['oauthId']) ? $installer['oauthId'] : null;
        $oauthSecret = isset($installer['oauthSecret']) ? $installer['oauthSecret'] : null;
        $groupId = isset($installer['groupId']) ? (int) $installer['groupId'] : null;
        $roomId = isset($installer['roomId']) ? (int) $installer['roomId'] : null;

        if (!$oauthId || !$oauthSecret || !$groupId) {
            throw new \Exception('Invalid installation request');
        }

        $app['logger']->debug(sprintf('Got oauthId %s and oauthSecret: %s', $oauthId, $oauthSecret));

        /* @var Registry $registry */
        $registry = $app['hc.api_registry'];
        $registry->install($oauthId, $oauthSecret, $groupId, $roomId);

        /** @var \Venyii\HipChatCommander\Api\Client $client */
        $client = $app['hc.api_client']($oauthId);

        try {
            // fetch auth token
            $authToken = $client->renewAuthToken($oauthId, $oauthSecret);
            $app['logger']->debug('Got AuthToken: '.$authToken);
        } catch (\Exception $e) {
            $registry->uninstall($oauthId);

            throw $e;
        }

        return new Response(null, 200);
    }

    /**
     * @param Application $app
     * @param string      $clientId
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function uninstallAction(Application $app, $clientId)
    {
        /* @var Registry $registry */
        $registry = $app['hc.api_registry'];

        if ($registry->uninstall($clientId)) {
            $app['logger']->info('Uninstalled client: '.$clientId);
        } else {
            $app['logger']->warning('Tried to uninstall unknown client: '.$clientId);
        }

        return new Response(null, 200);
    }
}
