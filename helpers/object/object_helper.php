<?php
/*
 * Object Controller's view helper 
 * */
class ObjectHelper extends ViewHelper {
    public $foo;
    
    public function getFoo() {
        return $this->foo = "bingo";
    }
    
    //public function 
}