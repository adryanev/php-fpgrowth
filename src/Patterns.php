<?php
/**
 * Project: php-fpgrowth.
 * @author Adryan Eka Vandra <adryanekavandra@gmail.com>
 *
 * Date: 4/8/2019
 * Time: 5:34 PM
 */

namespace adryanev\fpgrowth;


class Patterns
{
    public $suffix;
    public $patterns;

    public function __construct()
    {
        $this->suffix = 0;
        $this->patterns = [];
    }

    /**
     * @return int
     */
    public function getSuffix(): int
    {
        return $this->suffix;
    }

    /**
     * @param int $suffix
     */
    public function setSuffix(int $suffix): void
    {
        $this->suffix = $suffix;
    }

    /**
     * @return array
     */
    public function getPatterns(): array
    {
        return $this->patterns;
    }

    /**
     * @param Pattern $patterns
     */
    public function setPatterns(Pattern $patterns): void
    {
        $this->patterns[] = $patterns;
    }


}