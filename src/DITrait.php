<?php
// filename: phpec\src\DITrait.php;
namespace PHPec;

Trait DITrait {
    // 通过魔术方法注入
    function __get($k) {
        return $this -> _inject($k);
    }
    // 注入指定的对象
    function _inject($k) {
        if(in_array($k, ['Config', 'Logger', 'Response', 'Request'])) {
            return call_user_func(__NAMESPACE__.'\\'.$k.'::getInstance', null);
        }
    }
    ### -- 以下二次包装一些常用方法 --
    // 获取一个配置
    function config($k, $default = null) {
        return $this -> Config -> get($k, $default);
    }
    /**
     * 获取指定的Post参数值
     * 如果未设置，返回$default； $key可以为数组，即一次获取多个，返回数组
     */
    function post($key, $default = null) {
        if(is_array($key)) {
            $re = [];
            foreach($key as $k) {
                $re[$k] = $this -> Request -> post[$k] ?? $default;
            }
        } else {
            $re = $this -> Request -> post[$key] ?? $default;
        }
        return $re;
    }
    /**
     * 获取指定的Get参数值
     * 如果未设置，返回$default； $key可以为数组，即一次获取多个，返回数组
     */
    function get($key, $default = null) {
        if(is_array($key)) {
            $re = [];
            foreach($key as $k) {
                $re[$k] = $this -> Request -> get[$k] ?? $default;
            }
        } else {
            $re = $this -> Request -> get[$key] ?? $default;
        }
        return $re;
    }
    // 抛出框架自定义异常
    function exception($msg, $code = 0) {
        throw new Exception($msg, $code);
    }
    // 打印debug日志
    function debug($msg, ...$holder) {
        $msg = sprintf($msg, ...$holder);
        $this -> Logger -> debug($msg);
    }
}