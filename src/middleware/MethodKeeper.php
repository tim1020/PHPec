<?php
namespace PHPec\middleware;
use PHPec\Middleware;

class MethodKeeper extends Middleware{
    function handle() {
        $allow = $this -> config('allow_methods', ['HEAD', 'GET','POST']); // 缺省参数时只支持HEAD、GET和POST
        $method = $this -> Request -> method;
        if(!in_array($method, $allow)) { //不被允许
            $this -> Logger -> error("不被允许的请求方法 - ". $method);
            $this -> Response -> setError($this -> config('code.access_deny', CODE_ACCESS_DENY), '无权限访问');
            $this -> Response -> flush();
            return;
        }
        $this -> next();
    }
}