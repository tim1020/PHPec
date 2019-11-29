<?php
namespace PHPec;

final class App {
    function run() {
        $c = sprintf("\\%s\\controller\\%s", APP_NS, 'Helloworld'); 
        $act = $_GET['act'] ?? '_default';
        if(!method_exists($c, $act)) $act = '_default';
        call_user_func([new $c, $act]); // 调用名为Helloworld的控制器，执行act指定的方法
    }
}