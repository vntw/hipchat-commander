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

use Venyii\HipChatCommander\Api;

class Validator
{
    private $registry;

    /**
     * @param Api\Client\Registry $registry
     */
    public function __construct(Api\Client\Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param Api\Request $request
     *
     * @throws \Exception
     */
    public function validate(Api\Request $request)
    {
        if (!$this->registry->isInstalled($request->getClientId())) {
            throw new \Exception('Unknown client: '.$request->getClientId());
        }
    }
}
















