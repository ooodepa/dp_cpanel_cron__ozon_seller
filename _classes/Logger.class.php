<?php

class Logger {    
    static function getLogDate() {
        return date('Y-m-d h:i:s');
    }

    static function getPath() {
        global $HOME;
        return "$HOME/__logs/log.txt";
    }

    static function log($message) {
        $date = Logger::getLogDate();
        $path = Logger::getPath();
        $text = "\n$date\n$message\n";
        file_put_contents($path, $text, FILE_APPEND);
    }
}
