<?php
namespace adryanev\fpgrowth;


/**
 * Project: php-fpgrowth
 * Class Pattern
 * @package adryanev\fpgrowth
 * @author Adryan Eka Vandra <adryanekavandra@gmail.com>
 * Date: 4/9/2019
 * Time: 4:09 PM
 */
class Pattern
{
    /**
     * @var array
     */
    public $pattern;
    /**
     * @var int
     */
    public $supportCount;

    /**
     * Pattern constructor.
     */
    public function __construct()
    {
        $this->pattern = [];
        $this->supportCount = 0;
    }

    /**
     * @return array
     */
    public function getPattern(){
        return $this->pattern;
    }

    /**
     * @return int
     */
    public function getSupportCount(){
        return $this->supportCount;
    }

    /**
     * @param array $pattern
     */
    public function setPattern(array $pattern){
        $this->pattern = $pattern;
    }

    /**
     * @param $supportCount
     */
    public function setSupportCount($supportCount){
        $this->supportCount = $supportCount;
    }


}