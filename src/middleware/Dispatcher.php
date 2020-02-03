<?php
namespace PHPec\middleware;
// Dispatcher会被自动添加为最后一个中间件，负责根据路由结果调度控制器
use PHPec\Middleware;
class Dispatcher extends Middleware{
    function handle(){
        $path = $this -> Request -> router_path;
        $re = $this -> _excute($path);
        if(false === $re) { 
            // 没有找到要执行的控制方法，如果有在router.map中设置404，则执行404
            $err404 = $this -> config('router.map.404', null);
            if(!$err404 || !$this -> _excute($err404)) {
                trigger_error('dispatcher error: '. '控制器方法不存在', E_USER_ERROR);
            }
        }
    }
    /**
     * 执行router_path指定的控制器方法，并将结果放在response body
     * 如果成功执行，返回true, 否则返回false
     * $path的格式为: /a/b/c, 最后一段为action，其它为controller(可包含命名空间)
     */
    private function _excute($path){
        if(!$path) return false;
        if(substr($path, 0, 1) == '/') $path = substr($path, 1);
        $path = explode('/', $path); 
        if(count($path) < 2) {
            $path[1] = $path[0] ?? 'index';
            $path[0] = 'Controller';
        }
        $action = array_pop($path);
        $path[] = ucfirst(strtolower(array_pop($path))); // 控制器类名转换为首字母大小，其余全小写
        $controller = implode("\\", $path);
        $this -> Response -> setTplName(strtolower($controller.'_'.$action));
        $class = sprintf('%s\controller\%s', APP_NS, $controller);
        try{
            $ref = new \ReflectionClass($class);
            if(!$ref -> isSubclassOf('\PHPec\Controller')) trigger_error("控制器没有继承PHPec\Controller - ". $controller, E_USER_ERROR);
            $method = $ref -> getMethod($action);
            $parmeters = $method -> getParameters();
            if(count($parmeters) == 0) {
                $re = $method -> invoke(new $class);
            } else { // 有参数
                $arr = [];
                foreach($method -> getParameters() as $p) {
                    $name = $p -> getName();
                    if($this -> Request -> method == 'GET'){
                        $val = $this -> get($name, $p -> allowsNull() ? null : '');
                    } else {
                        $val = $this -> post($name, $p -> allowsNull() ? null : '');
                    }
                    if(!$val && $p -> isOptional()) {
                        $val = $p -> getDefaultValue();
                    }
                    $arr[] = $val;
                }
                $re = $method -> invokeArgs(new $class, $arr);
            }
            $this -> Response -> setBody($re);
            return true;
        } catch(\ReflectionException $e) {
            trigger_error('dispatcher exception: '. $e -> getMessage(), E_USER_ERROR);
            return false;
        } 
        return false;
    }
}
