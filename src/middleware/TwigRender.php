<?php
namespace PHPec\middleware;

class TwigRender extends Render {
    function format($data) {
        $tplPath   = $this -> config('view.tpl_path',  APP_PATH.'/view/');
        $cachePath = $this -> config('view.cache_path', APP_PATH.'/../runtime/twig_cache');
        $prodMode  = (defined('APP_PROD_MODE') && APP_PROD_MODE) ? true : false;

        $loader = new \Twig\Loader\FilesystemLoader($tplPath);
        $twig = new \Twig\Environment($loader, array(
            'debug' => !($this -> config('view.cache_on', $prodMode)),
            'cache' => $cachePath,
        ));
        // filter，通过默认指定的service来实现
        // $fClass = Container::twig_filter;
        // foreach(get_class_methods($fClass) as $filter) {
        //    $twig -> addFilter(new \Twig\TwigFilter($filter, [$fClass, $filter]));
        // }
        $okCode = $this -> config('code.ok', 0);
        if(!isset($data['code']) || $data['code'] == $okCode) {
            $tpl  = sprintf("%s.html", $this -> Response -> getTplName());
        } else {
            $tpl =  "error.html";
        }
        return $twig -> render($tpl, $data ?? []);
    }
}