<?php
namespace PHPec;

class Response {
    private static $instance = null;
    private $body = null;
    private $status = 200;
    private $statusText = '';
    private $headers = [
        'Content-Type' => 'text/html; charset=UTF-8'
    ];
    private $error = null;
    private $tpl_name = ''; // 保存模板名称
    private $render = []; // 渲染方法栈
    private $phrase= [ // 常见响应码短语
        304 => 'Not Modified',
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        408 => 'Request Timeout',
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout'
    ];
    public static function getInstance(){
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    // 设置头
    public function setHeader($key, $val) {
        $this -> headers[$key] = $val;
    }
    // 设置响应码
    public function setStatus($status, $text = null) {
        $this -> status = $status;
        $this -> statusText = $text ?? ($this -> phrase[$status] ?? '');
    }
    // 设置body内容
    public function setBody($data) {
        $this -> body = $data;
    }
    // 获取当前body内容
    public function getBody() {
        return $this -> body;
    }
    // 设置错误
    public function setError($code, $message) {
        $this -> error = [
            'code'      => $code,
            'message'   => $message
        ];
    }
    // 获取当前error内容
    public function getError() {
        return $this -> error;
    }
    // 设置模板名称
    public function setTplName($name) {
        $this -> tpl_name = $name;
    }
    // 获取模板名称
    public function getTplName($name) {
        return $this -> tpl_name;
    }
    // 设置render方法
    public function pushRender($cb) {
        if(is_callable($cb)) {
            $this -> render[] = $cb;
        } else {
            trigger_error('render invalid', E_USER_ERROR);
        }
    }
    // 输出响应，脚本执行结束
    public function flush() {
        if($this -> status != 200) {
            $str = sprintf("HTTP/1.1 %s %s", $this -> status, $this -> statusText);
            header($str);
        } else {
            if($this -> error) {
                $this -> body = $this -> error;
            }
            if($this -> render) {
                foreach(array_reverse($this -> render) as $cb) {
                    $this -> body = call_user_func($cb, $this -> body);
                }
            }
            if(is_array($this -> body)) {
                $this -> body = json_encode($this -> body);
                $this -> setHeader('Content-Type', 'application/json;charset=UTF-8');
            }
            if (!headers_sent()) {
                foreach ($this -> headers as $k => $v) {
                    header("$k:$v");
                }
            }
            $log_msg = [
            	'headers' => $this -> headers,
              	'body'    => $this -> body,
                'status'  => $this -> status,
                'tpl_name'=> $this -> tpl_name
            ];
            Logger::getInstance() -> debug('Response:', $log_msg);
            echo $this -> body;
        }
        exit;
    }
    // 重定向
    public function redirect($url) {
        $this -> setHeader('Location', $url);
        $this -> flush();
    }
}