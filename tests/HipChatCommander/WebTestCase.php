<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander;

use Doctrine\Common\Cache\FilesystemCache;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use org\bovigo\vfs\vfsStream;
use Silex\WebTestCase as BaseWebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Venyii\HipChatCommander\Config\Config;
use Venyii\HipChatCommander\Test\Mock\ApiClientMock;

abstract class WebTestCase extends BaseWebTestCase
{
    /**
     * @var FilesystemCache
     */
    protected $cache;

    /**
     * @return string|null
     */
    protected function getPackageName()
    {
        return null;
    }

    /**
     * @return string|null
     */
    protected function getPackageClass()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../src/bootstrap.php';
        $app['debug'] = true;
        $app['exception_handler']->disable();

        $app['logger'] = new Logger('TestLogger', [new TestHandler()]);

        $app['hc.cache_dir'] = $app['hc.cache_dir'].'/tests';

        $app['hc.api_client'] = $app->protect(function ($clientId) use ($app) {
            return new ApiClientMock($clientId, $app['hc.config'], $app['hc.api_registry'], $app['hc.http_client'], $app['logger']);
        });

        $httpClientMock = $this->getMock('\GuzzleHttp\Client');

        $app['hc.http_client'] = $app->share(function () use ($app, $httpClientMock) {
            return $httpClientMock;
        });

        if ($pkgName = $this->getPackageName()) {
            $this->cache = $app['hc.pkg_cache']($pkgName);
        }

        return $app;
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->clearTestingCache();

        $this->app['hc.api_registry']->install('__oauthId__', '__oauthSecret__', 963852);
        $this->app['hc.api_registry']->updateCreds('__oauthId__', '__oauthSecret__', '__authToken__', new \DateTime('+1 hour'));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->clearTestingCache();

        parent::tearDown();
    }

    /**
     * @param array|string $record
     * @param int          $level
     *
     * @return bool
     */
    protected function assertLoggerHasRecord($record, $level)
    {
        /* @var Logger $logger */
        $logger = $this->app['logger'];
        $levelMethod = 'has'.ucfirst(strtolower($logger::getLevelName($level)));

        $this->assertTrue($logger->getHandlers()[0]->{$levelMethod}($record), 'Failed asserting that a record exists');
    }

    /**
     * @param array $data
     *
     * @return Response
     */
    protected function request(array $data = array())
    {
        $client = $this->createClient();
        $client->request('POST', '/bot', [], [], [], json_encode($data));

        return $client->getResponse();
    }

    /**
     * @param string      $message
     * @param string|null $userId
     * @param string|null $userName
     * @param string|null $userMention
     *
     * @return array
     */
    protected function buildDummyData($message, $userId = null, $userName = null, $userMention = null)
    {
        return [
            'item' => [
                'message' => [
                    'from' => [
                        'id' => $userId ?: '1337',
                        'name' => $userName ?: 'Andi Fined',
                        'mention_name' => $userMention ?: 'andifined',
                    ],
                    'message' => $message,
                ],
                'room' => [
                    'id' => '7331',
                    'name' => 'The Lounge',
                ],
            ],
            'oauth_client_id' => '__oauthId__',
        ];
    }

    /**
     * @param string|null $yml
     */
    protected function createTestConfig($yml = null)
    {
        if (!$yml) {
            $yml = '';

            if ($this->getPackageClass()) {
                $yml .= <<<YML
packages:
  - Venyii\HipChatCommander\Package\HelloWorld
  - Venyii\HipChatCommander\Test\Package\Dummy1

YML;
            }

            if ($this->getPackageClass() !== 'Venyii\HipChatCommander\Package\HelloWorld') {
                $yml .= <<<YML
  - {$this->getPackageClass()}
YML;
            }

            $yml .= <<<YML

rooms:
  - id: 7331
    packages:
      - name: helloWorld
      - name: dummy1
      - name: {$this->getPackageName()}
YML;
        }

        $rootDir = vfsStream::setup();
        $config = vfsStream::newFile('config.yml');
        $config->setContent($yml);
        $rootDir->addChild($config);

        $configFile = $config->url();

        $this->app['hc.config'] = $this->app->share(function () use ($configFile) {
            return Config::loadYaml(file_get_contents($configFile));
        });
    }

    /**
     * @return \GuzzleHttp\Client|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getHttpClientMock()
    {
        return $this->app['hc.http_client'];
    }

    private function clearTestingCache()
    {
        (new Filesystem())->remove($this->app['hc.cache_dir']);
    }
}
