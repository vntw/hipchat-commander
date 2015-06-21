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

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\BufferedOutput;

class ResultRenderer
{
    const TYPE_HTML = 0;
    const TYPE_ASCII = 1;

    /**
     * @param array $matrix
     * @param array $storeRanking
     * @param array $abstains
     * @param int   $type
     *
     * @return string
     */
    public static function render(array $matrix, array $storeRanking, array $abstains, $type = self::TYPE_HTML)
    {
        switch ($type) {
            case self::TYPE_ASCII:
                return self::createMatrixAscii($matrix, $storeRanking, $abstains);
            case self::TYPE_HTML:
            default:
                return self::createMatrixHtml($matrix, $storeRanking, $abstains);
        }
    }

    /**
     * @param array $matrix
     * @param array $storeRanking
     * @param array $abstains
     *
     * @return string
     */
    private static function createMatrixHtml(array $matrix, array $storeRanking, array $abstains)
    {
        $best = $storeRanking;
        $bestValues = array_values($best);

        if ($bestValues[0] === $bestValues[1]) {
            // something that doesn't match
            $bestStore = uniqid(time());
        } else {
            $bestStore = array_keys($best)[0];
        }

        $table = '<table>';

        foreach ($matrix as $foodindex => $matriz) {
            $table .= '<tr>';
            $store = array_keys($matriz)[0];
            $isBest = $store === $bestStore;
            $isValidStore = isset($best[$store]);

            $table .= '<td>';

            if ($isValidStore) {
                $table .= '[';
            }
            if ($isBest) {
                $table .= '<b>✔';
            }

            $table .= $isValidStore ? $best[$store] : '&nbsp;';

            if ($isBest) {
                $table .= '</b>';
            }
            if ($isValidStore) {
                $table .= ']';
            }

            $table .= '</td>';

            foreach ($matriz as $k => $mat) {
                $table .= '<td>';

                if ($isBest) {
                    $table .= '<b>';
                }

                if ($mat === null || $mat === '') {
                    $table .= '&nbsp;';
                } elseif (is_bool($mat)) {
                    $table .= '&nbsp;'.($mat ? '✓' : '-');
                } else {
                    $table .= (string) $mat.'&nbsp;&nbsp;';
                }

                if ($isBest) {
                    $table .= '</b>';
                }

                $table .= '</td>';
            }

            $table .= '</tr>';
        }

        if (!empty($abstains)) {
            $table .= '<tr><td colspan="'.(count($matrix[0]) + 1).'">&nbsp;</td></tr>';
            $table .= '<tr><td colspan="'.(count($matrix[0]) + 1).'">';
            $table .= 'Abstains: '.implode(', ', array_keys($abstains));
            $table .= '</td></tr>';
        }

        $table .= '</table>';

        return $table;
    }

    /**
     * @param array $matrix
     * @param array $storeRanking
     * @param array $abstains
     *
     * @return string
     */
    private static function createMatrixAscii(array $matrix, array $storeRanking, array $abstains)
    {
        $best = $storeRanking;
        $bestValues = array_values($best);

        if ($bestValues[0] === $bestValues[1]) {
            $bestStore = uniqid();
        } else {
            $bestStore = array_keys($best)[0];
        }

        $output = new BufferedOutput();
        $table = new Table($output);
        $table->setStyle('borderless');

        foreach ($matrix as $foodindex => $matriz) {
            $row = [];
            $store = array_keys($matriz)[0];
            $isBest = $store === $bestStore;

            foreach ($matriz as $k => $mat) {
                if ($mat === null || $mat === '') {
                    $row[] = '';
                } elseif (is_bool($mat)) {
                    $row[] = $mat ? '✓' : '-';
                } else {
                    $row[] = (string) $mat;
                }
            }

            $bestStoreValue = $isBest ? '✔ ' : '';
            $bestStoreValue .= isset($best[$store]) ? $best[$store] : '';

            $row[] = $bestStoreValue;

            $table->addRow($row);
        }

        $table->render();

        return '/code '.$output->fetch();
    }
}
