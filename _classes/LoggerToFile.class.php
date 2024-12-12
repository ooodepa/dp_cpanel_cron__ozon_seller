<?php

class LoggerToFile {
    private $log = "";
    private $filename = "noname.txt";

    static function oneLog($filename, $text) {
        try {
            $logger = new LoggerToFile($filename);
            $logger->log($text);
        }
        catch(Throwable $exception) {
            $time = LoggerToFile::getTime();
            $message = "$exception";
            echo "\n$time\n$message\n";
        }
    }

    static function getTime() {
        try {
            return date('Y-m-d_h-i-s');
        }
        catch(Throwable $exception) {
            return "YYYY-MM-DD_hh-ii-ss";
        }
    }

    public function __construct($filename) {
        $this->filename = $filename;
    }

    public function log($log) {
        $this->log .= $log;
    }

    public function __destruct() {
        try {
            $filename = $this->filename;
            $text = $this->log;
            file_put_contents($filename, $text);
        }
        catch(Throwable $exception) {
            $time = LoggerToFile::getTime();
            $message = "$exception";
            echo "\n$time\n$message\n";
        }
    }
}
