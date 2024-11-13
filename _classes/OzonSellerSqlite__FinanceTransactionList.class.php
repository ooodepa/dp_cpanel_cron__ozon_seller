<?php
include_once "$HOME/_classes/DatabaseConnect.class.php";
include_once "$HOME/_classes/LoggerInTelegram.class.php";
include_once "$HOME/_classes/OzonSeller__FinanceTransactionList.class.php";

class OzonSellerSqlite__FinanceTransactionList
{
    private function getConnect() {
        return DatabaseConnect::getMysqlPdo();
    }

    public function fetch__getArray()
    {
        return (new OzonSeller__FinanceTransactionList())->getAll();
    }

    function saveToDatabase()
    {
        try {
            $pdo = $this->getConnect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $tableName = "OZON_DOC_FinanceTransactionList";

            $products = $this->fetch__getArray();

            $sql = "DROP TABLE IF EXISTS $tableName";
            LoggerInTelegram::logCode($sql);
            
            $pdo->prepare($sql)->execute();

            echo "<p style='color: green;'>Таблица удалена успешно $tableName</p>";

            $sql = "CREATE TABLE $tableName (
                        operation_id INTEGER,
                        operation_type TEXT,
                        operation_date TEXT,
                        operation_type_name TEXT,
                        delivery_charge REAL,
                        return_delivery_charge REAL,
                        accruals_for_sale REAL,
                        sale_commission REAL,
                        amount REAL,
                        type TEXT,
                        posting__delivery_schema TEXT,
                        posting__order_date REAL,
                        posting__posting_number TEXT,
                        _posting__user_id TEXT,
                        _posting__order_number TEXT,
                        _posting__order_number_on_day TEXT,
                        posting__warehouse_id REAL,
                        items TEXT,
                        services TEXT,
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
                        operation_id,
                        operation_type,
                        operation_date,
                        operation_type_name,
                        delivery_charge,
                        return_delivery_charge,
                        accruals_for_sale,
                        sale_commission,
                        amount,
                        type,
                        posting__delivery_schema,
                        posting__order_date,
                        posting__posting_number,
                        _posting__user_id,
                        _posting__order_number,
                        _posting__order_number_on_day,
                        posting__warehouse_id,
                        items,
                        services,
                        _raw_json,
                        _updated_at
                    )
                    VALUES
                    ";

            LoggerInTelegram::logCode($sql);

            $array_rows = [];
            $array_values = [];
            $count = 0;
            foreach ($products as $element) {
                $array_values[] = $element['operation_id'];
                $array_values[] = $element['operation_type'];
                $array_values[] = $element['operation_date'];
                $array_values[] = $element['operation_type_name'];
                $array_values[] = $element['delivery_charge'];
                $array_values[] = $element['return_delivery_charge'];
                $array_values[] = $element['accruals_for_sale'];
                $array_values[] = $element['sale_commission'];
                $array_values[] = $element['amount'];
                $array_values[] = $element['type'];
                $array_values[] = $element['posting']['delivery_schema'];
                $array_values[] = $element['posting']['order_date'];
                $array_values[] = $element['posting']['posting_number'];
                ;

                $posting_number = $element['posting']['posting_number'];
                $posting_number_arr3 = explode('-', $posting_number);

                $array_values[] = count($posting_number_arr3) > 0 ? $posting_number_arr3[0] : '';
                $array_values[] = count($posting_number_arr3) > 1 ? $posting_number_arr3[1] : '';
                $array_values[] = count($posting_number_arr3) > 2 ? $posting_number_arr3[2] : '';

                $array_values[] = $element['posting']['warehouse_id'];
                $array_values[] = json_encode($element['items']);
                $array_values[] = json_encode($element['services']);
                $array_values[] = json_encode($element, true);

                $count += 1;

                $array_rows[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            }

            $sql .= implode(",\n", $array_rows);

            $pdo->prepare($sql)->execute($array_values);

            echo "<p style='color: green;'>Таблица заполнена успешно $tableName</p>";
        } catch (Throwable $exception) {
            echo "<p style='color: red;'>Исключение</p>";
            echo "<p style='color: red;'>$exception</p>";
            LoggerInTelegram::logCode($exception);
        }
    }
}
