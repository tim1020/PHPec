<?php
namespace PHPec;
// 中间件基类（抽象类）
abstract class Middleware {
    use DITrait;
    protected $next = null;
    abstract function handle(); //具体处理
    // 进入下一中间件
    final public function next(){
        if($this -> next) {
            $this -> next -> handle();
        }
    }
    // 绑定下一个中间件
    final function setNext($m){
        $this -> next = $m;
    }
}