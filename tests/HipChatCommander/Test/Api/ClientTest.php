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
use GuzzleHttp\Stream\Stream;
use Venyii\HipChatCommander\Api;
use Venyii\HipChatCommander\Test\WebTestCase;

class ClientTest extends WebTestCase
{
    /**
     * @param int    $code
     * @param string $class
     *
     * @dataProvider dataProviderErrorCodes
     */
    public function testClientThrowsRateLimitException($code, $class)
    {
        $this->expectException($class);
        $this->createTestConfig();

        $request = new Request('POST', 'http://not-important');
        $response = new Response($code);
        $requestException = new RequestException('Not Important', $request, $response);

        $httpClientMock = $this->getMockBuilder(\GuzzleHttp\Client::class)
            ->setMethods(['send'])
            ->getMock();

        $httpClientMock
            ->expects($this->at(0))
            ->method('send')
            ->will($this->throwException($requestException));

        if ($code === 401) {
            // 401 renew auth token handling
            $jsonResponseData = [
                'access_token' => '__token__',
                'expires_in' => '1337',
            ];

            $renewAuthTokenResponse = new Response(200);
            $renewAuthTokenResponse->setBody(Stream::factory(json_encode($jsonResponseData)));

            $httpClientMock
                ->expects($this->at(1))
                ->method('send')
                ->will($this->returnValue($renewAuthTokenResponse));
            $httpClientMock
                ->expects($this->at(2))
                ->method('send')
                ->will($this->throwException($requestException));
        }

        $client = new Api\Client('__oauthId__', $this->app['hc.config'], $this->app['hc.api_registry'], $httpClientMock, $this->app['logger']);
        $client->send('/not-important', []);
    }

    /**
     * @return array
     */
    public static function dataProviderErrorCodes()
    {
        return [
            [400, 'Venyii\HipChatCommander\Api\Exception\BadRequestException'],
            [401, 'Venyii\HipChatCommander\Api\Exception\UnauthorizedException'],
            [403, 'Venyii\HipChatCommander\Api\Exception\ForbiddenException'],
            [404, 'Venyii\HipChatCommander\Api\Exception\NotFoundException'],
            [429, 'Venyii\HipChatCommander\Api\Exception\RateLimitReachedException'],
            [500, 'Venyii\HipChatCommander\Api\Exception\InternalServerErrorException'],
            [503, 'Venyii\HipChatCommander\Api\Exception\ServerUnavailableException'],
            [504, 'Venyii\HipChatCommander\Api\Exception\HipChatException'],
            [418, 'Venyii\HipChatCommander\Api\Exception\HipChatException'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageClass()
    {
        return 'Venyii\HipChatCommander\Package\HelloWorld';
    }
}
