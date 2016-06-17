# HipChat Commander

[![Build Status](https://travis-ci.org/venyii/hipchat-commander.svg?branch=master)](https://travis-ci.org/venyii/hipchat-commander)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/venyii/hipchat-commander/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/venyii/hipchat-commander/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/venyii/hipchat-commander/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/venyii/hipchat-commander/?branch=master)

This PHP bot application allows you to build custom packages (which include commands) that will interact with
your [HipChat][1] users.

## Config Options
Check the ```config/config.yml.dist``` file as an example.

* ```bot_name``` _(optional, string, default: "HC Commander")_ The name of the bot that will write messages in the chat.
* ```install``` _(optional, object)_
    * ```allow_room``` _(bool, default: false)_ Allow this application to be installed in a room
    * ```allow_global``` _(bool, default: true)_ Allow this application to be installed globally
    * ```use_webhook_pattern``` _(bool, default: true)_ Define whether every HipChat message is sent to this application.
If enabled, only messages that match a package name or alias from your configuration will be sent to the application. That
means, that if you add, remove or change a package (or aliases), you have to reinstall the application as a HipChat integration.
* ```packages``` _(array)_ An array of packages (namespace of the ```Package.php``` file) that you´d like to use
in your rooms.
* ```rooms``` _(required, array)_
    * ```id``` _(integer)_ The HipChat id of the room.
    * ```packages``` _(array)_
        * ```name``` _(required, string)_ The name of the packages as defined in the package´s ```configure``` method.
        * ```cache_ns``` _(optional, string)_ Define a custom cache key to be able to share data between multiple rooms.
Otherwise the cache will always be unique for each room.
        * ```options``` _(optional, object)_ Define custom package options that will be available in the package itself.
        * ```restrict``` _(optional, object)_
            * ```cmd``` _(required, array)_
                * ```name``` _(required, string)_ The name of the command that should be restricted.
                * ```user``` _(required, array)_ An array of user id´s that are permitted to call this command.
        * ```default``` _(optional, string)_ The name of the default configuration which should be loaded. It is not possible
to both load and override options at the moment.
* ```defaults``` _(optional, array)_ Define a package configuration once and use it multiple times without duplicating
options.
    * ```{packageName}```
        * ```{defaultConfigurationName}``` The name of the default configuration.
            * _~Package configurations.~_

## Installation
### Application
* Upload everything
* Make sure the ```logs``` and ```cache``` directories are writable.
* Create a ```config/config.yml``` (or copy the existing ```.dist``` file) and configure everything to your needs.
* Point your virtual host to the ```web/``` directory

### Bot
* Add a new (global) integration to your HipChat account and use the following URL: ```<domain.tld>/package.json```

## PHPCS
`./vendor/bin/php-cs-fixer fix --config-file .php_cs`

## ToDo
* Separate packages

[1]: https://www.hipchat.com
