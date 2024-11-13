<?php

$HOME = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : __DIR__;

try {
    include_once "$HOME/env.php";
    include_once "$HOME/_classes/LoggerInTelegram.class.php";
    include_once "$HOME/_classes/OzonSellerSqlite__ProductList.class.php";
    include_once "$HOME/_classes/OzonSellerSqlite__ProductInfo.class.php";
    include_once "$HOME/_classes/OzonSellerSqlite__PostingFbsActList.class.php";
    include_once "$HOME/_classes/OzonSellerSqlite__FinanceTransactionList.class.php";

    echo "START CRON";  
    LoggerInTelegram::log("START CRON");

    (new OzonSellerSqlite__FinanceTransactionList())->saveToDatabase();
    (new OzonSellerSqlite__PostingFbsActList())->saveToDatabase();
    (new OzonSellerSqlite__ProductList())->saveToDatabase();
    (new OzonSellerSqlite__ProductInfo())->saveToDatabase();
  
    LoggerInTelegram::log("END CRON");
    echo "END CRON";
}
catch(Throwable $ex) {
    echo $ex;
    LoggerInTelegram::logCode($ex);
    LoggerInTelegram::log("END CRON");
    echo "END CRON";
}
