<?php
include "$HOME/env.php";

class OzonSeller__PostingFbsActList {
    private function getData($startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end->modify('last day of this month'); // Получаем последний день месяца для конечной даты
        $data = [];
    
        while ($start <= $end) {
            $startOfMonth = $start->format('Y-m-01');
            $endOfMonth = $start->format('Y-m-t');
    
            $data[] = [
                'start' => $startOfMonth,
                'end' => $endOfMonth
            ];
    
            // Переход к следующему месяцу
            $start->modify('first day of next month');
        }

        echo "<pre>";

        return $data;
    }

    public function getAll() {
        $startDate = '2024-01-10';
        $endDate = (new DateTime('tomorrow'))->format('Y-m-d');

        $result = $this->getData($startDate, $endDate);

        $resultArray = [];
        for ($i = 0; $i < count($result); $i++) {
            $data = $this->fetchJson__getPostingFbsActListOnPeriod($result[$i]['start'], $result[$i]['end']);
            $arr = $data['result'];
            for ($j = 0; $j < count($arr); $j++) {
                $resultArray []= $arr[$j];
            }
        }

        return $resultArray;
    }

    public function fetchJson__getPostingFbsActListOnPeriod($dateFrom, $dateTo) {        
        return $this->fetchJson([
            "filter" => [
                "date_from" => $dateFrom,
                "date_to" => $dateTo,
            ],
            "limit" => 50,
        ]);
    }

    public function fetchJson($HTTP_DATA) {
        global $env;
        $URI = "/v2/posting/fbs/act/list";
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
            'Content-Type: application/json', // Указываем, что передаем данные в формате JSON
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
            throw new Error("Fetch error: $err");
        } else {
            // Закрываем cURL сессию
            curl_close($ch);
            $jsonString = $response;
            $phpObject = json_decode($jsonString, true);
            return $phpObject;
        }
    }

    public function fetchJson__downloadPdf($id, $filename) {
        global $env;
        $URI = "/v2/posting/fbs/act/get-pdf";
        $FETCH_URL = "https://api-seller.ozon.ru$URI";

        $jsonData = json_encode([
            'id' => $id,
        ]);

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
            throw new Error("Fetch error: $err");
        } else {
            // Закрываем cURL сессию
            curl_close($ch);
            // $jsonString = $response;
            // $phpObject = json_decode($jsonString, true);
            // return $phpObject;

            // $HOME = $_SERVER['DOCUMENT_ROOT'];
            // $filePath = "$HOME/reports/document.pdf";
            // echo $filePath;
            // file_put_contents($filePath, $response);

            // Устанавливаем заголовки для отправки PDF в браузер

            header('Content-Type: application/pdf');
            header("Content-Disposition: inline; filename=\"$filename\"");
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');

            // Отправляем PDF в браузер
            echo $response;
        }
    }
}