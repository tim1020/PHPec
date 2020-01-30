<?php
// filename: phpec\src\Validator.php
namespace PHPec;

class Validator extends \Particle\Validator\Validator {
    function check($data) {
        $result = $this -> validate($data);
        if($result -> isValid()) return true;
        $errors = $result -> getMessages();
        $re = [];
        foreach($errors as $k => $e) {
            $key = key($e);
            $re[$k] = $e[$key];
        }
        return $re; // 有错的时候返回 ['字段名' => '错误信息']
    }
}