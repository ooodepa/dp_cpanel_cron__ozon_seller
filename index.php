<?php

$HOME = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : __DIR__;

try {
    include_once "$HOME/env.php";
    include_once "$HOME/_classes/LoggerInTelegram.class.php";
    include_once "$HOME/_classes/OzonSellerSqlite__ProductList.class.php";
    include_once "$HOME/_classes/OzonSellerSqlite__ProductInfo.class.php";

    echo "START APP";  
    LoggerInTelegram::log("START APP");

    (new OzonSellerSqlite__ProductList())->saveToDatabase();
    (new OzonSellerSqlite__ProductInfo())->saveToDatabase();
  
    LoggerInTelegram::log("END APP");
    echo "END APP";
}
catch(Throwable $ex) {
    echo $ex;
    LoggerInTelegram::logCode($ex);
}
