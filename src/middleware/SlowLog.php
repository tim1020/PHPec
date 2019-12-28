<?php
namespace PHPec\middleware;
use PHPec\Middleware;

class SlowLog extends Middleware{
    private $start;
    function handle() {
        $this -> start = microtime(1);
        $this -> next(); // 处理其它内容
        // 后置处理
        $elapsed = round((microtime(1) - $this -> start) * 1000, 2);  // 毫秒
        $expires = $this -> config('log.slow_timeout', 10 * 1000); // 超时时间设置，默认10s
        if($elapsed > $expires) { // 所耗时间大于设置的超时时间，记录日志
            //TODO: 单独写入指定日志
            $this -> Logger -> warning("request timeout", ['elapsed' => $elapsed, 'request' => $this -> Request]);
        }
    }
}