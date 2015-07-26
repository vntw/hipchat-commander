<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test\Package\Random;

use GuzzleHttp\Stream\Stream;
use Venyii\HipChatCommander\Api\Response;
use Venyii\HipChatCommander\WebTestCase;

class PackageTest extends WebTestCase
{
    public function testPick()
    {
        $this->createTestConfig();

        $response = $this->request($this->buildDummyData('/random pick 1337,1338,1339'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertRegExp('/^Picked: (1337|1338|1339)$/', $responseJson['message']);
        $this->assertEquals(Response::FORMAT_TEXT, $responseJson['message_format']);
    }

    public function testPickSingle()
    {
        $this->createTestConfig();

        $response = $this->request($this->buildDummyData('/random pick 1337'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertSame('Picked (to no one´s surprise): 1337', $responseJson['message']);
        $this->assertEquals(Response::FORMAT_TEXT, $responseJson['message_format']);
    }

    public function testPickInvalid()
    {
        $this->createTestConfig();

        $response = $this->request($this->buildDummyData('/random pick'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertSame('(failed) You need to specify some values to pick between. Check the help for more info.', $responseJson['message']);
        $this->assertEquals(Response::FORMAT_TEXT, $responseJson['message_format']);
    }

    public function testRandomNumber()
    {
        $this->createTestConfig();

        $response = $this->request($this->buildDummyData('/random number'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertRegExp('/^Your random number is: \d+$/', $responseJson['message']);
        $this->assertEquals(Response::FORMAT_TEXT, $responseJson['message_format']);
    }

    public function testRandomNumberWithCustomRange()
    {
        $this->createTestConfig();

        $response = $this->request($this->buildDummyData('/random number 400-4000'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertRegExp('/^Your random number is: \d+$/', $responseJson['message']);
        $this->assertEquals(Response::FORMAT_TEXT, $responseJson['message_format']);
    }

    public function testRandomNumberWithInvalidRange()
    {
        $this->createTestConfig();

        $response = $this->request($this->buildDummyData('/random number 2342-23'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertSame('(failed) The min value may not be greater than the max value.', $responseJson['message']);
        $this->assertEquals(Response::FORMAT_TEXT, $responseJson['message_format']);
    }

    public function testWiki()
    {
        $this->createTestConfig();
        $this->setupHttpClientMock();

        $response = $this->request($this->buildDummyData('/random wiki'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertSame('Test Title - https://fullurl.com', $responseJson['message']);
        $this->assertEquals(Response::FORMAT_TEXT, $responseJson['message_format']);
    }

    public function testWikiWithCustomLanguage()
    {
        $this->createTestConfig();
        $this->setupHttpClientMock();

        $response = $this->request($this->buildDummyData('/random wiki de'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertRegExp('/^(.*) - https:\/\/(.*)$/', $responseJson['message']);
        $this->assertEquals(Response::FORMAT_TEXT, $responseJson['message_format']);
    }

    public function testWikiWithInvalidLanguage()
    {
        $this->createTestConfig();

        $response = $this->request($this->buildDummyData('/random wiki yolo'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertSame('(failed) The language must be 2 characters.', $responseJson['message']);
        $this->assertEquals(Response::FORMAT_TEXT, $responseJson['message_format']);
    }

    /**
     * @param array|null $jsonData
     */
    private function setupHttpClientMock($jsonData = null)
    {
        if ($jsonData === null) {
            $jsonData = ['query' => ['pages' => [['title' => 'Test Title', 'fullurl' => 'https://fullurl.com']]]];
        }

        $response = new \GuzzleHttp\Message\Response(200, [], Stream::factory(json_encode($jsonData)));

        $this
            ->getHttpClientMock()
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($response))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageName()
    {
        return 'random';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageClass()
    {
        return 'Venyii\HipChatCommander\Package\Random';
    }
}
