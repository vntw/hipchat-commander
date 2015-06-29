<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
    ->exclude([
        __DIR__.'/cache',
        __DIR__.'/vendor'
    ])
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(['-empty_return', '-pre_increment'])
    ->setUsingCache(false)
    ->finder($finder)
;
