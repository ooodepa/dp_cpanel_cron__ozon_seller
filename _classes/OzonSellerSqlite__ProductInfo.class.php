<?php
include_once "$HOME/_classes/DatabaseConnect.class.php";
include_once "$HOME/_classes/LoggerInTelegram.class.php";
include_once "$HOME/_classes/OzonSeller__ProductInfo.class.php";

class OzonSellerSqlite__ProductInfo {
    private function getConnect() {
        LoggerInTelegram::log("Connect with pdo");
        return DatabaseConnect::getMysqlPdo();
    }

    private function getAllProducts() {
        return (new OzonSeller__ProductInfo())->fetchJson__getAllProducts();
    }

    function saveToDatabase() {
        try {
            $pdo = $this->getConnect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            echo "<p style='color: green;'>Успешное подключение к SQLite</p>";

            $products = $this->getAllProducts();

            $tableName = "OZON_CTL_ProductInfo";

            $sql = "DROP TABLE IF EXISTS $tableName";
            $pdo->prepare($sql)->execute();
            LoggerInTelegram::logCode($sql);
       
            echo "<p style='color: green;'>Таблица удалена успешно $tableName</p>";

            $sql = "CREATE TABLE $tableName (
                        id INT,
                        name TEXT,
                        offer_id TEXT,
                        barcode TEXT,
                        buybox_price TEXT,
                        category_id INT,
                        created_at TEXT,
                        images TEXT,
                        marketing_price REAL,
                        min_ozon_price TEXT,
                        old_price REAL,
                        premium_price TEXT,
                        price REAL,
                        recommended_price TEXT,
                        min_price TEXT,
                        sources TEXT,
                        stocks__coming INT,
                        stocks__present INT,
                        stocks__reserved INT,
                        errors TEXT,
                        vat REAL,
                        visible INT,
                        visibility_details__has_price INT,
                        visibility_details__has_stock INT,
                        visibility_details__active_product INT,
                        price_index REAL,

                        commissions TEXT,
                        
                        volume_weight REAL,
                        is_prepayment INT,
                        is_prepayment_allowed INT,
                        images360 TEXT,
                        color_image TEXT,
                        primary_image TEXT,

                        status TEXT,

                        state TEXT,
                        service_type TEXT,
                        fbo_sku INT,
                        fbs_sku INT,
                        currency_code TEXT,
                        is_kgt INT,

                        discounted_stocks__coming INT,
                        discounted_stocks__present INT,
                        discounted_stocks__reserved INT,

                        is_discounted INT,
                        has_discounted_item INT,
                        barcodes TEXT,
                        updated_at TEXT,
                        price_indexes TEXT,
                        sku INT,
                        description_category_id INT,
                        type_id INT,
                        is_archived INT,
                        is_autoarchived INT,

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
                        name,
                        offer_id,
                        barcode,
                        buybox_price,
                        category_id,
                        created_at,
                        images,
                        marketing_price,
                        min_ozon_price,
                        old_price,
                        premium_price,
                        price,
                        recommended_price,
                        min_price,
                        sources,
                        stocks__coming,
                        stocks__present,
                        stocks__reserved,
                        errors,
                        vat,
                        visible,
                        visibility_details__has_price,
                        visibility_details__has_stock,
                        visibility_details__active_product,
                        price_index,
                        commissions,
                        volume_weight,
                        is_prepayment,
                        is_prepayment_allowed,
                        images360,
                        color_image,
                        primary_image,
                        status,
                        state,
                        service_type,
                        fbo_sku,
                        fbs_sku,
                        currency_code,
                        is_kgt,
                        discounted_stocks__coming,
                        discounted_stocks__present,
                        discounted_stocks__reserved,
                        is_discounted,
                        has_discounted_item,
                        barcodes,
                        updated_at,
                        price_indexes,
                        sku,
                        description_category_id,
                        type_id,
                        is_archived,
                        is_autoarchived,
                        _raw_json,
                        _updated_at
                    )
                    VALUES
                    ";

            $array_rows = [];
            $array_values = [];
            $count = 0;
            foreach($products as $el) {
                $element = $el['result'];
                
                $array_values []= $element['id'];
                $array_values []= $element['name'];
                $array_values []= $element['offer_id'];
                $array_values []= $element['barcode'];
                $array_values []= $element['buybox_price'];
                $array_values []= $element['category_id'];
                $array_values []= $element['created_at'];
                $array_values []= implode("\n", $element['images']);
                $array_values []= $element['marketing_price'];
                $array_values []= $element['min_ozon_price'];
                $array_values []= $element['old_price'];
                $array_values []= $element['premium_price'];
                $array_values []= $element['price'];
                $array_values []= $element['recommended_price'];
                $array_values []= $element['min_price'];
                $array_values []= implode("\n", $element['sources']);
                $array_values []= $element['stocks']['coming'] ?? "";
                $array_values []= $element['stocks']['present'] ?? "";
                $array_values []= $element['stocks']['reserved'] ?? "";
                $array_values []= implode("\n", $element['errors']);
                $array_values []= $element['vat'];
                $array_values []= $element['visible'];
                $array_values []= $element['visibility_details']['has_price'] ? 1 : 0;
                $array_values []= $element['visibility_details']['has_stock'] ? 1 : 0;
                $array_values []= $element['visibility_details']['active_product'] ? 1 : 0;
                $array_values []= $element['price_index'];

                $array_values []= json_encode($element['commissions']);

                $array_values []= $element['volume_weight'];
                $array_values []= $element['is_prepayment'] ? 1 : 0;
                $array_values []= $element['is_prepayment_allowed'] ? 1 : 0;
                $array_values []= implode("\n", $element['images360']);
                $array_values []= $element['color_image'];
                $array_values []= $element['primary_image'];

                $array_values []= json_encode($element['status']);

                $array_values []= $element['state'];
                $array_values []= $element['service_type'];
                $array_values []= $element['fbo_sku'];
                $array_values []= $element['fbs_sku'];
                $array_values []= $element['currency_code'];
                $array_values []= $element['is_kgt'] ? 1 : 0;

                $array_values []= $element['discounted_stocks']['coming'] ?? "";
                $array_values []= $element['discounted_stocks']['present'] ?? "";
                $array_values []= $element['discounted_stocks']['reserved'] ?? "";

                $array_values []= $element['is_discounted'] ? 1 : 0;
                $array_values []= $element['has_discounted_item'] ? 1 : 0;
                $array_values []= implode("\n", $element['barcodes']);
                $array_values []= $element['updated_at'];

                $array_values []= json_encode($element['price_indexes']);
               
                $array_values []= $element['sku'];
                $array_values []= $element['description_category_id'];
                $array_values []= $element['type_id'];
                $array_values []= $element['is_archived'] ? 1 : 0;
                $array_values []= $element['is_autoarchived'] ? 1 : 0;
                $array_values []= json_encode($element);

                $count += 1;

                $array_rows []= "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            }

            $sql .= implode(",\n", $array_rows);

            LoggerInTelegram::logCode($sql);
            $pdo->prepare($sql)->execute($array_values);

            echo "<p style='color: green;'>Таблица заполнена успешно $tableName</p>";
        }
        catch(Throwable $exception) {
            echo "<p style='color: red;'>Исключение</p>";
            echo "<p style='color: red;'>$exception</p>";
            LoggerInTelegram::logCode("$exception");
        }
    }
}
