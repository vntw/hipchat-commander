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

class Loader
{
    /**
     * @var string[]
     */
    private $packageNamespaces;

    /**
     * @var AbstractPackage[]
     */
    private $packages;

    /**
     * @param array $packageNamespaces
     */
    public function __construct(array $packageNamespaces)
    {
        $this->packageNamespaces = $packageNamespaces;

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

        foreach ($this->packageNamespaces as $packageNamespace) {
            $packageClass = $packageNamespace.'\\Package';

            if (!class_exists($packageClass)) {
                throw new \InvalidArgumentException(sprintf('The package "%s" could not be found', $packageClass));
            }

            $package = new $packageClass();

            if (!$package instanceof AbstractPackage) {
                throw new \InvalidArgumentException(sprintf('The package "%s" must extend the %s class', $packageClass, AbstractPackage::class));
            }

            $package->configure();

            $this->packages[$package->getName()] = $package;
        }
    }
}
