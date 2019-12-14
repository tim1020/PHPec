<?php
// filename: phpec\src\Request.php
namespace PHPec;

class Request{
    private static $instance = null;
    private $method, $pathinfo, $query_str, $header, $cookie, $files, $document_uri, $post, $get;
    private $router_path, $user_info; // 通过运算得出的请求相关内容，可修改

    public static function getInstance(){
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this -> method       = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this -> pathinfo     = $_SERVER['PATH_INFO'] ?? null;
        $this -> query_str    = $_SERVER['QUERY_STRING'] ??  '';
        $this -> document_uri = $_SERVER['DOCUMENT_URI'] ?? '/';
        $this -> header       = getallheaders();
        $this -> cookie       = $_COOKIE;
        $this -> files        = $_FILES;
        $this -> get          = $_GET;
        if(!empty($_POST)) $this -> post = $_POST;
        else {
            $type     = strtolower($_SERVER['CONTENT_TYPE'] ?? 'application/x-www-form-urlencoded');
            $data = file_get_contents('php://input');
            if($type == 'application/x-www-form-urlencoded') {
                parse_str($data, $this -> post);
            } else {
                $this -> post = json_decode($data, true);
                if(false == $this -> post && $type == 'application/json') {
                    trigger_error('请求参数不是有效的json格式', E_USER_ERROR);
                }
            } 
        }
        Logger::getInstance() -> debug('Request:', get_object_vars($this));
    }
    public function setUserInfo($info) {
        $this -> user_info = $info;
    }
    public function setRouterPath($path) {
        $this -> router_path = $path;
    }
    public function __toString() {
       return json_encode(get_object_vars($this));
    }
    public function __get($k) {
        if(isset($this -> {$k})) return $this -> {$k};
        return null;
    }
}

if (!function_exists('getallheaders')) {
    function getallheaders(){ 
        $headers = [];
        foreach ($_SERVER as $name => $value)  {
            if (substr($name, 0, 5) == 'HTTP_') {
                $k = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$k] = $value; 
            } 
        } 
        return $headers; 
    } 
}