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

   public $root;
   public static $headerList;
   public $frequentItems;

   public function __construct($transactions, $threshold, $rootValue, $rootCount){

       $this->frequentItems = self::findFrequentItems($transactions, $threshold);
       self::$headerList = self::buildHeaderList($this->frequentItems);
       $this->root = $this->buildFPTree($transactions, $rootValue, $rootCount, $this->frequentItems);
   }

    private static function findFrequentItems($transactions, $threshold)
    {

        $items = [];
        foreach($transactions as $transaction){
            foreach ($transaction as $item){
                if(array_key_exists($item, $items)) $items[$item] +=1;
                else $items[$item] = 1;

            }
        }

        print "Items sebelum di cek treshold".PHP_EOL;
        print_r($items);
        foreach ($items as $key =>$value){
            if($value < $threshold){
                unset($items[$key]);
            }
        }

        print "Item sesudah dicek treshold". PHP_EOL;
        print_r($items);


        return $items;
    }

    private static function buildHeaderList($frequentItems)
    {
        $headers = [];
        foreach ($frequentItems as $key =>$value){
            $headers[$key]= null;
        }

        print "HeaderList".PHP_EOL;
        print_r($headers);

        return $headers;


    }

    private function buildFPTree($transactions, $rootValue, $rootCount, $frequentItems)
    {
        $root = new FPNode($rootValue,$rootCount,null);
        print "The Root FP Tree".PHP_EOL;
        print_r($root);
        $sortedItems = [];
        print "Transaction".PHP_EOL;
        foreach ($transactions as $transaction){

            $holder = [];
            foreach ($transaction as $item){

                if(array_key_exists($item, $frequentItems)){
                    array_push($holder,$item);
                }
            }
            usort($holder, function ($item1, $item2){
                $compare = $this->frequentItems[$item2] - $this->frequentItems[$item1];
                if($compare == 0) return ($item1-$item2);
                return $compare;
            });
            $sortedItems = $holder;
            if(sizeof($sortedItems)>0) $this->insertTree($sortedItems,$root, self::$headerList);
        }
//        print "HeaderList Now";
//        print_r(self::$headerList);
//        print "TREE".PHP_EOL;
//        print_r($root);
//
//        exit();
        return $root;



    }

    private function insertTree(array $items, FPNode $node, array &$headerList)
    {
//
//        print "=========================".PHP_EOL;
//        print "items".PHP_EOL;
//        print_r($items);
//        print "=========================".PHP_EOL;

        $first = $items[0];
//        print "First element in tree". PHP_EOL;
//        print_r($first);
//        print PHP_EOL;
//        print "=========================".PHP_EOL;

        $child = $node->getChild($first);
//        print "Current Node Child".PHP_EOL;
//        var_dump($child);
//        print "=========================".PHP_EOL;

        if(!is_null($child)){
//            print "Menambahkan support count ke child".PHP_EOL;
            $child->supportCount +=1;
//            var_dump($child);
//            print "=========================".PHP_EOL;
//            var_dump($headerList);


        }
         else{
//             print "Menambahkan child ke current node".PHP_EOL;
             $child = $node->addChild($first);
//             print_r($child);
//             print PHP_EOL;
//             print "=========================".PHP_EOL;

//             print "Kondisi node".PHP_EOL;
//             print_r($node);
//             print "=========================".PHP_EOL;

//             print "HeaderList Now".PHP_EOL;
//             print_r($headerList);
//             print "=========================".PHP_EOL;
//             var_dump($headerList[2]);

//             var_dump($headerList[$first]);
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
//         print "remaining items";
//         print_r($remaining_items);
//        print "=========================".PHP_EOL;

        if (sizeof($remaining_items)> 0){
            $this->insertTree($remaining_items, $child,$headerList);
        }

    }

    private function treeHasSinglePath($node){
       $numChildren = sizeof($node->children);
       if($numChildren>1) return false;
       elseif ($numChildren ==0) return true;
       else return true && $this->treeHasSinglePath($node->children[0]);
    }

    public function minePatterns($threshold)
    {
        if ($this->treeHasSinglePath($this->root)) {
           return $this->generatePatternList();

        }
        else{
           return $this->zipPatterns($this->mineSubTree($threshold));
        }
    }

    private function generatePatternList()
    {
        $patterns = [];

        $pattern = ['suffix'=>null, 'supportCount'=>null];
        $items = array_keys($this->frequentItems);

        if(is_null($this->root->itemID)){
           $pattern['suffix'] =null;
        }else{
            $pattern['suffix'][] = $this->root->itemID;
            $pattern['supportCount'] = $this->root->supportCount;
            $patterns[] = $pattern;
        }

        for($i=1; $i<sizeof($items)+1;$i++){
            $combination = Combination::get($items,$i);
            foreach ($combination as $subset){
               $a = $subset;
               array_push($a, $this->root->itemID);
               asort($a);
               $val = [];
               foreach ($a as $x){
                   $val[] = $this->frequentItems[$x];
               }
               $min = min($val);
               $pattern['suffix'] = $a;
               $pattern['supportCount'] = $min;
               $patterns[] = $pattern;
            }
        }
        return $patterns;
    }

    private function mineSubTree($threshold)
    {
        $patterns = [];
        $miningOrder = array_keys($this->frequentItems);
        usort($miningOrder, function ($item2, $item1){
            $compare = $this->frequentItems[$item2] - $this->frequentItems[$item1];
            if($compare == 0) return ($item1-$item2);
            return $compare;
        });

        print_r($miningOrder);
        exit();

        foreach ($miningOrder as $item){
            $suffixes = [];
            $conditionalTreeInput = [];
            $node = self::$headerList[$item];

            while(!is_null($node)){
                array_push($suffixes, $node);
                $node = $node->links;
            }
            foreach($suffixes as $suffix){
                $frequency = $suffix->count;
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
                    $patterns[$key]['supportCount']+=$pattern['supportCount'];
                }else{
                    $key = array_search($pattern,$patterns);
                    $patterns[$key]['supportCount'] = $pattern['supportCount'];
                }

            }

        }

        return $patterns;
    }

    private function zipPatterns($patterns)
    {
        $suffix = $this->root->itemID;

        if(!is_null($suffix)){
            $newPattern = [];
            foreach ($patterns as $pattern){
                $a = $pattern;
                array_push($a, $this->root->itemID);
                asort($a);
                $val = [];
                $pattern['suffix'] = $a;
                $pattern['supportCount'] = $a[''];
                $patterns[] = $pattern;

            }
        }

        return $patterns;
    }


}