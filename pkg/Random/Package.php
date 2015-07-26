<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Package\Random;

use Venyii\HipChatCommander\Package\AbstractPackage;
use Venyii\HipChatCommander\Api\Response;

class Package extends AbstractPackage
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('random')
            ->setDescription('Randomize everything!')
            ->addCommand('help')
            ->addCommand('pick', 'Pick a random item from a list separated by commas (","). Usage: /random pick 4,8,15,16,23,42')
            ->addCommand('number', 'Generate a random number. You can define a range (e.g. "1-100" = default) as an argument. Usage: /random number <range>', ['num'], true)
            ->addCommand('wiki', 'Randomly pick a wiki page. Optionally pass a 2-digit language (en = default, de, fr, ...). Usage: /random wiki <language>')
        ;
    }

    /**
     * @return Response
     */
    public function pickCmd()
    {
        $pickString = $this->getRequest()->getArg(1);

        if ($pickString === null) {
            return Response::createError('You need to specify some values to pick between. Check the help for more info.');
        }

        $picks = array_unique(array_map('trim', explode(',', $pickString)));
        shuffle($picks);

        if (count($picks) === 1) {
            return Response::create(sprintf('Picked (to no one´s surprise): %s', $picks[0]));
        }

        return Response::create(sprintf('Picked: %s', $picks[0]), null, Response::COLOR_RANDOM);
    }

    /**
     * @return Response
     */
    public function numberCmd()
    {
        $rangeString = $this->getRequest()->getArg(1) ?: $this->getOption('default_range', '1-10000');
        $range = array_map('intval', explode('-', $rangeString));

        if (count($range) !== 2) {
            return Response::createError('You need to provide a valid range of two numbers, e.g. 1-100.');
        }

        if ($range[0] > $range[1]) {
            return Response::createError('The min value may not be greater than the max value.');
        }

        return Response::create(sprintf('Your random number is: %d', mt_rand($range[0], $range[1])), null, Response::COLOR_RANDOM);
    }

    /**
     * @return Response
     */
    public function wikiCmd()
    {
        $language = $this->getRequest()->getArg(1) ?: $this->getOption('default_wiki_language', 'en');

        if (strlen($language) !== 2) {
            return Response::createError('The language must be 2 characters.');
        }

        $url = sprintf('https://%s.wikipedia.org/w/api.php?action=query&generator=random&grnnamespace=0&prop=info&inprop=url&formatversion=2&format=json', $language);

        $response = $this->getHttpClient()->get($url);

        if ($response->getStatusCode() !== 200) {
            return Response::createError('There was an error while talking to Wikipedia. Please try again.');
        }

        $json = $response->json();
        $title = $json['query']['pages'][0]['title'];
        $fullUrl = $json['query']['pages'][0]['fullurl'];

        return Response::create(sprintf('%s - %s', $title, $fullUrl), null, Response::COLOR_RANDOM);
    }
}
