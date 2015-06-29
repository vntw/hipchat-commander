<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Config;

use Assert\Assertion;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class Config
{
    private $options;
    private $enabledPackages;
    private $rooms;
    private $filesystem;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
        $this->filesystem = new Filesystem();

        $this->validate();

        $this->parseEnabledPackages();
        $this->parseRooms();
    }

    /**
     * @return string[]
     */
    public function getEnabledPackages()
    {
        return $this->enabledPackages;
    }

    /**
     * @return Room[]
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * @param int $id
     *
     * @return Room|null
     */
    public function getRoomById($id)
    {
        return isset($this->rooms[$id]) ? $this->rooms[$id] : null;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (strstr($key, '.') !== false) {
            $tmp = $this->options;

            foreach (explode('.', $key) as $k) {
                if (isset($tmp[$k])) {
                    $tmp = $tmp[$k];
                } else {
                    return $default;
                }
            }

            return $tmp;
        }

        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    private function validate()
    {
        Assertion::keyExists($this->options, 'packages');
        Assertion::isArray($this->options['packages']);

        Assertion::keyExists($this->options, 'rooms');
        Assertion::isArray($this->options['rooms']);
    }

    private function parseEnabledPackages()
    {
        $this->enabledPackages = [];

        foreach ($this->options['packages'] as $package) {
            if (in_array($package, $this->enabledPackages, true)) {
                throw new \InvalidArgumentException(sprintf('Looks like the package "%s" is duplicated in your enabled packages config.', $package));
            }

            $this->enabledPackages[] = $package;
        }
    }

    private function parseRooms()
    {
        $this->rooms = [];

        foreach ($this->options['rooms'] as $room) {
            if (empty($room['packages'])) {
                continue;
            }

            $this->rooms[$room['id']] = new Room($room['id'], $this->parseRoomPackages($room['packages']));
        }
    }

    /**
     * @param array $packages
     *
     * @return array
     *
     * @throws \Exception
     */
    private function parseRoomPackages(array $packages)
    {
        $pkgs = [];

        foreach ($packages as $package) {
            if (isset($package['default'])) {
                if (!isset($this->options['defaults'][$package['name']]) || !isset($this->options['defaults'][$package['name']][$package['default']])) {
                    throw new \Exception('Missing default configurations!');
                }

                // recursive merge not supported (e.g. restrictions)
                $package = array_merge($package, $this->options['defaults'][$package['name']][$package['default']]);
            }

            $pkgs[$package['name']] = new Package(
                $package['name'],
                isset($package['cache_ns']) ? $package['cache_ns'] : null,
                isset($package['restrict']) ? $this->parsePackageRestrictions($package['restrict']) : [],
                isset($package['options']) ? $package['options'] : []
            );
        }

        return $pkgs;
    }

    /**
     * @param array $restrict
     *
     * @return array
     */
    private function parsePackageRestrictions(array $restrict)
    {
        $restrictions = [];

        if (isset($restrict['user'])) {
            $restrictions['__global__'] = $restrict['user'];
        }

        if (isset($restrict['cmd'])) {
            foreach ($restrict['cmd'] as $cmd) {
                $restrictions[$cmd['name']] = $cmd['user'];
            }
        }

        return $restrictions;
    }

    /**
     * @param string $yml
     *
     * @return Config
     */
    public static function loadYaml($yml)
    {
        return new self(Yaml::parse($yml));
    }
}
