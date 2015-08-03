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

class HtmlRenderer
{
    private $html;
    private $matrix;
    private $storeRanking;
    private $abstains;

    /**
     * @param array $matrix
     * @param array $storeRanking
     * @param array $abstains
     */
    public function __construct(array $matrix, array $storeRanking, array $abstains)
    {
        $this->matrix = $matrix;
        $this->storeRanking = $storeRanking;
        $this->abstains = $abstains;
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->html = '<table>';
        $hasVotes = false;

        if (!empty($this->matrix) && !empty($this->storeRanking)) {
            $this->renderVotes();
            $hasVotes = true;
        }

        if (!empty($this->abstains)) {
            if ($hasVotes) {
                $this->renderSeparator();
            }

            $this->renderAbstains();
        }

        $this->html .= '</table>';

        return $this->html;
    }

    /**
     * @return string
     */
    private function renderVotes()
    {
        $best = $this->storeRanking;
        $bestValues = array_values($best);

        if ($bestValues[0] === $bestValues[1]) {
            // something that doesn't match
            $bestStore = uniqid(time());
        } else {
            $bestStore = array_keys($best)[0];
        }

        foreach ($this->matrix as $foodindex => $matriz) {
            $this->html .= '<tr>';
            $store = array_keys($matriz)[0];
            $isBest = $store === $bestStore;
            $isValidStore = isset($best[$store]);

            $this->html .= '<td>';

            if ($isValidStore) {
                $this->html .= '[';
            }
            if ($isBest) {
                $this->html .= '<b>✔';
            }

            $this->html .= $isValidStore ? $best[$store] : '&nbsp;';

            if ($isBest) {
                $this->html .= '</b>';
            }
            if ($isValidStore) {
                $this->html .= ']';
            }

            $this->html .= '</td>';

            foreach ($matriz as $k => $mat) {
                $this->html .= '<td>';

                if ($isBest) {
                    $this->html .= '<b>';
                }

                if ($mat === null || $mat === '') {
                    $this->html .= '&nbsp;';
                } elseif (is_bool($mat)) {
                    $this->html .= '&nbsp;'.($mat ? '✓' : '-');
                } else {
                    $this->html .= (string) $mat.'&nbsp;&nbsp;';
                }

                if ($isBest) {
                    $this->html .= '</b>';
                }

                $this->html .= '</td>';
            }

            $this->html .= '</tr>';
        }
    }

    /**
     * @return string
     */
    private function renderAbstains()
    {
        $voteCount = count($this->matrix[0]);

        if ($voteCount > 1) {
            $this->html .= '<tr><td colspan="'.($voteCount + 1).'">';
        } else {
            $this->html .= '<tr><td>';
        }

        $this->html .= 'Abstains: '.implode(', ', array_keys($this->abstains));
        $this->html .= '</td></tr>';
    }

    /**
     * @return string
     */
    private function renderSeparator()
    {
        $this->html .= '<tr><td colspan="'.(count($this->matrix[0]) + 1).'">&nbsp;</td></tr>';
    }
}
