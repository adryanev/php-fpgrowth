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
    public $data;
    public $minSupport;
    public $minConfidence;


    public function findFrequentPattern($data, $minSupportCount)
    {
        $this->data= $data;
        $this->minSupport = $minSupportCount;

        $pattern = new FPTree($data,$minSupportCount,null, null);
        return $pattern->minePatterns($minSupportCount);
    }

    public function generateAssociationRules($patterns, $minConfidence){
        $rules = [];

        return $rules;
        }


}