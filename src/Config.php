<?php
namespace PHPec;
// 配置读取
class Config {
    private $data = [];
    private static $instance = null;

    private function __construct(){
        if(file_exists(APP_PATH.'/config/prod')) {
            define('APP_PROD_MODE', true);
            $config = include APP_PATH.'/config/env_prod.php';
        } else {
            $config = include APP_PATH.'/config/env_dev.php';
        }
        // 通用配置，设置那些与环境不相关，没有在环境配置中设置的项
        $common = include APP_PATH.'/config/config.php';
        if(defined('CFG_FILE')) { 
            $common = array_merge($common, include CFG_FILE);
        }
        $this -> data =  $config + $common; 
    }

    static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function get(string $key, $default = null) {
        $ks = explode(".", $key);
        $data = $this -> data;
        foreach ($ks as $k) {
            if (empty($k)) {
                $data = null;
                break;
            }
            if (isset($data[$k])) {
                $data = $data[$k];
            } else {
                $data = null;
                break;
            }
        }
        return $data ?? $default;
    }
}