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
    private $packageLocator;
    private $type;

    /**
     * @param string          $appUrl
     * @param Config          $config
     * @param Package\Locator $packageLocator
     * @param string          $type
     */
    public function __construct($appUrl, Config $config, Package\Locator $packageLocator, $type)
    {
        $this->appUrl = $appUrl;
        $this->config = $config;
        $this->packageLocator = $packageLocator;
        $this->type = $type = 'global';
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function build()
    {
        $packages = $this->packageLocator->getPackages();

        if (empty($packages)) {
            throw new \Exception('No packages were found.');
        }

        if ($this->type === 'room') {
            $scopes = ['send_notification', 'view_group'];
        } else {
            $scopes = ['send_notification', 'view_group', 'admin_room'];
        }

        $descriptor = [
            'name' => $this->config->get('bot_name') ?: static::DEFAULT_BOT_NAME,
            'description' => 'A PHP HipChat application',
            'key' => 'de.cersei.hccommander',
            'links' => [
                'self' => $this->appUrl.'/package.json',
            ],
            'capabilities' => [
                'installable' => [
                    'allowRoom' => (bool) $this->config->get('install.allow_room'),
                    'allowGlobal' => (bool) $this->config->get('install.allow_global'),
                    'callbackUrl' => $this->appUrl.'/cb/install',
                ],
                'hipchatApiConsumer' => [
                    'scopes' => $scopes,
                ],
                'webhook' => [
                    [
                        'url' => $this->appUrl.'/bot/addon',
                        'event' => 'room_message',
                        'pattern' => $this->buildPattern($packages),
                    ],
                ],
            ],
        ];

        return $descriptor;
    }

    /**
     * @param Package\AbstractPackage[] $packages
     *
     * @return string
     */
    private function buildPattern(array $packages)
    {
        $cmds = [];

        foreach ($packages as $package) {
            /* @var Package\AbstractPackage $package */
            $cmds[] = preg_quote($package->getName());
            $aliases = $package->getAliases();

            if (!empty($aliases)) {
                $cmds[] = implode('|', array_map('preg_quote', $aliases));
            }
        }

        return sprintf('^\/(%s)', implode('|', $cmds));
    }
}
