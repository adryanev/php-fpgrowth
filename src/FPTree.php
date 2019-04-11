<?php
/**
 * Created by PhpStorm.
 * User: adryanev
 * Date: 25/03/19
 * Time: 11:51
 */

namespace adryanev\fpgrowth;

use Math\Combinatorics\Combination;

class FPTree
{

    /**
     * @var FPNode
     */
    public $root;
    /**
     * @var array
     */
    public $headerList;
    /**
     * @var array
     */
    public $frequentItems;

    /**
     * FPTree constructor.
     * @param $transactions
     * @param $threshold
     * @param $rootValue
     * @param $rootCount
     */
    public function __construct($transactions, $threshold, $rootValue, $rootCount){

       $this->frequentItems = $this->findFrequentItems($transactions, $threshold);
       $this->headerList = $this->buildHeaderList($this->frequentItems);
       $this->root = $this->buildFPTree($transactions, $rootValue, $rootCount, $this->frequentItems);
   }

    /**
     * @param $transactions
     * @param $threshold
     * @return array
     */
    private static function findFrequentItems($transactions, $threshold)
    {

        $items = [];
        foreach($transactions as $transaction){
            foreach ($transaction as $item){
                if(array_key_exists($item, $items)) $items[$item] +=1;
                else $items[$item] = 1;

            }
        }

        foreach ($items as $key =>$value){
            if($value < $threshold){
                unset($items[$key]);
            }
        }

        return $items;
    }

    /**
     * @param $frequentItems
     * @return array
     */
    private static function buildHeaderList($frequentItems)
    {
        $headers = [];
        foreach ($frequentItems as $key =>$value){
            $headers[$key]= null;
        }

        return $headers;


    }

    /**
     * @param $transactions
     * @param $rootValue
     * @param $rootCount
     * @param $frequentItems
     * @return FPNode
     */
    private function buildFPTree($transactions, $rootValue, $rootCount, $frequentItems)
    {
        $root = new FPNode($rootValue,$rootCount,null);

        foreach ($transactions as $transaction){

            $holder = [];
            foreach ($transaction as $item){

                if(array_key_exists($item, $frequentItems)){
                    array_push($holder,$item);
                }
            }
            usort($holder, function ($item1, $item2){
                $compare = $this->frequentItems[$item2] - $this->frequentItems[$item1];
                if($compare == 0){
                    if(is_string($item1)){
                        return strcmp($item1,$item2);
                    }
                    else{
                        return strcmp($item1,$item2);
                    }
                }

                return $compare;
            });
            $sortedItems = $holder;
            if(sizeof($sortedItems)>0) $this->insertTree($sortedItems,$root, $this->headerList);
        }



        return $root;



    }

    /**
     * @param array $items
     * @param FPNode $node
     * @param array $headerList
     */
    private function insertTree(array $items, FPNode $node, array &$headerList)
    {


        $first = $items[0];


        $child = $node->getChild($first);


        if(!is_null($child)){
            $child->supportCount +=1;


        }
         else{
             $child = $node->addChild($first);

             if(is_null($headerList[$first])) $headerList[$first] = $child;
            else{
                $current = $headerList[$first];

                while (!is_null($current->links)){
                    $current = $current->links;
                }

                $current->links = $child;

            }
        }
         $remaining_items = array_slice($items, 1);

        if (sizeof($remaining_items)> 0){
            $this->insertTree($remaining_items, $child,$headerList);
        }

    }

    /**
     * @param $node
     * @return bool
     */
    private function treeHasSinglePath($node){
       $numChildren = sizeof($node->children);
       if($numChildren>1) return false;
       elseif ($numChildren ==0) return true;
       else return true && $this->treeHasSinglePath($node->children[0]);
    }

    /**
     * @param $threshold
     * @return array
     */
    public function minePatterns($threshold)
    {
        if ($this->treeHasSinglePath($this->root)) {
           return $this->generatePatternList();

        }
        else{
           return $this->zipPatterns($this->mineSubTree($threshold));
        }
    }

    /**
     * @return array
     */
    private function generatePatternList()
    {

        $patterns = [];
        $pattern = new Pattern();
        $items = array_keys($this->frequentItems);

        $suffix_value = null;
        if(is_null($this->root->itemID)){
            $suffix_value = null;

        }else{
            $suffix_value = [$this->root->itemID];
            $pattern->setPattern($suffix_value);
            $pattern->setSupportCount($this->root->supportCount);
            $patterns[] = $pattern;
        }

        for($i=1; $i<sizeof($items)+1;$i++){
            $combination = Combination::get($items,$i);

            foreach ($combination as $subset){
                $pats = new Pattern();

                asort($subset);
                $new = array_merge($subset,$suffix_value);
                $pats->setPattern($new);

               $val = [];
               foreach ($subset as $x){
                   $val[] = $this->frequentItems[$x];
               }
               $min = min($val);
               $pats->setSupportCount($min) ;
               $patterns[] = $pats;
            }

        }


        return $patterns;
    }

    /**
     * @param $threshold
     * @return array
     */
    private function mineSubTree($threshold)
    {
        $patterns = [];
        $miningOrder = array_keys($this->frequentItems);
        usort($miningOrder, function ($item2, $item1){
            $compare = $this->frequentItems[$item2] - $this->frequentItems[$item1];
           if($compare == 0){
                    if(is_string($item1)){
                        return strcmp($item1,$item2);
                    }
                    else{
                        return strcmp($item1,$item2);
                    }
                }

            return $compare;
        });

        foreach ($miningOrder as $item){
            $suffixes = [];
            $conditionalTreeInput = [];
            $node = $this->headerList[$item];

            while(!is_null($node)){
                array_push($suffixes, $node);
                $node = $node->links;
            }

            foreach($suffixes as $suffix){
                $frequency = $suffix->supportCount;
                $path = [];
                $parent = $suffix->parent;
                while (!is_null($parent->parent)){
                    array_push($path,$parent->itemID);
                    $parent = $parent->parent;
                }

                for ($i =0; $i<$frequency; $i++){
                    array_push($conditionalTreeInput,$path);
                }


            }


            $subTree = new FPTree($conditionalTreeInput, $threshold, $item, $this->frequentItems[$item]);
            $subTreePatterns = $subTree->minePatterns($threshold);


            foreach ($subTreePatterns as $pattern){
                if(in_array($pattern,$patterns)){
                    $key = array_search($pattern,$patterns);
                    $patterns[$key]->supportCount += $pattern->supportCount;
                }else{
                    $patterns[] = $pattern;
                }
            }

        }


        return $patterns;
    }

    /**
     * @param $patterns
     * @return array
     */
    private function zipPatterns($patterns)
    {
        $suffix = [$this->root->itemID];

        if(!is_null($suffix)){
            $newPattern = [];
            foreach ($patterns as $pattern){
                $oldPattern = $pattern->getPattern();
                asort($oldPattern);
                $new = array_merge($oldPattern,$suffix);
                $newPat = new Pattern();
                $newPat->setPattern($new);
                $newPat->setSupportCount($pattern->getSupportCount());
                $newPattern[] = $newPat;
            }
            return$newPattern;
        }

        return $patterns;
    }


}