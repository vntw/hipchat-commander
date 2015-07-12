<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$vendorFound = false;
$vendorDirs = [__DIR__.'/../vendor/autoload.php', __DIR__.'/../../../autoload.php'];

foreach ($vendorDirs as $vendorDir) {
    if (file_exists($vendorDir)) {
        include $vendorDir;
        $vendorFound = true;
    }
}

if (!$vendorFound) {
    throw new \RunTimeException('Cannot find an autoload.php file, have you executed composer install command?');
}

use Doctrine\Common\Cache\FilesystemCache;
use GuzzleHttp\Client as GuzzleClient;
use Silex\Provider\MonologServiceProvider;
use Venyii\HipChatCommander\Api;
use Venyii\HipChatCommander\Config\Config;
use Venyii\HipChatCommander\Package;

date_default_timezone_set('UTC');

$app = new Silex\Application();
$app['debug'] = true;

$app['hc.root_dir'] = realpath(__DIR__.'/..');
$app['hc.src_dir'] = $app['hc.root_dir'].'/src';
$app['hc.cache_dir'] = $app['hc.root_dir'].'/cache';
$app['hc.pkg_dir'] = $app['hc.root_dir'].'/pkg';

$app['hc.config'] = $app->share(function () use ($app) {
    $configFile = $app['hc.root_dir'].'/config/config.yml';

    if (!file_exists($configFile)) {
        throw new Exception('Missing config file!');
    }

    return Config::loadYaml(file_get_contents($configFile));
});

$app['hc.method_builder'] = $app->share(function () {
    return new Package\MethodGenerator();
});

$app['hc.package_loader'] = $app->share(function () use ($app) {
    return new Package\Loader($app['hc.config']->getEnabledPackages());
});

$app['hc.cache_factory'] = $app->protect(function ($namespace) use ($app) {
    return new FilesystemCache($app['hc.cache_dir'].'/'.$namespace);
});

$app['hc.cache'] = $app->share(function () use ($app) {
    return $app['hc.cache_factory']('__core__');
});

$app['hc.pkg_cache'] = $app->protect(function ($ns, $package = null) use ($app) {
    $pkgDir = $package ? '/'.$package : null;

    return $app['hc.cache_factory']($ns.$pkgDir);
});

$app['hc.api_registry'] = $app->share(function () use ($app) {
    return new Api\Client\Registry($app['hc.cache']);
});

$app['hc.api_client'] = $app->protect(function ($clientId) use ($app) {
    return new Api\Client($clientId, $app['hc.config'], $app['hc.api_registry'], $app['hc.http_client'], $app['logger']);
});

$app['hc.api_request_validator'] = $app->share(function () use ($app) {
    return new Api\Request\Validator($app['hc.api_registry']);
});

$app['hc.http_client'] = $app->share(function () use ($app) {
    return new GuzzleClient();
});

$app->register(new MonologServiceProvider(), array(
    'monolog.name' => 'HipChatCommander',
    'monolog.logfile' => $app['hc.root_dir'].'/logs/'.($app['debug'] ? 'dev' : 'prod').'.log',
));

require __DIR__.'/controller.php';

return $app;
