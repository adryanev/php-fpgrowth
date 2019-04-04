<?php
/**
 * Created by PhpStorm.
 * User: adryanev
 * Date: 25/03/19
 * Time: 11:51
 */

namespace adryanev\fpgrowth;

class FPNode
{

    /**
     * @var string
     */
    public $itemID;
    /**
     * @var int
     */
    public $supportCount;
    /**
     * @var FPNode
     */
    public $parent;
    /**
     * @var FPNode
     */
    public $links;
    /**
     * @var FPNode[]
     */
    public $children;

    /**
     * FPNode constructor.
     * @param $itemID
     * @param $count
     * @param $parent
     */
    public function __construct($itemID = -1, $count = 1, $parent = null)
    {
        $this->itemID = $itemID;
        $this->supportCount = $count;
        $this->parent = $parent;
        $this->links = null;
        $this->children = [];
    }


    /** Check if the value has child node
     * @param $itemID
     * @return bool
     */
    public function hasChild($itemID){
        foreach ($this->children as $node){
            if($node->itemID === $itemID){
                return true;
            }

        }
        return false;
    }

    /**
     * Adding child to current Node
     * @param $itemID
     * @return FPNode
     */
    public function addChild($itemID){
        $child = new FPNode($itemID,1,$this);
        array_push($this->children, $child);
        return $child;
    }

    /**
     * Get the child node
     * @param $id
     * @return mixed|null
     */
    public function getChild($id){
        foreach ($this->children as $node){
            if($node->itemID === $id){
                return $node;
            }
        }
        return null;
    }


}