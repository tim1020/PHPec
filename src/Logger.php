<?php
namespace PHPec;
use Monolog\Logger as MonoLogger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class Logger extends MonoLogger{
    private static $instance = null;

    public static function getInstance(){
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $logconf = Config::getInstance() -> get('log', []);
        $channel = $logconf['channel'] ?? 'PHPec';
        parent::__construct($channel);
        $path   = $logconf['path']   ?? APP_PATH.'/../runtime/';
        $name   = $logconf['prefix'] ??'log';
        $fname  = $path.'/'.$name;
        $max    = $logconf['max_files'] ?? 7;
        $line   = $logconf['format'] ?? "%datetime% [%channel%:%level_name%] %message% %context% %extra%\n";
        $level  = $logconf['level']  ?? MonoLogger::DEBUG;
        $handler   = new RotatingFileHandler($fname,  $max,  $level, false, 0666);
        $formatter = new LineFormatter($line, "Y-m-d H:i:s", false, true);
        $handler -> setFormatter($formatter);
        $this -> pushHandler($handler);
    }
}
?>
