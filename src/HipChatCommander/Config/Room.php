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

class Room
{
    private $id;
    private $packages;

    /**
     * @param int   $id
     * @param array $packages
     */
    public function __construct($id, array $packages)
    {
        $this->id = (int) $id;
        $this->packages = $packages;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $packageName
     *
     * @return Package|null
     */
    public function getPackageByName($packageName)
    {
        return isset($this->packages[$packageName]) ? $this->packages[$packageName] : null;
    }
}
