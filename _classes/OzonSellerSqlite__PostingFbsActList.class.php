<?php

$HOME = strlen($_SERVER['DOCUMENT_ROOT']) != 0 ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['ENV_CRON_CPANEL_OZON_SELLER__HOME'];

include_once "$HOME/_classes/LoggerToFile.class.php";
include_once "$HOME/_classes/DatabaseConnect.class.php";
include_once "$HOME/_classes/LoggerInTelegram.class.php";
include_once "$HOME/_classes/OzonSeller__PostingFbsActList.class.php";

class OzonSellerSqlite__PostingFbsActList {
    private function getConnect() {
        return DatabaseConnect::getMysqlPdo();
    }

    public function fetch__getArray() {
        return (new OzonSeller__PostingFbsActList())->getAll();
    }

    function saveToDatabase() {
        try {
            $tableName = "OZON_DOC_PostingFbsActList";
            $products = $this->fetch__getArray();

            $YYYY_MM_DD = date('Y-m-d');
            $log_filename = "_logs/$YYYY_MM_DD"."_$tableName.json";
            $log_text = json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            LoggerToFile::oneLog($log_filename, $log_text);

            $pdo = $this->getConnect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            echo "<p style='color: green;'>Успешное подключение к SQLite</p>";

            $sql = "DROP TABLE IF EXISTS $tableName";
            LoggerInTelegram::logCode($sql);
            $pdo->prepare($sql)->execute();
       
            echo "<p style='color: green;'>Таблица удалена успешно $tableName</p>";

            $sql = "CREATE TABLE $tableName (
                        id INT,
                        delivery_method_id INT,
                        delivery_method_name TEXT,
                        integration_type TEXT,
                        containers_count INT,
                        status TEXT,
                        departure_date TEXT,
                        created_at TEXT,
                        updated_at TEXT,
                        act_type TEXT,
                        related_docs__act_of_acceptance TEXT,
                        related_docs__act_of_mismatch TEXT,
                        related_docs__act_of_excess TEXT,
                        is_partial INT,
                        has_postings_for_next_carriage INT,
                        partial_num INT,
                        _raw_json TEXT,
                        _updated_at TIMESTAMP
                    )
                    ";

            LoggerInTelegram::logCode($sql);

            $pdo->prepare($sql)->execute();

            echo "<p style='color: green;'>Таблица создана успешно $tableName</p>";

            $sql = "INSERT INTO
                    $tableName
                    (
                        id,
                        delivery_method_id,
                        delivery_method_name,
                        integration_type,
                        containers_count,
                        status,
                        departure_date,
                        created_at,
                        updated_at,
                        act_type,
                        related_docs__act_of_acceptance,
                        related_docs__act_of_mismatch,
                        related_docs__act_of_excess,
                        is_partial,
                        has_postings_for_next_carriage,
                        partial_num,
                        _raw_json,
                        _updated_at
                    )
                    VALUES
                    ";
            LoggerInTelegram::logCode($sql);

            $array_rows = [];
            $array_values = [];
            $count = 0;
            foreach($products as $element) {
                $array_values []= $element['id'];
                $array_values []= $element['delivery_method_id'];
                $array_values []= $element['delivery_method_name'];
                $array_values []= $element['integration_type'];
                $array_values []= $element['containers_count'];
                $array_values []= $element['status'];
                $array_values []= $element['departure_date'];
                $array_values []= $element['created_at'];
                $array_values []= $element['updated_at'];
                $array_values []= $element['act_type'];
                $array_values []= json_encode($element['related_docs']['act_of_acceptance'], true);
                $array_values []= json_encode($element['related_docs']['act_of_mismatch'], true);
                $array_values []= json_encode($element['related_docs']['act_of_excess'], true);
                $array_values []= $element['is_partial'] ? '1' : '0';
                $array_values []= $element['has_postings_for_next_carriage'] ? '1' : '0';
                $array_values []= $element['partial_num'];
                $array_values []= json_encode($element, true);

                $count += 1;

                $array_rows []= "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            }

            $sql .= implode(",\n", $array_rows);

            LoggerInTelegram::logCode(json_encode($array_values, true));

            $pdo->prepare($sql)->execute($array_values);

            echo "<p style='color: green;'>Таблица заполнена успешно $tableName</p>";
        }
        catch(Throwable $exception) {
            echo "<p style='color: red;'>Исключение</p>";
            echo "<p style='color: red;'>$exception</p>";
            LoggerInTelegram::logCode($exception);
        }
    }
}
