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

use Silex\Application;

class BaseTest extends WebTestCase
{
    public function testEmptyRequestReturnsError()
    {
        $this->setExpectedException('Exception', 'Invalid request');

        $client = $this->createClient();
        $client->request('POST', '/bot/addon');
    }

    public function testDescriptorFile()
    {
        $yml = <<<YML
bot_name: ACME Maid

install:
  allow_room: false
  allow_global: true

packages:
  - Venyii\HipChatCommander\Package\HelloWorld
  - Venyii\HipChatCommander\Test\Package\Dummy1

rooms:
  - id: 7331
    packages:
      - name: helloWorld
      - name: dummy1
YML;

        $this->createTestConfig($yml);

        $client = $this->createClient();
        $client->request('GET', '/package.json');
        $response = $client->getResponse();
        $jsonResponse = json_decode($response->getContent(), true);

        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals('ACME Maid', $jsonResponse['name']);
        $this->assertEquals('de.cersei.hccommander', $jsonResponse['key']);
        $this->assertEquals('^\/(helloWorld|dummy1)', $jsonResponse['capabilities']['webhook'][0]['pattern']);
    }
}
