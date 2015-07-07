<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in([
        __DIR__.'/src',
        __DIR__.'/pkg',
        __DIR__.'/tests',
        __DIR__.'/web',
    ])
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(['-empty_return', '-pre_increment'])
    ->setUsingCache(false)
    ->finder($finder)
;
