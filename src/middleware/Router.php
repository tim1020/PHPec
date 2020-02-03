<?php
namespace PHPec\middleware;
use PHPec\Middleware;

class Router extends Middleware{
    final function handle() {
        $path = $this -> path();
        $map  = $this -> config('router.map', []);
        $path = $map[$path] ?? $path;
        $this -> Request -> setRouterPath($path);
        $this -> next(); 
    }

    public function path(){
        $pos = strpos($_SERVER['REQUEST_URI'], '?');
        $path = (false === $pos) ? $_SERVER['REQUEST_URI'] : substr($_SERVER['REQUEST_URI'], 0, $pos);
        return $path;
    }
}