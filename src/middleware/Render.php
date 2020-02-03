<?php
namespace PHPec\middleware;
use PHPec\Middleware;

class Render extends Middleware{
    final function handle() {
        $this -> Response -> pushRender([$this,'format']);
        $this -> next(); 
    }

    // 格式化
    public function format($data){
        $okCode = $this -> config('code.ok', 0);
        if(!isset($data['code']) || $data['code'] == $okCode) { // 
            $data = [
                'code'      => $okCode,
                'message'   => 'ok',
                'data'      => $data
            ];
        }
        return $data;
    }
}
