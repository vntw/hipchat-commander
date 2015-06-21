<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Package;

class Locator
{
    /**
     * @var string[]
     */
    private $packageClasses;

    /**
     * @var AbstractPackage[]
     */
    private $packages;

    /**
     * @param array $packageClasses
     */
    public function __construct(array $packageClasses)
    {
        $this->packageClasses = $packageClasses;

        $this->loadPackages();
    }

    /**
     * @param string $name
     *
     * @return null|AbstractPackage
     */
    public function getPackage($name)
    {
        foreach ($this->packages as $package) {
            if ($package->getName() === $name || in_array($name, $package->getAliases(), true)) {
                return $package;
            }
        }

        return null;
    }

    /**
     * @return AbstractPackage[]
     */
    public function getPackages()
    {
        return $this->packages;
    }

    private function loadPackages()
    {
        $this->packages = [];

        foreach ($this->packageClasses as $packageClass) {
            if (!class_exists($packageClass)) {
                throw new \InvalidArgumentException('Package not found: '.$packageClass);
            }

            $package = new $packageClass();

            if (!$package instanceof AbstractPackage) {
                throw new \InvalidArgumentException('The package does not implement the HandlerInterface');
            }

            $package->configure();

            $this->packages[$package->getName()] = $package;
        }
    }
}
