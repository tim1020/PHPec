<?php
namespace PHPec;

defined('APP_PATH') || die('未定义APP_PATH'); // 项目源码目录
defined('APP_NS')   || die('未定义APP_NS');

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
        // 全局异常处理
        set_exception_handler(function(\Throwable $e) {
            $msg = sprintf("%s in %s at line %d", 
                           $e -> getMessage(),
                           $e -> getFile(), 
                           $e -> getLine()
                          );
            trigger_error($msg, E_USER_ERROR);
        });
        // 全局错误处理
        set_error_handler(function($errno, $errstr, $errfile, $errline){
            if($errno == E_USER_ERROR || $errno == E_USER_WARNING) {
                $msg = $errstr;
            } else {
                $msg = sprintf("%s in %s at line %d", $errstr, $errfile, $errline);
            }
            Logger::getInstance() -> error($msg);
            switch($errno) {
                case E_USER_ERROR: // 用户错误，中止，http 500错误
                    if(defined('APP_PROD_MODE') && APP_PROD_MODE) {
                        Response::getInstance() -> setStatus(500);
                        Response::getInstance() -> flush();
                    } else {
                        die($msg);
                    }
                    exit;
                case E_USER_WARNING: // 记录警告信息，跳过系统机制继续往下执行
                    return true;
                default:
                    return false; // 交回系统内置机制处理
            }
        });
        // 生成并返回应用实例
        return new App();
    }
}
?>