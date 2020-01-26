<?php
namespace PHPec;

final class App {
    use DITrait;
    private $mHead = null; // 队头
    private $mTail = null; // 队尾

    function __construct(){
        $this -> _add('Router');
    }

    function run() {
        $this -> _add('Dispatcher');
        $this -> mHead -> handle();
        $this -> Response -> flush();
    }
    
    // use方法处理
    function __call($name, $args) {
        if ($name == 'use') {
            foreach ($args as $v) {
                $this -> _add($v);  
            }
        }
    }

    // 加入一个中间件
    private function _add($middleware) {
        $m = APP_NS.'\middleware\\' .$middleware;
        if(!class_exists($m)) {
            $m = "PHPec\middleware\\".$middleware;
        }
        $m = new $m;
        if(!$this -> mHead) {
            $this -> mHead = $m;
            $this -> mTail = $m;
        } else {
            $this -> mTail -> setNext($m);
            $this -> mTail = $m;
        }
    }
}