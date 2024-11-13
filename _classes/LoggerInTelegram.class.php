<?php

class LoggerInTelegram {
    static function fetchJson($HTTP_DATA) {
        global $env;
        $telegramBotToken = $env['telegram-bot-token'];
        $URI = "/bot$telegramBotToken/sendMessage";
        $FETCH_URL = "https://api.telegram.org$URI";
        echo "$FETCH_URL\n";

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
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json', // Указываем, что передаем данные в формате JSON
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

    static function log($message) {
        try {
            global $env;
            $telegramBotChatId = $env['telegram-bot-chat-id'];
            $date = date('Y-m-d h:i:s');
            LoggerInTelegram::fetchJson([
                "chat_id" => "$telegramBotChatId",
                "text" => "$date\n$message",
                "parse_mode" => "Markdown",
                "disable_web_page_preview" => true,
                "disable_notification" => true,
                // "reply_to_message_id" => 0,
                // "reply_markup" => [],
            ]);
        }
        catch(Throwable $exception) {
            echo $exception;
        }
    }

    static function logCode($message) {
        LoggerInTelegram::log("```\n$message\n```");
    }
}