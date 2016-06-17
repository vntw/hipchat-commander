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

use Venyii\HipChatCommander\Test\WebTestCase;

class DescriptorTest extends WebTestCase
{
    public function testDescriptorFileAction()
    {
        $this->createTestConfig();

        $client = $this->createClient();
        $client->request('GET', '/package.json');
        $response = $client->getResponse();

        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertSame(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertSame(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayHasKey('name', $json);
        $this->assertSame('HC Commander', $json['name']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageClass()
    {
        return 'Venyii\HipChatCommander\Package\HelloWorld';
    }
}
