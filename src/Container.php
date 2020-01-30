<?php
// filename: phpec\src\Container
// 注入对象容器
namespace PHPec;

class Container {
    private static $objs = [];
    // 设置一个新对象，会覆盖原有的同名对象
    public static function set($k, $obj) {
        self::$objs[$k] = $obj;
    }
    // 获取一个对象，未存在时先放入再返回啊
    public static function get($name) {
        $obj = self::$objs[$name] ?? null;
        if(!$obj) {
            $class = sprintf("\\%s\\services\\%s", APP_NS, $name);
            if(!class_exists($class)) {
                trigger_error(sprintf("service %s not found", $name), E_USER_ERROR);
            }
            $obj = new $class;
            self::set($name, $obj);
        }
        return $obj;
    }
}