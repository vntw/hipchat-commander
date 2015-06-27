<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Api;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Stream\Stream;
use Psr\Log\LoggerInterface;
use Venyii\HipChatCommander\Api\Request as ApiRequest;
use Venyii\HipChatCommander\Config\Config;

class Client
{
    const API_URL = 'https://api.hipchat.com/v2/';

    private $clientId;
    private $config;
    private $registry;
    private $httpClient;
    private $logger;
    private $requestType;

    /**
     * @param string          $clientId
     * @param Config          $config
     * @param Client\Registry $registry
     * @param GuzzleClient    $httpClient
     * @param LoggerInterface $logger
     * @param string|null     $requestType
     */
    public function __construct($clientId, Config $config, Client\Registry $registry, GuzzleClient $httpClient, LoggerInterface $logger, $requestType = null)
    {
        $this->clientId = $clientId;
        $this->config = $config;
        $this->registry = $registry;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->requestType = $requestType;
    }

    /**
     * @param string $uri
     * @param array  $data
     *
     * @return ResponseInterface
     *
     * @throws \Exception
     */
    public function send($uri, array $data)
    {
        if ($this->requestType !== ApiRequest::REQ_TYPE_ADDON) {
            // only addon can send api requests
            $this->logger->debug('Tried to send request in non-addon mode');

            return;
        }

        $this->logger->debug('API Request: '.$uri);

        if ($this->getAuthToken() === null) {
            $this->renewAuthToken();
        }

        $response = null;

        try {
            $response = $this->doSend($uri, $data);
        } catch (RequestException $e) {
            $this->logger->error($e->getMessage());

            switch ($e->getCode()) {
                case 401:
                    // expired auth token? try once again
                    $this->logger->debug('Got 401 - Trying to renew auth token');
                    $this->renewAuthToken();
                    $response = $this->doSend($uri, $data);
                    break;
                case 429:
                    $this->logger->warning('Got 429 - Rate limited', [
                        'limit' => $e->getResponse()->getHeader('X-RateLimit-Limit'),
                        'remaining' => $e->getResponse()->getHeader('X-RateLimit-Remaining'),
                        'reset' => $e->getResponse()->getHeader('X-RateLimit-Reset'),
                    ]);
                    // rate limited - fall through
                default:
                    throw $e;
            }
        }

        return $response;
    }

    /**
     * @param string $uri
     * @param array  $data
     *
     * @return ResponseInterface
     *
     * @throws Exception\HipChatException
     */
    private function doSend($uri, array $data)
    {
        $request = $this->httpClient->createRequest('POST', $this->buildApiUrl($uri));
        $request->setBody(Stream::factory(json_encode($data)));

        $this->prepareRequest($request);

        try {
            $response = $this->httpClient->send($request);
        } catch (RequestException $e) {
            switch ($e->getCode()) {
                case 400:
                    throw new Exception\BadRequestException($e->getMessage(), $e->getRequest(), $e->getResponse(), $e);
                case 401:
                    throw new Exception\UnauthorizedException($e->getMessage(), $e->getRequest(), $e->getResponse(), $e);
                case 403:
                    throw new Exception\ForbiddenException($e->getMessage(), $e->getRequest(), $e->getResponse(), $e);
                case 404:
                    throw new Exception\NotFoundException($e->getMessage(), $e->getRequest(), $e->getResponse(), $e);
                case 429:
                    throw new Exception\RateLimitReachedException($e->getMessage(), $e->getRequest(), $e->getResponse(), $e);
                case 500:
                    throw new Exception\InternalServerErrorException($e->getMessage(), $e->getRequest(), $e->getResponse(), $e);
                case 503:
                    throw new Exception\ServerUnavailableException($e->getMessage(), $e->getRequest(), $e->getResponse(), $e);
                default:
                    throw new Exception\HipChatException($e->getMessage(), $e->getRequest(), $e->getResponse(), $e);
            }
        }

        return $response;
    }

    /**
     * Get a new access token and save everything if it´s valid.
     *
     * @param string|null $oauthId
     * @param string|null $oauthSecret
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function renewAuthToken($oauthId = null, $oauthSecret = null)
    {
        if ($oauthId === null || $oauthSecret === null) {
            $this->logger->info('Renewing auth token with existing creds');

            $creds = $this->registry->getClientCredentials($this->clientId);

            $oauthId = isset($creds['oauthId']) ? $creds['oauthId'] : null;
            $oauthSecret = isset($creds['oauthSecret']) ? $creds['oauthSecret'] : null;
        } else {
            $this->logger->info('Renewing auth token with given creds');
        }

        if ($oauthId === null || $oauthSecret === null) {
            throw new \InvalidArgumentException('Missing credentials');
        }

        $this->logger->debug('Using: '.$oauthId.' - '.$oauthSecret);

        $authBody = [
            'grant_type' => 'client_credentials',
            'scope' => 'send_notification view_group admin_room',
        ];

        $request = $this->httpClient->createRequest('POST', $this->buildApiUrl('oauth/token'));
        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader('Authorization', 'Basic '.base64_encode($oauthId.':'.$oauthSecret));
        $request->setBody(Stream::factory(json_encode($authBody)));

        $authJson = $this->httpClient->send($request)->json();
        $authToken = $authJson['access_token'];

        $expiresInSeconds = (int) $authJson['expires_in'];

        $this->logger->debug('Got new auth token: '.$authToken.' - Expires: '.$expiresInSeconds);
        $this->registry->updateCreds($oauthId, $oauthSecret, $authToken, new \DateTime(sprintf('+%d seconds', $expiresInSeconds)));

        return $authToken;
    }

    /**
     * @return string|null
     */
    private function getAuthToken()
    {
        $creds = $this->registry->getClientCredentials($this->clientId);

        if (!is_array($creds) || !isset($creds['authToken'])) {
            return null;
        }

        if ($creds['expires'] <= (new \DateTime())) {
            // token expired
            return null;
        }

        return $creds['authToken'];
    }

    /**
     * @param RequestInterface $request
     */
    private function prepareRequest(RequestInterface $request)
    {
        $request->setHeader('Content-Type', 'application/json');
        $this->addAuthTokenHeader($request);
    }

    /**
     * @param RequestInterface $request
     */
    private function addAuthTokenHeader(RequestInterface $request)
    {
        $request->addHeader('Authorization', 'Bearer '.$this->getAuthToken());
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    private function buildApiUrl($uri)
    {
        return self::API_URL.ltrim($uri, '/');
    }
}
