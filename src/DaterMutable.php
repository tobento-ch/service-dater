<?php

/**
 * TOBENTO
 *
 * @copyright    Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);
 
namespace Tobento\Service\Dater;

use DateTime;

/**
 * DaterMutable
 */
class DaterMutable extends DateTime implements DaterInterface
{
    /**
     * Add minutes
     *
     * @param int $minutes The minutes
     * @return static
     */
    public function addMinutes(int $minutes): static
    {
        $this->modify('+'.$minutes.' minutes');
        return $this;
    }

    /**
     * Substract minutes
     *
     * @param int $minutes The minutes
     * @return static
     */
    public function subMinutes(int $minutes): static
    {
        $this->modify('-'.$minutes.' minutes');
        return $this;
    }

    /**
     * Add days
     *
     * @param int $days The days
     * @return static
     */
    public function addDays(int $days): static
    {
        $this->modify('+'.$days.' days');
        return $this;
    }

    /**
     * Substract days
     *
     * @param int $days The days
     * @return static
     */
    public function subDays(int $days): static
    {
        $this->modify('-'.$days.' days');
        return $this;
    }    
}