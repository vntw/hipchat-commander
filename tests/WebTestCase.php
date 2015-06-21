<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test;

use Doctrine\Common\Cache\FilesystemCache;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use org\bovigo\vfs\vfsStream;
use Silex\WebTestCase as BaseWebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Venyii\HipChatCommander\Config\Config;

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
        return;
    }

    /**
     * @return string|null
     */
    protected function getPackageClass()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../src/bootstrap.php';
        $app['debug'] = true;
        $app['exception_handler']->disable();

        $app['logger'] = new Logger('TestLogger', [new TestHandler()]);

        $app['hc.cache_dir'] = $app['hc.cache_dir'].'/tests';

        if ($pkgName = $this->getPackageName()) {
            $this->cache = $app['hc.pkg_cache']($pkgName);
        }

        return $app;
    }

    public function setUp()
    {
        parent::setUp();

        $this->clearTestingCache();
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
     * @param array  $data
     * @param string $uri
     *
     * @return Response
     */
    protected function request(array $data = array(), $uri = '/bot')
    {
        $client = $this->createClient();
        $client->request('POST', $uri, [], [], [], json_encode($data));

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

    protected function createTestConfig($yml = null)
    {
        if (!$yml) {
            $yml = '';

            if ($this->getPackageClass()) {
                $yml .= <<<YML
packages:
  - Venyii\HipChatCommander\Package\HelloWorld
  - Venyii\HipChatCommander\Test\Package\Dummy1
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

    private function clearTestingCache()
    {
        (new Filesystem())->remove($this->app['hc.cache_dir']);
    }
}
