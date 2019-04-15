<?php
/**
 * Created by PhpStorm.
 * User: adryanev
 * Date: 25/03/19
 * Time: 11:52
 */

namespace adryanev\fpgrowth;

use Math\Combinatorics\Combination;

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
        $allArray = [];
        foreach ($patterns as $key =>$pattern){
            $pat = $pattern->getPattern();
            $new = array_filter($pat);
            $allArray[] = $new;
        }


        foreach ($patterns as $key => $pattern){
            $upperSupport = $pattern->getSupportCount();
            $pat = $pattern->getPattern();
            $new = array_filter($pat);
            for($i =1 ; $i<sizeof($new); $i++){
                $combination = Combination::get($new,$i);
                foreach ($combination as $antecedent){
                    sort($antecedent);
                    $consequent = array_diff($new, $antecedent);
                    $keyConseq = [];

                    foreach ($allArray as $newKey =>$arr){
                        if($antecedent == $arr){
                            $keyConseq[] = $newKey;

                        }
                    }

                    if(!empty($keyConseq)){
                        $rule = new Rule();
                        $rule->antecedent = $antecedent;
                        $rule->consequent = $consequent;
                        $rule->support = $upperSupport / sizeof($this->data);
                        $lowerSupport = $patterns[$keyConseq[0]]->supportCount;
                        $confidence = $upperSupport/ $lowerSupport;
                        $rule->confidence = $confidence;

                        if($confidence >= $minConfidence){
                            $rules[] = $rule;
                        }

                    }
                }
            }

         }
        return $rules;
        }


}