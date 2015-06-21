<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Api\Request;

class User
{
    private $id;
    private $name;
    private $mentionName;

    /**
     * @param int    $id
     * @param string $name
     * @param string $mentionName
     */
    public function __construct($id, $name, $mentionName)
    {
        $this->id = (int) $id;
        $this->name = $name;
        $this->mentionName = $mentionName;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMentionName()
    {
        return $this->mentionName;
    }
}
