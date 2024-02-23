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

use DateTimeImmutable;

/**
 * Dater
 *
 * @psalm-immutable
 */
class Dater extends DateTimeImmutable implements DaterInterface
{
    /**
     * Add minutes
     *
     * @param int $minutes The minutes
     * @return static
     */
    public function addMinutes(int $minutes): static
    {
        $modified = $this->modify('+'.$minutes.' minutes');
        return $modified ?: $this;
    }

    /**
     * Substract minutes
     *
     * @param int $minutes The minutes
     * @return static
     */
    public function subMinutes(int $minutes): static
    {
        $modified = $this->modify('-'.$minutes.' minutes');
        return $modified ?: $this;
    }

    /**
     * Add days
     *
     * @param int $days The days
     * @return static
     */
    public function addDays(int $days): static
    {
        $modified = $this->modify('+'.$days.' days');
        return $modified ?: $this;
    }

    /**
     * Substract $days days
     *
     * @param int The days
     * @return static
     */
    public function subDays(int $days): static
    {
        $modified = $this->modify('-'.$days.' days');
        return $modified ?: $this;
    }    
}