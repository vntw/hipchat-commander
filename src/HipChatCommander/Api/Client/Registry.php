<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Api\Client;

use Doctrine\Common\Cache\FilesystemCache;

class Registry
{
    const INSTALLS_KEY = 'installs';

    private $cache;

    /**
     * @param FilesystemCache $cache
     */
    public function __construct(FilesystemCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param string   $clientId
     * @param string   $clientSecret
     * @param int      $groupId
     * @param int|null $roomId
     *
     * @throws \Exception
     */
    public function install($clientId, $clientSecret, $groupId, $roomId = null)
    {
        if ($this->existsInstallForCombination($groupId, $roomId)) {
            throw new \Exception('A client is already installed for this group/room combination');
        }

        $installs = $this->getInstalls();
        $installs[$clientId] = [
            'date' => new \DateTime(),
            'groupId' => $groupId,
            'roomId' => $roomId,
            'credentials' => [
                'oauthId' => $clientId,
                'oauthSecret' => $clientSecret,
            ],
        ];

        $this->cache->save(self::INSTALLS_KEY, $installs);
    }

    /**
     * @param string $clientId
     *
     * @return bool
     */
    public function uninstall($clientId)
    {
        $installs = $this->getInstalls();

        if (isset($installs[$clientId])) {
            unset($installs[$clientId]);

            $this->cache->save(self::INSTALLS_KEY, $installs);

            return true;
        }

        return false;
    }

    /**
     * @param string $clientId
     *
     * @return array|null
     */
    public function getClient($clientId)
    {
        return isset($this->getInstalls()[$clientId]) ? $this->getInstalls()[$clientId] : null;
    }

    /**
     * @param string $clientId
     *
     * @return bool
     */
    public function isInstalled($clientId)
    {
        return isset($this->getInstalls()[$clientId]);
    }

    /**
     * @param string    $clientId
     * @param string    $clientSecret
     * @param string    $authToken
     * @param \DateTime $expires
     */
    public function updateCreds($clientId, $clientSecret, $authToken, \DateTime $expires)
    {
        $installs = $this->getInstalls();
        $installs[$clientId]['credentials'] = [
            'oauthId' => $clientId,
            'oauthSecret' => $clientSecret,
            'authToken' => $authToken,
            'expires' => $expires,
        ];

        $this->cache->save(self::INSTALLS_KEY, $installs);
    }

    /**
     * @param string $clientId
     *
     * @return array|null
     */
    public function getClientCredentials($clientId)
    {
        if (!$this->isInstalled($clientId)) {
            return null;
        }

        return $this->getInstalls()[$clientId]['credentials'];
    }

    /**
     * @return array
     */
    public function getInstalls()
    {
        $installs = $this->cache->fetch(self::INSTALLS_KEY);

        return is_array($installs) ? $installs : [];
    }

    /**
     * @param int      $groupId
     * @param int|null $roomId
     *
     * @return bool
     */
    private function existsInstallForCombination($groupId, $roomId = null)
    {
        foreach ($this->getInstalls() as $install) {
            if ($install['groupId'] === $groupId && $install['roomId'] === $roomId) {
                return true;
            }
        }

        return false;
    }
}
