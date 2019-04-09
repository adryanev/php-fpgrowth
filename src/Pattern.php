<?php
/**
 * Project: php-fpgrowth.
 * @author Adryan Eka Vandra <adryanekavandra@gmail.com>
 *
 * Date: 4/8/2019
 * Time: 5:18 PM
 */

namespace adryanev\fpgrowth;


class Pattern
{
    public $pattern;
    public $supportCount;

    public function __construct()
    {
        $this->pattern = [];
        $this->supportCount = 0;
    }

    public function getPattern(){
        return $this->pattern;
    }

    public function getSupportCount(){
        return $this->supportCount;
    }

    public function setPattern(array $pattern){
        $this->pattern = $pattern;
    }

    public function setSupportCount($supportCount){
        $this->supportCount = $supportCount;
    }


}