<?php
include_once "$HOME/env.php";
include_once "$HOME/_classes/LoggerInTelegram.class.php";
include_once "$HOME/_classes/OzonSeller__ProductList.class.php";

class OzonSeller__ProductInfo {
    public function fetchJson__getAllProducts() {
        $productList = (new OzonSeller__ProductList())->fetchJson__getAllProducts();
        $arr = [];
        foreach($productList as $element) {
            $offer_id = $element['offer_id'];
            $arr []= $this->fetchJson__getByOfferId($offer_id);
        }
        return $arr;
    }

    public function fetchJson__getByOfferId($offer_id) {
        return $this->fetchJson([
            'offer_id' => $offer_id,
        ]);
    }

    public function fetchJson($HTTP_DATA) {
        global $env;
        $URI = "/v2/product/info";
        $FETCH_URL = "https://api-seller.ozon.ru$URI";

        $jsonData = json_encode($HTTP_DATA);

        // Инициализируем cURL сессии
        $ch = curl_init($FETCH_URL);

        // Устанавливаем параметры cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);  // Устанавливаем метод POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Тело запроса

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        // Устанавливаем заголовки
        $ozonClientId = $env['ozon-client-id'];
        $ozonApiKey = $env['ozon-api-key'];

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Client-Id: $ozonClientId",
            "Api-Key: $ozonApiKey",
        ));

        // Передаем куки
        // curl_setopt($ch, CURLOPT_COOKIE, "Client-Id=$ozonClientId; Api-Key=$ozonApiKey");

        // Выполняем запрос и получаем ответ
        $response = curl_exec($ch);

        // Проверяем на наличие ошибок
        if (curl_errno($ch)) {
            $err = curl_error($ch);
            // Закрываем cURL сессию
            curl_close($ch);
            LoggerInTelegram::logCode($err);
            throw new Error("Fetch error: $err");
        } else {
            // Закрываем cURL сессию
            curl_close($ch);
            $jsonString = $response;
            $phpObject = json_decode($jsonString, true);
            return $phpObject;
        }
    }
}
