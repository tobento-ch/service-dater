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

use DateTimeInterface;

/**
 * DaterInterface
 */
interface DaterInterface extends DateTimeInterface
{
    /**
     * Add minutes
     *
     * @param int $minutes The minutes
     * @return static
     */
    public function addMinutes(int $minutes): static;

    /**
     * Substract minutes
     *
     * @param int $minutes The minutes
     * @return static
     */
    public function subMinutes(int $minutes): static;

    /**
     * Add days
     *
     * @param int $days The days
     * @return static
     */
    public function addDays(int $days): static;

    /**
     * Substract days
     *
     * @param int $days The days
     * @return static
     */
    public function subDays(int $days): static;   
}