<?php
/**
 * DokuWiki Plugin relativetimehelper (Helper Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <dokuwiki@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class helper_plugin_relativetimehelper extends DokuWiki_Plugin {

    const SECONDS_PER_YEAR = 31536000;
    const SECONDS_PER_MONTH = 2635200;
    const SECONDS_PER_WEEK = 604800;
    const SECONDS_PER_DAY = 86400;
    const SECONDS_PER_HOUR = 3600;
    const SECONDS_PER_MINUTE = 60;

    /**
     * @param int $timestamp
     * @param int $depth how fine the granularity should be
     * @param int $now reference timestamp, defaults to time()
     * @return string
     */
    public function getRelativeTimeString($timestamp, $depth = 2, $now = null) {
        if (!$now) {
            $now = time();
        }
        $timediff = abs($now - $timestamp);
        $parts = array();
        $seconds = $timediff % $this::SECONDS_PER_MINUTE;
        if ($seconds != 0) {
            if ($seconds == 1) {
                $parts[] = sprintf($this->getLang('second'), 1);
            } else {
                $parts[] = sprintf($this->getLang('seconds'), $seconds);
            }
            $timediff -= $seconds;
        } else {
            $parts[] = null;
        }

        $minuts = ($timediff % $this::SECONDS_PER_HOUR) / $this::SECONDS_PER_MINUTE;
        if ($minuts != 0) {
            if ($minuts == 1) {
                array_unshift($parts, sprintf($this->getLang('minut'), 1));
            } else {
                array_unshift($parts, sprintf($this->getLang('minuts'), $minuts));
            }
            $timediff -= $timediff % $this::SECONDS_PER_HOUR;
        } elseif ($timediff) {
            array_unshift($parts, null);
        }

        $hours = ($timediff % $this::SECONDS_PER_DAY) / $this::SECONDS_PER_HOUR;;
        if ($hours != 0) {
            if ($hours == 1) {
                array_unshift($parts, sprintf($this->getLang('hour'), 1));
            } else {
                array_unshift($parts, sprintf($this->getLang('hours'), $hours));
            }
            $timediff -= $timediff % $this::SECONDS_PER_DAY;
        } elseif ($timediff) {
            array_unshift($parts, null);
        }

        $days = ($timediff % $this::SECONDS_PER_WEEK) / $this::SECONDS_PER_DAY;;
        if ($days != 0) {
            if ($days == 1) {
                array_unshift($parts, sprintf($this->getLang('day'), 1));
            } else {
                array_unshift($parts, sprintf($this->getLang('days'), $days));
            }
            $timediff -= $timediff % $this::SECONDS_PER_WEEK;
        } elseif ($timediff) {
            array_unshift($parts, null);
        }

        $weeks = $timediff / $this::SECONDS_PER_WEEK; // todo extend to cover months and years by date-calculation
        if ($weeks != 0) {
            if ($weeks == 1) {
                array_unshift($parts, sprintf($this->getLang('week'), 1));
            } else {
                array_unshift($parts, sprintf($this->getLang('weeks'), $weeks));
            }
        } elseif ($timediff) {
            array_unshift($parts, null);
        }

        while (count($parts) > $depth) {
            array_pop($parts);
        }

        $parts = array_filter($parts);

        if (count($parts) == 1) {
            $relTime = $parts[0];
        } else {
            $lastPart = array_pop($parts);
            $relTime = join(', ', $parts) . ' ' . $this->getLang('and') . ' ' . $lastPart;
        }
        if ($now - $timestamp < 0) {
            return sprintf($this->getLang('in future'), $relTime);
        }
        return sprintf($this->getLang('in past'), $relTime);
    }

    protected function round($number, $rounding){
        if ($rounding == 'ceil') {
            return ceil($number);
        }
        if ($rounding == 'floor') {
            return floor($number);
        }
        return round($number);
    }

}

// vim:ts=4:sw=4:et:
