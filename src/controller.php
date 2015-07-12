<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation;
use Venyii\HipChatCommander\Controller;

$app->mount('/cb', new Controller\Callback());
$app->mount('', new Controller\Bot());
$app->mount('', new Controller\Descriptor());

$app->error(function (\Exception $e) use ($app) {
    if ($e instanceof RequestException) {
        $app['logger']->error('[EH] '.$e->getMessage().' - Code: '.$e->getCode().' - Req: '.$e->getResponse()->getBody()->getContents());
    } else {
        $app['logger']->error('[EH] '.$e->getMessage().' - Code: '.$e->getCode());
    }

    if ($app['debug']) {
        throw $e;
    }

    return new HttpFoundation\Response(null, 503, array('X-Status-Code' => 503));
});
