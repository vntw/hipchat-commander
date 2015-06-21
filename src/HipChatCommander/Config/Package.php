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

class Package
{
    private $name;
    private $cacheNs;
    private $restrictions;
    private $options;

    /**
     * @param string      $name
     * @param string|null $cacheNs
     * @param array       $restrictions
     * @param array       $options
     */
    public function __construct($name, $cacheNs = null, array $restrictions = [], array $options = [])
    {
        $this->name = $name;
        $this->cacheNs = $cacheNs;
        $this->restrictions = $restrictions;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getCacheNs()
    {
        return $this->cacheNs;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param int         $userId
     * @param string|null $cmd
     *
     * @return bool
     */
    public function isUserPermitted($userId, $cmd = null)
    {
        if (empty($this->restrictions)) {
            return true;
        }

        if ($cmd !== null) {
            if (isset($this->restrictions[$cmd])) {
                return in_array($userId, $this->restrictions[$cmd]);
            }
        }

        if (!empty($this->restrictions['__global__']) && !in_array($userId, $this->restrictions['__global__'])) {
            return false;
        }

        return true;
    }
}
