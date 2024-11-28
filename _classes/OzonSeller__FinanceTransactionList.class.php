<?php
include "$HOME/env.php";

class OzonSeller__FinanceTransactionList {
 
    private function getData($startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end->modify('last day of this month'); // Получаем последний день месяца для конечной даты
        $data = [];
    
        while ($start <= $end) {
            $startOfMonth = $start->format('Y-m-01\T00:00:00.000\Z');
            $endOfMonth = $start->format('Y-m-t\T23:59:59.999\Z');
    
            $data[] = [
                'start' => $startOfMonth,
                'end' => $endOfMonth
            ];
    
            // Переход к следующему месяцу
            $start->modify('first day of next month');
        }
    
        return $data;
    }

    public function getAll() {
        $startDate = '2024-01-10';
        $endDate = (new DateTime('tomorrow'))->format('Y-m-d');

        $result = $this->getData($startDate, $endDate);

        $resultArray = [];
        for ($i = 0; $i < count($result); $i++) {
            $data = $this->fetchJson__getTransactionsOnPeriod($result[$i]['start'], $result[$i]['end']);
            $arr = $data['result']['operations'];
            for ($j = 0; $j < count($arr); $j++) {
                $resultArray []= $arr[$j];
            }
        }

        return $resultArray;
    }

    public function getAllTransformed() {
        return $this->transformArray($this->getAll());
    }

    public function fetchJson__getTransactionsOnPeriod($dateFrom, $dateTo) {        
        return $this->fetchJson([
            "filter" => [
                "date" => [
                    "from" => $dateFrom,
                    "to" => $dateTo,
                ],
                "operation_type" => [],
                "posting_number" => "",
                "transaction_type" => "all",
            ],
            "page" => 1,
            "page_size" => 1000,
        ]);
    }

    public function fetchJson($HTTP_DATA) {
        global $env;
        $URI = "/v3/finance/transaction/list";
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
            throw new Error("Fetch error: $err");
        } else {
            // Закрываем cURL сессию
            curl_close($ch);
            $jsonString = $response;
            $phpObject = json_decode($jsonString, true);
            return $phpObject;
        }
    }

    private function transformArray($arr) {
        $result_array = [];

        for ($i = 0; $i < count($arr); $i++) {
            $element = $arr[$i];
            $MarketplaceRedistributionOfAcquiringOperation = 0;
            $MarketplaceServiceItemDropoffPVZ = 0;
            $MarketplaceServiceItemDirectFlowTrans = 0;
            $MarketplaceServiceItemDelivToCustomer = 0;
            $MarketplaceServiceItemDirectFlowLogistic = 0;
            $MarketplaceServiceItemReturnAfterDelivToCustomer = 0;
            $MarketplaceServiceItemReturnFlowTrans = 0;
            $MarketplaceServiceItemReturnFlowLogistic = 0;
            $MarketplaceServiceItemRedistributionReturnsPVZ = 0;
            $MarketplaceServiceItemReturnNotDelivToCustomer = 0;
            $MarketplaceServiceItemReturnPartGoodsCustomer = 0;

            $services = $element['services'];
            $services_length = count($services);

            for ($j = 0; $j < $services_length; $j++) {
                if (strcmp($services[$j]['name'], 'MarketplaceRedistributionOfAcquiringOperation') == 0) {
                    $MarketplaceRedistributionOfAcquiringOperation = $services[$j]['price'];
                    break;
                }
            }

            for ($j = 0; $j < $services_length; $j++) {
                if (strcmp($services[$j]['name'], 'MarketplaceServiceItemDropoffPVZ') == 0) {
                    $MarketplaceServiceItemDropoffPVZ = $services[$j]['price'];
                    break;
                }
            }

            for ($j = 0; $j < $services_length; $j++) {
                if (strcmp($services[$j]['name'], 'MarketplaceServiceItemDirectFlowTrans') == 0) {
                    $MarketplaceServiceItemDirectFlowTrans = $services[$j]['price'];
                    break;
                }
            }

            for ($j = 0; $j < $services_length; $j++) {
                if (strcmp($services[$j]['name'], 'MarketplaceServiceItemDelivToCustomer') == 0) {
                    $MarketplaceServiceItemDelivToCustomer = $services[$j]['price'];
                    break;
                }
            }

            for ($j = 0; $j < $services_length; $j++) {
                if (strcmp($services[$j]['name'], 'MarketplaceServiceItemDirectFlowLogistic') == 0) {
                    $MarketplaceServiceItemDirectFlowLogistic = $services[$j]['price'];
                    break;
                }
            }

            for ($j = 0; $j < $services_length; $j++) {
                if (strcmp($services[$j]['name'], 'MarketplaceServiceItemReturnAfterDelivToCustomer') == 0) {
                    $MarketplaceServiceItemReturnAfterDelivToCustomer = $services[$j]['price'];
                    break;
                }
            }

            for ($j = 0; $j < $services_length; $j++) {
                if (strcmp($services[$j]['name'], 'MarketplaceServiceItemReturnFlowTrans') == 0) {
                    $MarketplaceServiceItemReturnFlowTrans = $services[$j]['price'];
                    break;
                }
            }

            for ($j = 0; $j < $services_length; $j++) {
                if (strcmp($services[$j]['name'], 'MarketplaceServiceItemReturnFlowLogistic') == 0) {
                    $MarketplaceServiceItemReturnFlowLogistic = $services[$j]['price'];
                    break;
                }
            }

            for ($j = 0; $j < $services_length; $j++) {
                if (strcmp($services[$j]['name'], 'MarketplaceServiceItemRedistributionReturnsPVZ') == 0) {
                    $MarketplaceServiceItemRedistributionReturnsPVZ = $services[$j]['price'];
                    break;
                }
            }

            for ($j = 0; $j < $services_length; $j++) {
                if (strcmp($services[$j]['name'], 'MarketplaceServiceItemReturnNotDelivToCustomer') == 0) {
                    $MarketplaceServiceItemReturnNotDelivToCustomer = $services[$j]['price'];
                    break;
                }
            }

            for ($j = 0; $j < $services_length; $j++) {
                if (strcmp($services[$j]['name'], 'MarketplaceServiceItemReturnPartGoodsCustomer') == 0) {
                    $MarketplaceServiceItemReturnPartGoodsCustomer = $services[$j]['price'];
                    break;
                }
            }

            $posting_number = $element['posting']['posting_number'];
            $posting_number_arr3 = explode('-', $posting_number);
            $posting_number_1 = count($posting_number_arr3) > 0 ? $posting_number_arr3[0] : '';
            $posting_number_2 = count($posting_number_arr3) > 1 ? $posting_number_arr3[1] : '';
            $posting_number_3 = count($posting_number_arr3) > 2 ? $posting_number_arr3[2] : '';

            $result_array []= [
                'operation_id' => $element['operation_id'],
                'operation_type' => $element['operation_type'],
                'operation_date' => $element['operation_date'],
                'operation_type_name' => $element['operation_type_name'],
                'delivery_charge' => $element['delivery_charge'],
                'return_delivery_charge' => $element['return_delivery_charge'],
                'accruals_for_sale' => $element['accruals_for_sale'],
                'sale_commission' => $element['sale_commission'],
                'amount' => $element['amount'],
                'type' => $element['type'],
                'posting' => json_encode($element['posting'], true),
                '_posting__delivery_schema' => $element['posting']['delivery_schema'],
                '_posting__order_date' => $element['posting']['order_date'],
                '_posting__posting_number' => $element['posting']['posting_number'],
                '_posting__posting_number_1' => $posting_number_1,
                '_posting__posting_number_2' => $posting_number_2,
                '_posting__posting_number_3' => $posting_number_3,
                '_posting__warehouse_id' => $element['posting']['warehouse_id'],
                'items' => json_encode($element['items'], true),
                'services' => json_encode($element['services'], true),
                '_MarketplaceRedistributionOfAcquiringOperation' => $MarketplaceRedistributionOfAcquiringOperation,
                '_MarketplaceServiceItemDropoffPVZ' => $MarketplaceServiceItemDropoffPVZ,
                '_MarketplaceServiceItemDirectFlowTrans' => $MarketplaceServiceItemDirectFlowTrans,
                '_MarketplaceServiceItemDelivToCustomer' => $MarketplaceServiceItemDelivToCustomer,
                '_MarketplaceServiceItemDirectFlowLogistic' => $MarketplaceServiceItemDirectFlowLogistic,
                '_MarketplaceServiceItemReturnAfterDelivToCustomer' => $MarketplaceServiceItemReturnAfterDelivToCustomer,
                '_MarketplaceServiceItemReturnFlowTrans' => $MarketplaceServiceItemReturnFlowTrans,
                '_MarketplaceServiceItemReturnFlowLogistic' => $MarketplaceServiceItemReturnFlowLogistic,
                '_MarketplaceServiceItemRedistributionReturnsPVZ' => $MarketplaceServiceItemRedistributionReturnsPVZ,
                '_MarketplaceServiceItemReturnNotDelivToCustomer' => $MarketplaceServiceItemReturnNotDelivToCustomer,
                '_MarketplaceServiceItemReturnPartGoodsCustomer' => $MarketplaceServiceItemReturnPartGoodsCustomer,
                '_raw_json' => json_encode($element['services'], true),
            ];
        }

        return $result_array;
    }
}
