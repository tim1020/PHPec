<?php
namespace PHPec\middleware;
use PHPec\Middleware;

class Router extends Middleware{
    function handle() {
        $pos = strpos($_SERVER['REQUEST_URI'], '?');
        $key = (false === $pos) ? $_SERVER['REQUEST_URI'] : substr($_SERVER['REQUEST_URI'], 0, $pos);
        $this -> Request -> setRouterPath($key);
        $this -> next(); 
    }
}