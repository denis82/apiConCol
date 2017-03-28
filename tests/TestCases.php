<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace tests;

class TestCases
{
    public $res = [];
    public $keys;
    public $arrays = [];
    
    public function setUp() {
     
    }    

    public function tearDown() {

    }
    
    protected function assert($condition, $message = '1')
    {
        echo $message;
        //print_r('22'.$condition.'22');
       // print_r($this->keys);echo PHP_EOL;
        //print_r($this->arrays);echo PHP_EOL;
        //print_r(gettype($condition));echo PHP_EOL;
        if($condition) {
            echo ' Ok '.PHP_EOL;
        } else {
            echo ' false '.PHP_EOL;
            exit();
        }
    }
    
     protected function assertTrue($condition, $message = '')
    {
        $this->assert($condition === true, $message);
    }
    
    protected function assertFalse($condition, $message = '')
    {
        $this->assert($condition === false, $message);
    }
    
    protected function assertArrayHasKey($key,$array, $message = '')
    {
       // print_r('22'.$key.'22');
        // $this->keys = $key;
        // $this->arrays = $array;
        $this->assert(array_key_exists($key,$array), $message);
    }
}

