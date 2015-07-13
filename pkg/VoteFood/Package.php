<?php

/*
 * This file is part of the HipChat Commander.
 *
 * (c) venyii
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Venyii\HipChatCommander\Package\VoteFood;

use Venyii\HipChatCommander\Package\AbstractPackage;
use Venyii\HipChatCommander\Api\Response;

class Package extends AbstractPackage
{
    const CACHE_KEY_VOTES = 'food-votes';
    const CACHE_KEY_STORES = 'food-stores';
    const CACHE_KEY_ABSTAINS = 'food-abstains';

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('voteFood')
            ->setDescription('Vote for your favourite food store and decide where to eat with your team.')
            ->setAliases(['essen'])
            ->addCommand('help')
            ->addCommand('status', 'Shows a summary of all votes.', [], true)
            ->addCommand('init', 'Initiates a new vote. This won´t delete your custom stores.')
            ->addCommand('clear', 'Initiates a new vote and removes all custom stores.')
            ->addCommand('stores', 'Displays all available stores.')
            ->addCommand('vote', 'Vote for one or more stores. Calling this multiple times will overwrite your previous vote.')
            ->addCommand('decide', 'Randomly decide where to eat.')
            ->addCommand('abstain', 'Abstain from the current vote.')
            ->addCommand('mkstore', 'Add a custom store. Usage: /voteFood mkstore <store_short> <Store>')
            ->addCommand('rmstore', 'Remove a custom store. Usage: /voteFood rmstore <store_short>')
            ->addCommand('go', 'Notify all voters that it´s time to go. The store is optional.', ['notify'])
            ->addCommand('ack', 'Accept another users vote. Usage: /voteFood ack <user>')
        ;
    }

    /**
     * @return Response
     */
    public function initCmd()
    {
        $this->getCache()->delete(self::CACHE_KEY_VOTES);
        $this->getCache()->delete(self::CACHE_KEY_ABSTAINS);

        return Response::createSuccess('Successfully initiated a new vote!');
    }

    /**
     * @return Response
     */
    public function clearCmd()
    {
        $this->getCache()->delete(self::CACHE_KEY_VOTES);
        $this->getCache()->delete(self::CACHE_KEY_ABSTAINS);
        $this->getCache()->delete(self::CACHE_KEY_STORES);

        $this->populateDefaultStores();

        return Response::createSuccess('Successfully cleared all votes and custom stores!');
    }

    /**
     * @param string|null $store
     *
     * @return Response
     */
    public function goCmd($store = null)
    {
        $stores = $this->getStores();

        if (null === $store) {
            $store = $this->getRequest()->getArg(1);

            if (!$store) {
                // use the store with the most votes if no store is specified
                $storeRanking = $this->getStoreRanking();
                $bestValues = array_values($storeRanking);
                $bestKeys = array_keys($storeRanking);

                if ($bestValues[0] > $bestValues[1]) {
                    $store = $bestKeys[0];
                }
            }
        }

        if (!isset($stores[$store])) {
            return Response::createError('Unknown store!');
        }

        $mentions = [];
        $votes = $this->getVotes();

        foreach ($votes as $user => $vote) {
            if (in_array($store, $vote, true)) {
                $mentions[] = '@'.$user;
            }
        }

        if (empty($mentions)) {
            return Response::create('Nobody wants to go there!', null, Response::COLOR_GRAY);
        }

        return Response::create(sprintf('Time To Eat @ %s! %s', $stores[$store], implode(' ', $mentions)));
    }

    /**
     * @return Response
     */
    public function decideCmd()
    {
        $userVotes = $this->getVotes();

        if (empty($userVotes)) {
            return Response::createError('What exactly do you want me to decide? Nobody even voted! (facepalm)');
        }

        $storeRanking = $this->getStoreRanking();
        $bestValues = array_values($storeRanking);
        $bestKeys = array_keys($storeRanking);

        $i = 1;
        $contestants = [$bestKeys[0]];

        while (isset($bestValues[$i]) && $bestValues[0] === $bestValues[$i]) {
            $contestants[] = $bestKeys[$i];
            $i++;
        }

        shuffle($contestants);

        return $this->goCmd($contestants[0]);
    }

    /**
     * @return Response
     */
    public function storesCmd()
    {
        $stores = $this->getStores();
        $niceStores = [];

        array_map(function ($a, $b) use (&$niceStores) {
            $niceStores[] = sprintf('%s (%s)', $a, $b);
        }, array_keys($stores), $stores);

        return Response::create('(beer) Available stores: '.implode(', ', $niceStores));
    }

    /**
     * @return Response
     */
    public function mkstoreCmd()
    {
        $storeShort = $this->getRequest()->getArg(1);
        $storeLong = $this->getRequest()->getArg(2);

        if (null === $storeShort || null === $storeLong) {
            return Response::create('(doh) Usage: mkstore name_short name_long', null, Response::COLOR_YELLOW);
        }

        if (strlen($storeShort) < 2 || strlen($storeLong) < 2) {
            return Response::createError('The store names must be at least 2 characters!');
        }

        $stores = $this->getStores();

        if (isset($stores[$storeShort]) && $stores[$storeShort] === $storeLong) {
            return Response::createError('This store already exists!');
        }

        $this->addStore($storeShort, $storeLong);

        return Response::createSuccess('Successfully added the store!');
    }

    /**
     * @return Response
     */
    public function rmstoreCmd()
    {
        $storeShort = $this->getRequest()->getArgs()[1];

        if (isset($this->getDefaultStores()[$storeShort])) {
            return Response::createError('This is a default store... so... no. (okay)');
        }

        $stores = $this->getStores();

        if (!isset($stores[$storeShort])) {
            return Response::createError('Unknown store.');
        }

        $this->removeStore($storeShort);

        return Response::createSuccess('Successfully removed the store!');
    }

    /**
     * @return Response
     */
    public function abstainCmd()
    {
        $userMention = $this->getRequest()->getUser()->getMentionName();

        $this->addAbstain($userMention);

        $votes = $this->getVotes();
        unset($votes[$userMention]);

        $this->saveVotes($votes);

        return $this->statusCmd();
    }

    /**
     * @return Response
     */
    public function voteCmd()
    {
        $stores = $this->getStores();

        if (empty($stores)) {
            // init the vote automatically
            $this->sendRoomMsg($this->initCmd());
        }

        $votes = $this->getRequest()->getArgs();

        // remove cmd
        array_shift($votes);

        $userMention = $this->getRequest()->getUser()->getMentionName();
        $userVotes = $this->getVotes() ?: [];

        if (empty($votes)) {
            if (isset($userVotes[$userMention])) {
                unset($userVotes[$userMention]);

                $this->saveVotes($userVotes);
                $this->sendRoomMsg(Response::createSuccess('Successfully removed your vote!'));
            }

            $this->addAbstain($userMention);

            return !empty($userVotes) ? $this->statusCmd() : null;
        }

        $defaultStores = $this->getStores();

        foreach ($votes as $key => $vote) {
            if (!isset($defaultStores[$vote])) {
                $closestMatch = $this->autoCorrectStore($vote);

                if (null === $closestMatch) {
                    return Response::createError(sprintf('Invalid store: `%s` - Check the available stores using the `stores` command!', $vote));
                }

                // replace the unknown store with the closest match
                $votes[$key] = $closestMatch;
            }
        }

        $userVotes[$userMention] = $votes;

        $this->saveVotes($userVotes);
        $this->removeAbstain($userMention);

        return $this->statusCmd();
    }

    /**
     * @return Response
     */
    public function ackCmd()
    {
        $user = $this->getRequest()->getArg(1);
        $userVotes = $this->getVotes();

        if ($user === null) {
            if (empty($userVotes)) {
                return Response::createError('Nobody voted yet, looks like you`ll have to choose on your own this time!');
            }

            $user = end(array_keys($userVotes));
        }

        if (!isset($userVotes[$user])) {
            return Response::createError(sprintf('Could not find any vote for user `%s`', $user));
        }

        $userMention = $this->getRequest()->getUser()->getMentionName();
        $userVotes[$userMention] = $userVotes[$user];

        $this->saveVotes($userVotes);
        $this->removeAbstain($userMention);

        return $this->statusCmd();
    }

    /**
     * @return Response
     */
    public function statusCmd()
    {
        $stores = $this->getStores();
        $userVotes = $this->getVotes();

        if (empty($userVotes)) {
            return Response::create('Nobody voted yet!');
        }

        $matrix = [[null]];

        foreach ($stores as $storeKey => $store) {
            $matrix[] = [$storeKey => $store];
        }

        $i = 0;

        foreach ($userVotes as $user => $votes) {
            $matrix[0][$i + 1] = $user;
            $uvi = 1;

            foreach ($stores as $storeKey => $store) {
                $matrix[$uvi][$i + 1] = in_array($storeKey, $votes, true);
                $uvi++;
            }

            $i++;
        }

        return Response::create($this->renderMatrix($matrix), Response::FORMAT_HTML);
    }

    /**
     * @param array $matrix
     *
     * @return string
     */
    private function renderMatrix(array $matrix)
    {
        return ResultRenderer::render($matrix, $this->getStoreRanking(), $this->getAbstains());
    }

    /**
     * @param string $short
     * @param string $store
     */
    private function addStore($short, $store)
    {
        $stores = $this->getCache()->fetch(self::CACHE_KEY_STORES) ?: [];
        $stores[$short] = $store;

        $this->getCache()->save(self::CACHE_KEY_STORES, $stores);
    }

    /**
     * @param string $short
     */
    private function removeStore($short)
    {
        $stores = $this->getCache()->fetch(self::CACHE_KEY_STORES) ?: [];

        unset($stores[$short]);

        $this->getCache()->save(self::CACHE_KEY_STORES, $stores);
    }

    /**
     * @return array|null
     */
    private function getAbstains()
    {
        $abstains = $this->getCache()->fetch(self::CACHE_KEY_ABSTAINS);

        return $abstains ? $abstains : [];
    }

    /**
     * @return array|null
     */
    private function getVotes()
    {
        $votes = $this->getCache()->fetch(self::CACHE_KEY_VOTES);

        return $votes ? $votes : [];
    }

    /**
     * @param array $votes
     */
    private function saveVotes(array $votes)
    {
        $this->getCache()->save(self::CACHE_KEY_VOTES, $votes);
    }

    /**
     * @return array|null
     */
    private function getStores()
    {
        $stores = $this->getCache()->fetch(self::CACHE_KEY_STORES);

        if (empty($stores)) {
            $this->populateDefaultStores();
            $stores = $this->getCache()->fetch(self::CACHE_KEY_STORES);
        }

        return $stores ? $stores : [];
    }

    private function populateDefaultStores()
    {
        foreach ($this->getDefaultStores() as $key => $store) {
            $this->addStore($key, $store);
        }
    }

    /**
     * @return array
     */
    private function getDefaultStores()
    {
        return $this->getOption('default_stores', []);
    }

    /**
     * @param string $store
     *
     * @return string|null
     */
    private function autoCorrectStore($store)
    {
        $closest = null;

        foreach (array_keys($this->getStores()) as $word) {
            $lev = levenshtein($store, $word);

            if ($lev <= strlen($store) / 3 || false !== strpos($word, $store)) {
                $closest = $word;
            }
        }

        return $closest;
    }

    /**
     * @return array
     */
    private function getStoreRanking()
    {
        $best = [];
        $stores = $this->getStores();
        $userVotes = $this->getVotes();

        if (empty($userVotes)) {
            return $best;
        }

        foreach ($userVotes as $user => $votes) {
            foreach ($stores as $storeKey => $store) {
                if (!isset($best[$storeKey])) {
                    $best[$storeKey] = 0;
                }
                $best[$storeKey] += in_array($storeKey, $votes, true) ? 1 : 0;
            }
        }

        arsort($best);

        return $best;
    }

    /**
     * @param string $userMention
     */
    private function addAbstain($userMention)
    {
        $abstains = $this->getAbstains();
        $abstains[$userMention] = true;

        $this->getCache()->save(self::CACHE_KEY_ABSTAINS, $abstains);
    }

    /**
     * @param string $userMention
     */
    private function removeAbstain($userMention)
    {
        $abstains = $this->getAbstains();
        unset($abstains[$userMention]);

        $this->getCache()->save(self::CACHE_KEY_ABSTAINS, $abstains);
    }
}
