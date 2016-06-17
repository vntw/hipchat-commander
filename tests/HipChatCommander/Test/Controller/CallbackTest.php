<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test\Controller;

use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use Monolog\Logger;
use Venyii\HipChatCommander\Test\WebTestCase;

class CallbackTest extends WebTestCase
{
    public function testGlobalInstallCallbackSuccess()
    {
        $this->createTestConfig();

        $data = [
            'oauthId' => '__oauthId__',
            'oauthSecret' => '__oauthSecret__',
            'groupId' => '34531',
        ];

        $authResponse = new Response(200, [], Stream::factory(json_encode(['access_token' => '__authToken__', 'expires_in' => 3600])));

        $httpClientMock = $this->getMockBuilder(\GuzzleHttp\Client::class)->setMethods(['send'])->getMock();
        $httpClientMock
            ->expects($this->once())
            ->method('send')
            ->will($this->returnValue($authResponse));

        $clientMock = $this->getMockBuilder(\Venyii\HipChatCommander\Api\Client::class)
            ->setConstructorArgs([
                '__clientId__',
                $this->app['hc.config'],
                $this->app['hc.api_registry'],
                $httpClientMock,
                $this->app['logger'],
            ])
            ->setMethods(null)
            ->getMock()
        ;

        $this->app['hc.api_client'] = $this->app->protect(function () use ($clientMock) { return $clientMock; });

        $client = $this->createClient();
        $client->request('POST', '/cb/install', [], [], [], json_encode($data));
        $response = $client->getResponse();

        $this->assertLoggerHasRecord('Got authToken "__authToken__"', Logger::DEBUG);
        $this->assertEquals(200, $response->getStatusCode());

        $creds = $this->app['hc.api_registry']->getClient('__oauthId__');
        $installDate = $creds['date'];
        $expiresDate = $creds['credentials']['expires'];
        unset($creds['date']);
        unset($creds['credentials']['expires']);

        $this->assertEquals(
            [
                'groupId' => 34531,
                'roomId' => null,
                'credentials' => [
                    'oauthId' => '__oauthId__',
                    'oauthSecret' => '__oauthSecret__',
                    'authToken' => '__authToken__',
                ],
            ],
            $creds
        );

        $this->assertInstanceOf('DateTime', $installDate);
        $this->assertInstanceOf('DateTime', $expiresDate);
    }

    public function testRoomInstallCallbackSuccess()
    {
        $this->createTestConfig();

        $data = [
            'oauthId' => '__oauthId__',
            'oauthSecret' => '__oauthSecret__',
            'groupId' => '34531',
            'roomId' => '986531',
        ];

        $authResponse = new Response(200, [], Stream::factory(json_encode(['access_token' => '__authToken__', 'expires_in' => 3600])));

        $httpClientMock = $this->getMockBuilder(\GuzzleHttp\Client::class)->setMethods(['send'])->getMock();
        $httpClientMock
            ->expects($this->once())
            ->method('send')
            ->will($this->returnValue($authResponse));

        $clientMock = $this->getMockBuilder(\Venyii\HipChatCommander\Api\Client::class)
            ->setConstructorArgs([
                '__clientId__',
                $this->app['hc.config'],
                $this->app['hc.api_registry'],
                $httpClientMock,
                $this->app['logger'],
            ])
            ->setMethods(null)
            ->getMock()
        ;

        $this->app['hc.api_client'] = $this->app->protect(function () use ($clientMock) { return $clientMock; });

        $client = $this->createClient();
        $client->request('POST', '/cb/install', [], [], [], json_encode($data));
        $response = $client->getResponse();

        $this->assertLoggerHasRecord('Got authToken "__authToken__"', Logger::DEBUG);
        $this->assertEquals(200, $response->getStatusCode());

        $creds = $this->app['hc.api_registry']->getClient('__oauthId__');
        $installDate = $creds['date'];
        $expiresDate = $creds['credentials']['expires'];
        unset($creds['date']);
        unset($creds['credentials']['expires']);

        $this->assertEquals(
            [
                'groupId' => 34531,
                'roomId' => 986531,
                'credentials' => [
                    'oauthId' => '__oauthId__',
                    'oauthSecret' => '__oauthSecret__',
                    'authToken' => '__authToken__',
                ],
            ],
            $creds
        );

        $this->assertInstanceOf('DateTime', $installDate);
        $this->assertInstanceOf('DateTime', $expiresDate);
    }

    public function testInstallCallbackFailsWithMissingCreds()
    {
        $this->createTestConfig();
        $this->setExpectedException('Exception', 'Invalid installation request');

        $client = $this->createClient();
        $client->request('POST', '/cb/install', [], [], [], json_encode([]));
    }

    public function testInstallCallbackFailsWhenAlreadyInstalledGlobally()
    {
        $this->createTestConfig();
        $this->app['hc.api_registry']->install('2982593d-9985-4cac-a008-8393912a3656', '__clientSecret__', 38423);
        $this->setExpectedException('Exception', 'A client is already installed for this group/room combination');
        $this->app['hc.api_registry']->install('2982593d-9985-4cac-a008-8393912a3656', '__clientSecret__', 38423);
    }

    public function testInstallCallbackFailsWhenAlreadyInstalledInRoom()
    {
        $this->createTestConfig();
        $this->app['hc.api_registry']->install('2982593d-9985-4cac-a008-8393912a3656', '__clientSecret__', 38423, 987465);
        $this->setExpectedException('Exception', 'A client is already installed for this group/room combination');
        $this->app['hc.api_registry']->install('2982593d-9985-4cac-a008-8393912a3656', '__clientSecret__', 38423, 987465);
    }

    public function testUninstallSuccess()
    {
        $this->createTestConfig();

        $this->app['hc.api_registry']->install('2982593d-9985-4cac-a008-8393912a3656', '__clientSecret__', 38423);

        $client = $this->createClient();
        $client->request('DELETE', '/cb/install/2982593d-9985-4cac-a008-8393912a3656', [], [], [], json_encode([]));
        $response = $client->getResponse();

        $this->assertLoggerHasRecord('Uninstalled client: 2982593d-9985-4cac-a008-8393912a3656', Logger::INFO);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUninstallFailsWithInvalidClientId()
    {
        $this->createTestConfig();
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $client = $this->createClient();
        $client->request('DELETE', '/cb/install/__clientId__', [], [], [], json_encode([]));
    }

    public function testInstallReturnsErrorOnFailure()
    {
        $this->createTestConfig();

        $this->app['debug'] = false;

        $data = [
            'oauthId' => '__oauthId__',
            'oauthSecret' => '__oauthSecret__',
            'groupId' => '34531',
            'roomId' => '986531',
        ];

        $httpClientMock = $this->getMockBuilder(\GuzzleHttp\Client::class)->setMethods(['send'])->getMock();
        $httpClientMock
            ->expects($this->once())
            ->method('send')
            ->will($this->throwException(new \Exception('Some error', 599)));

        $clientMock = $this->getMockBuilder(\Venyii\HipChatCommander\Api\Client::class)
            ->setConstructorArgs([
                '__clientId__',
                $this->app['hc.config'],
                $this->app['hc.api_registry'],
                $httpClientMock,
                $this->app['logger'],
            ])
            ->setMethods(null)
            ->getMock()
        ;

        $this->app['hc.api_client'] = $this->app->protect(function () use ($clientMock) { return $clientMock; });

        $client = $this->createClient();
        $client->request('POST', '/cb/install', [], [], [], json_encode($data));
        $response = $client->getResponse();

        $this->assertLoggerHasRecord('[EH] Some error - Code: 599', Logger::ERROR);
        $this->assertSame(503, $response->getStatusCode());
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageClass()
    {
        return 'Venyii\HipChatCommander\Package\HelloWorld';
    }
}
