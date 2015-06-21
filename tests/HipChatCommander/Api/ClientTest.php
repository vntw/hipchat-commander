<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test\Api;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use Venyii\HipChatCommander\Api;
use Venyii\HipChatCommander\Test\WebTestCase;

class ClientTest extends WebTestCase
{
    public function testClientThrowsRateLimitException()
    {
        $this->createTestConfig();

        $this->app['hc.api_registry']->install('__oauthId__', '__oauthSecret__', 684351, 7331);
        $this->app['hc.api_registry']->updateCreds('__oauthId__', '__oauthSecret__', '__authToken__', new \DateTime('+1 hour'));

        $request = new Request('POST', 'http://not-important');
        $response = new Response(429);

        $requestException = new RequestException('Not Important', $request, $response);

        $httpClientMock = $this->getMock('GuzzleHttp\Client', ['send']);
        $httpClientMock
            ->expects($this->once())
            ->method('send')
            ->will($this->throwException($requestException));

        $client = new Api\Client('__oauthId__', $this->app['hc.config'], $this->app['hc.api_registry'], $httpClientMock, $this->app['logger'], Api\Request::REQ_TYPE_ADDON);

        $this->setExpectedException('Venyii\HipChatCommander\Api\Exception\RateLimitReachedException');

        $client->send('blub', []);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageClass()
    {
        return 'Venyii\HipChatCommander\Package\HelloWorld';
    }
}
