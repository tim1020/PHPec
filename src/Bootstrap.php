<?php
namespace PHPec;

defined('APP_PATH') || die('未定义APP_PATH'); // 项目源码目录
defined('APP_NS')   || die('未定义APP_NS');

// todo: 全局错误处理，异常处理


class Bootstrap {
    static function run() {
        // 注册一个自动加载，用来加载项目中的class
        spl_autoload_register(function($class){
            $class = str_replace('.','', $class); //安全过滤
            $path = explode('\\', $class);
            $ns = array_shift($path);
            if($ns == APP_NS) {
                $prefix = APP_PATH;
                $classFile = $prefix. '/'. implode('/',$path).'.php';
                file_exists($classFile) && require $classFile;
            }
        });
        // 生成并返回应用实例
        return new App();
    }
}
?>