<?php
namespace PHPec;
// 控制器基类

class Controller {
    use DITrait;
    protected $Validator; 
    protected $services = []; // 注入的service， ['类名']
    function __construct() {
        // $this -> Validator  = new Validator();
    }
}