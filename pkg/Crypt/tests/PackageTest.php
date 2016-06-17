<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Test\Package\Crypt;

use Venyii\HipChatCommander\Api\Response;
use Venyii\HipChatCommander\Test\WebTestCase;

class PackageTest extends WebTestCase
{
    private static $plainData = 'encrypt_it';

    /**
     * @return array
     */
    public static function dataProviderCrypts()
    {
        return [
            'encryptMd5' => [
                'encrypt',
                'md5',
                self::$plainData,
                md5(self::$plainData),
            ],
            'encryptSha256' => [
                'encrypt',
                'sha256',
                self::$plainData,
                hash('sha256', self::$plainData),
            ],
            'encryptSha512' => [
                'encrypt',
                'sha512',
                self::$plainData,
                hash('sha512', self::$plainData),
            ],
            'encryptBase64' => [
                'encrypt',
                'base64',
                self::$plainData,
                base64_encode(self::$plainData),
            ],
            'decryptBase64' => [
                'decrypt',
                'base64',
                base64_encode(self::$plainData),
                self::$plainData,
            ],
        ];
    }

    /**
     * @param string $cmd
     * @param string $type
     * @param string $tbdCryptString
     * @param string $resultCryptString
     *
     * @dataProvider dataProviderCrypts
     */
    public function testEncrypt($cmd, $type, $tbdCryptString, $resultCryptString)
    {
        $this->createTestConfig();

        $response = $this->request($this->buildDummyData(sprintf('/%s %s %s', $cmd, $type, $tbdCryptString)));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertSame(sprintf('%s: %s', strtoupper($type), $resultCryptString), $responseJson['message']);
        $this->assertEquals(Response::FORMAT_TEXT, $responseJson['message_format']);
    }

    public function testDecryptUnsupportedCrypt()
    {
        $this->createTestConfig();

        $response = $this->request($this->buildDummyData('/decrypt md5 yolo'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertSame('(failed) Operation not supported!', $responseJson['message']);
        $this->assertEquals(Response::FORMAT_TEXT, $responseJson['message_format']);
    }

    public function testDecryptCorruptData()
    {
        $this->createTestConfig();

        $response = $this->request($this->buildDummyData('/decrypt base64 yo+lo'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $responseJson = json_decode($response->getContent(), true);

        $this->assertSame('(failed) Could not decrypt the base64 data.', $responseJson['message']);
        $this->assertEquals(Response::FORMAT_TEXT, $responseJson['message_format']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageName()
    {
        return 'encrypt';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageClass()
    {
        return 'Venyii\HipChatCommander\Package\Crypt';
    }
}
