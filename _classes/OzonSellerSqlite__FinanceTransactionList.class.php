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
        return (new OzonSeller__FinanceTransactionList())->getAllTransformed();
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
                        posting TEXT,
                        _posting__delivery_schema TEXT,
                        _posting__order_date REAL,
                        _posting__posting_number TEXT,
                        _posting__posting_number_1 TEXT,
                        _posting__posting_number_2 TEXT,
                        _posting__posting_number_3 TEXT,
                        _posting__warehouse_id REAL,
                        items TEXT,
                        services TEXT,
                        _MarketplaceRedistributionOfAcquiringOperation REAL,
                        _MarketplaceServiceItemDropoffPVZ REAL,
                        _MarketplaceServiceItemDirectFlowTrans REAL,
                        _MarketplaceServiceItemDelivToCustomer REAL,
                        _MarketplaceServiceItemDirectFlowLogistic REAL,
                        _MarketplaceServiceItemReturnAfterDelivToCustomer REAL, 
                        _MarketplaceServiceItemReturnFlowTrans REAL,
                        _MarketplaceServiceItemReturnFlowLogistic REAL,
                        _MarketplaceServiceItemRedistributionReturnsPVZ REAL,
                        _MarketplaceServiceItemReturnNotDelivToCustomer REAL,
                        _MarketplaceServiceItemReturnPartGoodsCustomer REAL,
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
                        posting,
                        _posting__delivery_schema,
                        _posting__order_date,
                        _posting__posting_number,
                        _posting__posting_number_1,
                        _posting__posting_number_2,
                        _posting__posting_number_3,
                        _posting__warehouse_id,
                        items,
                        services,
                        _MarketplaceRedistributionOfAcquiringOperation,
                        _MarketplaceServiceItemDropoffPVZ,
                        _MarketplaceServiceItemDirectFlowTrans,
                        _MarketplaceServiceItemDelivToCustomer,
                        _MarketplaceServiceItemDirectFlowLogistic,
                        _MarketplaceServiceItemReturnAfterDelivToCustomer, 
                        _MarketplaceServiceItemReturnFlowTrans,
                        _MarketplaceServiceItemReturnFlowLogistic,
                        _MarketplaceServiceItemRedistributionReturnsPVZ,
                        _MarketplaceServiceItemReturnNotDelivToCustomer,
                        _MarketplaceServiceItemReturnPartGoodsCustomer,
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
                $array_values[] = $element['posting'];
                $array_values[] = $element['_posting__delivery_schema'];
                $array_values[] = $element['_posting__order_date'];
                $array_values[] = $element['_posting__posting_number'];
                $array_values[] = $element['_posting__posting_number_1'];
                $array_values[] = $element['_posting__posting_number_2'];
                $array_values[] = $element['_posting__posting_number_3'];
                $array_values[] = $element['_posting__warehouse_id'];
                $array_values[] = $element['items'];
                $array_values[] = $element['services'];
                $array_values[] = $element['_MarketplaceRedistributionOfAcquiringOperation'];
                $array_values[] = $element['_MarketplaceServiceItemDropoffPVZ'];
                $array_values[] = $element['_MarketplaceServiceItemDirectFlowTrans'];
                $array_values[] = $element['_MarketplaceServiceItemDelivToCustomer'];
                $array_values[] = $element['_MarketplaceServiceItemDirectFlowLogistic'];
                $array_values[] = $element['_MarketplaceServiceItemReturnAfterDelivToCustomer'];
                $array_values[] = $element['_MarketplaceServiceItemReturnFlowTrans'];
                $array_values[] = $element['_MarketplaceServiceItemReturnFlowLogistic'];
                $array_values[] = $element['_MarketplaceServiceItemRedistributionReturnsPVZ'];
                $array_values[] = $element['_MarketplaceServiceItemReturnNotDelivToCustomer'];
                $array_values[] = $element['_MarketplaceServiceItemReturnPartGoodsCustomer'];
                $array_values[] = $element['_raw_json'];

                $count += 1;

                $array_rows[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
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
