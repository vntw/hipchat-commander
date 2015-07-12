<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test\Package\HelloWorld;

use Venyii\HipChatCommander\Api\Response;
use Venyii\HipChatCommander\WebTestCase;

class PackageTest extends WebTestCase
{
    public function testHelloWorld()
    {
        $yml = <<<YML
install:
  allow_room: false
  allow_global: true

packages:
  - Venyii\HipChatCommander\Package\HelloWorld

rooms:
  - id: 7331
    packages:
      - name: helloWorld
        default: lounge-default

defaults:
  helloWorld:
    lounge-default:
      cache_ns: some_cache
YML;

        $this->createTestConfig($yml);

        $response = $this->request($this->buildDummyData('/helloWorld'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertContains(') Hello Andi Fined and World! (', $responseJson['message']);
        $this->assertEquals(Response::FORMAT_TEXT, $responseJson['message_format']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageName()
    {
        return 'helloWorld';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageClass()
    {
        return 'Venyii\HipChatCommander\Package\HelloWorld';
    }
}
