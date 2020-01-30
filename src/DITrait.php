<?php
// filename: phpec\src\DITrait.php;
namespace PHPec;

Trait DITrait {

    protected $services = [];
    function __get($k) {
        return $this -> _inject($k);
    }

    function _inject($k) {
        $re = $this -> _injectCommon($k);
        if(!$re) {
            $re = $this -> _injectService($k);
        }
        return $re;
    }

    function _injectCommon($k) {
        if(in_array($k, ['Config', 'Logger', 'Response', 'Request'])) {
            return call_user_func(__NAMESPACE__.'\\'.$k.'::getInstance', null);
        }
        return null;
    }
    function _injectService($k) {
        if(empty($this -> services)) return null;
        if(isset($this -> services[$k])) { // 使用 '接口名' => '类名' 配置
            $interface =  sprintf('%s\\services\interfaces\\%s', APP_NS, $k);
            if(!interface_exists($interface)) {
                trigger_error(sprintf("接口未定义： %s", $interface), E_USER_ERROR);
            }
            $k = $this -> services[$k];
            $obj = Container::get($k);
            if(!($obj instanceof $interface)) { // 判断是否实现的接口
                trigger_error(sprintf("service %s 必须继承 %s", $k, $interface), E_USER_ERROR);
            }
            return $obj;
        } elseif(false !== array_search($k, $this -> services)){
            return Container::get($k);
        }
        return null;
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
    // 重定向
    function redirect($url) {
        $this -> Response -> redirect($url);
    }
    // 打印debug日志
    function debug($msg, ...$holder) {
        $msg = sprintf($msg, ...$holder);
        $this -> Logger -> debug($msg);
    }
}