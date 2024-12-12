<?php

$HOME = strlen($_SERVER['DOCUMENT_ROOT']) != 0 ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['ENV_CRON_CPANEL_OZON_SELLER__HOME'];

include_once "$HOME/env.php";
include_once "$HOME/_classes/LoggerInTelegram.class.php";

class OzonSeller__ProductList {
    public function fetchJson__getAllProducts() {
        return array_merge(
            $this->fetchJson__getNotArchivedProducts()['result']['items'],
            $this->fetchJson__getArchivedProducts()['result']['items']
        );
    }

    public function fetchJson__getNotArchivedProducts() {
        return $this->fetchJson([
            'limit' => 10000,
            'filter' => [
                'visibility' => 'ALL',
            ],
        ]);
    }

    public function fetchJson__getArchivedProducts() {
        return $this->fetchJson([
            'limit' => 10000,
            'filter' => [
                'visibility' => 'ARCHIVED',
            ],
        ]);
    }

    public function fetchJson($HTTP_DATA) {
        global $env;
        $URI = "/v2/product/list";
        $FETCH_URL = "https://api-seller.ozon.ru$URI";
        LoggerInTelegram::log($FETCH_URL);

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
            LoggerInTelegram::logCode($jsonString);
            $phpObject = json_decode($jsonString, true);
            return $phpObject;
        }
    }
}
