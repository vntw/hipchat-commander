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

use Doctrine\Common\Cache\Cache;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use Psr\Log\LoggerInterface;
use Venyii\HipChatCommander\Api;
use Venyii\HipChatCommander\Package\Command\HelpTable;

abstract class AbstractPackage
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $aliases = [];

    /**
     * @var string
     */
    private $description;

    /**
     * @var Command[]
     */
    private $commands = [];

    /**
     * @var bool
     */
    private $defaultCommandAdded = false;

    /**
     * @var Api\Request
     */
    private $request;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Api\Client
     */
    private $client;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param string $name
     *
     * @return $this
     */
    protected function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array $aliases
     *
     * @return $this
     */
    protected function setAliases(array $aliases)
    {
        $this->aliases = array_unique($aliases);

        return $this;
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    protected function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Command[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequest(Api\Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setApiClient(Api\Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @param HttpClientInterface $client
     *
     * @return $this
     */
    public function setHttpCLient(\GuzzleHttp\ClientInterface $client)
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return Api\Request
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Cache
     */
    protected function getCache()
    {
        return $this->cache;
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    protected function getOption($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    /**
     * Method to configure the package.
     */
    public function configure()
    {
        throw new \LogicException('The package must override this method and configure itself.');
    }

    /**
     * A generic help command.
     * 
     * @return Api\Response
     */
    public function helpCmd()
    {
        return Api\Response::create(
            (new HelpTable($this))->build(),
            Api\Response::FORMAT_HTML,
            Api\Response::COLOR_GRAY
        );
    }

    /**
     * @param Api\Response $response
     */
    public function sendRoomMsg(Api\Response $response)
    {
        $uri = sprintf('room/%d/notification', $this->request->getRoom()->getId());

        $this->client->send($uri, $response->toArray());
    }

    /**
     * @return HttpClientInterface
     */
    protected function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param string      $name
     * @param string      $description
     * @param array       $aliases
     * @param bool        $default
     * @param string|null $method
     *
     * @return $this
     */
    protected function addCommand($name, $description = null, array $aliases = [], $default = false, $method = null)
    {
        if (isset($this->commands[$name])) {
            throw new \LogicException(sprintf('A command with the name "%s" already exists.', $name));
        }

        $default = (bool) $default;

        if ($this->defaultCommandAdded && $default) {
            throw new \LogicException('A default command has already been added.');
        }

        $this->defaultCommandAdded = $default;
        $this->commands[$name] = new Command($name, $description, $aliases, $default, $method);

        return $this;
    }

    /**
     * @return Command
     */
    public function getDefaultCommand()
    {
        $defaultCommand = array_filter($this->commands, function (Command $command) {
            return $command->isDefault();
        });

        if (count($defaultCommand) !== 1) {
            return null;
        }

        return reset($defaultCommand);
    }

    /**
     * @param string $argument
     *
     * @return Command|null
     */
    public function getCommandByArgument($argument)
    {
        if ($argument === null) {
            $defaultCommand = $this->getDefaultCommand();

            if (!$defaultCommand) {
                throw new \InvalidArgumentException('No argument given and no default available');
            }

            return $defaultCommand;
        }

        $argument = strtolower($argument);

        foreach ($this->commands as $command) {
            if ($command->getName() === $argument) {
                return $command;
            }

            if (in_array($argument, $command->getAliases(), true)) {
                return $command;
            }
        }

        return null;
    }
}
