<?php
include_once "$HOME/env.php";

class DatabaseConnect {
    static function getMysqlPdo() {
        global $env;

        $db_host = $env['db_host'];
        $db_port = $env['db_port'];
        $db_name = $env['db_name'];
        $db_user = $env['db_user'];
        $db_pass = $env['db_pass'];
    
        echo "Start connect with PDO\n";
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        
        return $pdo;
    }
}