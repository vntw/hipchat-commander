<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Package\Crypt;

use Venyii\HipChatCommander\Package\AbstractPackage;
use Venyii\HipChatCommander\Api\Response;

class Package extends AbstractPackage
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('encrypt')
            ->setAliases(['decrypt'])
            ->setDescription('Provides basic crypt functions')
            ->addCommand('help')
            ->addCommand('md5', 'Encrypt a string to MD5', [], false, 'crypt')
            ->addCommand('sha256', 'Encrypt a string to SHA265', [], false, 'crypt')
            ->addCommand('sha512', 'Encrypt a string to SHA512', [], false, 'crypt')
            ->addCommand('base64', 'Encrypt and decrypt a string from and to Base64', [], false, 'crypt')
        ;
    }

    /**
     * @return Response
     */
    public function cryptCmd()
    {
        $command = $this->getRequest()->getPackage();
        $args = $this->getRequest()->getArgs();

        $type = strtolower(array_shift($args));
        $method = $command.ucfirst($type);

        if (!method_exists($this, $method)) {
            return Response::createError('Operation not supported!');
        }

        try {
            $result = $this->{$method}(implode('', $args));
        } catch (Exception\CorruptDataException $e) {
            return Response::createError(sprintf('Could not %s the %s data.', $command, $type));
        }

        return Response::create(sprintf('%s: %s', strtoupper($type), $result));
    }

    private function encryptMd5($string)
    {
        return md5($string);
    }

    private function encryptSha256($string)
    {
        return hash('sha256', $string);
    }

    private function encryptSha512($string)
    {
        return hash('sha512', $string);
    }

    private function encryptBase64($string)
    {
        return base64_encode($string);
    }

    private function decryptBase64($string)
    {
        $decoded = base64_decode($string);

        if ($decoded === false || !mb_check_encoding($decoded, 'UTF-8')) {
            throw new Exception\CorruptDataException();
        }

        return (string) $decoded;
    }
}
