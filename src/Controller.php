<?php
namespace PHPec;
// 控制器基类

class Controller {
    use DITrait;
    protected $Validator; 
    function __construct() {
        $this -> Validator  = new Validator();
    }
}