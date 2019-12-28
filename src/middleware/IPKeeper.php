<?php
namespace PHPec\middleware;
use PHPec\Middleware;

class IPKeeper extends Middleware{
    // 重写前置处理
    function handle() {
        $ip = $this -> _getIp();
        $whiteList = $this -> config('ip.allow', []); // 白名单，通过ip.allow配置
        $blackList = $this -> config('ip.deny', []);  // 黑名单，通过ip.deny配置
        // 判断，有白名单但不在白名单中，或者有黑名单且在黑名单中，禁止访问
        if( ($whiteList && !in_array($ip, $whiteList)) || ($blackList && in_array($ip, $blackList)) ) {
            $this -> Logger -> error("IP禁止访问 - ". $ip);
            $this -> Response -> setError($this -> config('code.access_deny', CODE_ACCESS_DENY), '无权限访问');
            $this -> Response -> flush();
            return;
        }
        // 继续下一中间件
        $this -> next();
    }

    private function _getIp() {
        $ip = getenv('REMOTE_ADDR');   
        if(getenv('HTTP_CLIENT_IP')){
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR')){
            $ip = getenv('HTTP_X_FORWARDED_FOR');    
        }
        return $ip;
    }
}