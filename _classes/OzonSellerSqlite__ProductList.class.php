<?php

$HOME = strlen($_SERVER['DOCUMENT_ROOT']) != 0 ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['ENV_CRON_CPANEL_OZON_SELLER__HOME'];

include_once "$HOME/_classes/LoggerToFile.class.php";
include_once "$HOME/_classes/DatabaseConnect.class.php";
include_once "$HOME/_classes/LoggerInTelegram.class.php";
include_once "$HOME/_classes/OzonSeller__ProductList.class.php";

class OzonSellerSqlite__ProductList {
    private function getConnect() {
        return DatabaseConnect::getMysqlPdo();
    }

    private function getAllProducts() {
        return (new OzonSeller__ProductList())->fetchJson__getAllProducts();
    }

    function saveToDatabase() {
        try {
            $tableName = "OZON_CTL_ProductList";
            $products = $this->getAllProducts();

            $YYYY_MM_DD = date('Y-m-d');
            $log_filename = "_logs/$YYYY_MM_DD"."_$tableName.json";
            $log_text = json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            LoggerToFile::oneLog($log_filename, $log_text);

            $pdo = $this->getConnect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            echo "<p style='color: green;'>Успешное подключение к SQLite</p>\n";

            $sql = "DROP TABLE IF EXISTS $tableName";
            LoggerInTelegram::logCode($sql);

            $pdo->prepare($sql)->execute();

            echo "<p style='color: green;'>Таблица удалена успешно $tableName</p>";
       
            $sql = "CREATE TABLE $tableName (
                        product_id INT,
                        offer_id TEXT,
                        has_fbo_stocks INT,
                        has_fbs_stocks INT,
                        archived INT,
                        is_discounted INT,
                        quants TEXT,
                        _raw_json TEXT,
                        _updated_at TIMESTAMP
                    )
                    ";
            LoggerInTelegram::logCode($sql);

            echo "<p style='color: green;'>Таблица создана успешно $tableName</p>\n";

            $pdo->prepare($sql)->execute();

            $sql = "INSERT INTO
                    $tableName
                    (product_id, offer_id, has_fbo_stocks, has_fbs_stocks, archived, quants, is_discounted, _raw_json, _updated_at)
                    VALUES
                    ";
            LoggerInTelegram::logCode($sql);

            $array_rows = [];
            $array_values = [];
            foreach($products as $element) {
                $array_values []= $element['product_id'];
                $array_values []= $element['offer_id'];
                $array_values []= $element['has_fbo_stocks'] ? 1 : 0;
                $array_values []= $element['has_fbs_stocks'] ? 1 : 0;
                $array_values []= $element['archived'] ? 1 : 0;
                $array_values []= $element['is_discounted'] ? 1 : 0;
                $array_values []= json_encode($element['quants']);
                $array_values []= json_encode($element);
                $array_rows []= "(?, ?, ?, ?, ?, ?, ?, NOW())";
            }

            $sql .= implode(",\n", $array_rows);

            $pdo->prepare($sql)->execute($array_values);

            echo "<p style='color: green;'>Таблица заполнена успешно $tableName</p>\n";
        }
        catch(Throwable $exception) {
            echo "<p style='color: red;'>Исключение</p>";
            echo "<p style='color: red;'>$exception</p>";
            LoggerInTelegram::logCode(message: $exception);
        }
    }
}
