<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Descriptor;

use Venyii\HipChatCommander\Config\Config;
use Venyii\HipChatCommander\Package;

class Builder
{
    const DEFAULT_BOT_NAME = 'HC Commander';

    private $appUrl;
    private $config;
    private $packages;

    /**
     * @param string $appUrl
     * @param Config $config
     * @param array  $packages
     */
    public function __construct($appUrl, Config $config, array $packages)
    {
        $this->appUrl = $appUrl;
        $this->config = $config;
        $this->packages = $packages;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function build()
    {
        if (empty($this->packages)) {
            throw new \Exception('No packages were found.');
        }

        $descriptor = [
            'name' => $this->config->get('bot_name', static::DEFAULT_BOT_NAME),
            'description' => 'A PHP HipChat application',
            'key' => 'de.cersei.hccommander',
            'links' => [
                'self' => $this->appUrl.'/package.json',
            ],
            'capabilities' => [
                'installable' => [
                    'allowRoom' => (bool) $this->config->get('install.allow_room', false),
                    'allowGlobal' => (bool) $this->config->get('install.allow_global', true),
                    'callbackUrl' => $this->appUrl.'/cb/install',
                ],
                'hipchatApiConsumer' => [
                    'scopes' => ['send_notification', 'view_group', 'admin_room'],
                ],
                'webhook' => [
                    [
                        'url' => $this->appUrl.'/bot/addon',
                        'event' => 'room_message',
                    ],
                ],
            ],
        ];

        $this->addWebhookPattern($descriptor);

        return $descriptor;
    }

    /**
     * @param array $descriptor
     */
    private function addWebhookPattern(array &$descriptor)
    {
        if (false === $this->config->get('install.use_webhook_pattern', true)) {
            return;
        }

        $cmds = [];

        foreach ($this->packages as $package) {
            /* @var Package\AbstractPackage $package */
            $cmds[] = preg_quote($package->getName());
            $aliases = $package->getAliases();

            if (!empty($aliases)) {
                $cmds[] = implode('|', array_map('preg_quote', $aliases));
            }
        }

        $descriptor['capabilities']['webhook'][0]['pattern'] = sprintf('^\/(%s)', implode('|', $cmds));
    }
}
