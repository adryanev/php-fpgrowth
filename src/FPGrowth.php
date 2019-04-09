<?php
/**
 * Created by PhpStorm.
 * User: adryanev
 * Date: 25/03/19
 * Time: 11:52
 */

namespace adryanev\fpgrowth;

class FPGrowth
{
    /**
     * @var
     */
    public $data;
    /**
     * @var
     */
    public $minSupport;
    /**
     * @var
     */
    public $minConfidence;


    /**
     * @param $data
     * @param $minSupportCount
     * @return array
     */
    public function findFrequentPattern($data, $minSupportCount)
    {
        $this->data= $data;
        $this->minSupport = $minSupportCount;

        $pattern = new FPTree($data,$minSupportCount,null, null);
        return $pattern->minePatterns($minSupportCount);
    }

    /**
     * @param $patterns
     * @param $minConfidence
     * @return array
     */
    public function generateAssociationRules($patterns, $minConfidence){
        $rules = [];

        return $rules;
        }


}