<?php

class Amocrm {
    private $name;
    private $email;
    private $phone;
    private $price;
    private $access_token;

    // Конструктор для инициализации значений
    public function __construct() {
        $this->name = $_POST['name'] ?? '';
        $this->email = $_POST['email'] ?? '';
        $this->phone = $_POST['phone'] ?? '';
        $this->price = $_POST['price'] ?? '';
        
        // Токен доступа может быть передан в качестве параметра или загружен из переменной окружения
        $this->access_token = getenv('AMOCRM_ACCESS_TOKEN') ?: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjQyMzhhYjgzYzg2ZTNmZWIwYmQ3OTlkMGI0YjczY2IyMTcxM2FlNWYwNjczOTBhZTQyNTQyOTJkNjJhYzhjYmE3ODlmM2U2MWM3YTI2NGQ5In0.eyJhdWQiOiI2NTU1ODE5MS1jZWJiLTQ1MDYtOTJhZi0zNWQxZmNmMjM3NmMiLCJqdGkiOiI0MjM4YWI4M2M4NmUzZmViMGJkNzk5ZDBiNGI3M2NiMjE3MTNhZTVmMDY3MzkwYWU0MjU0MjkyZDYyYWM4Y2JhNzg5ZjNlNjFjN2EyNjRkOSIsImlhdCI6MTcyOTg1NDM5NywibmJmIjoxNzI5ODU0Mzk3LCJleHAiOjE3NTkxOTA0MDAsInN1YiI6IjExNjkyNDA2IiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMyMDI3NTgyLCJiYXNlX2RvbWFpbiI6ImFtb2NybS5ydSIsInZlcnNpb24iOjIsInNjb3BlcyI6WyJjcm0iLCJmaWxlcyIsImZpbGVzX2RlbGV0ZSIsIm5vdGlmaWNhdGlvbnMiLCJwdXNoX25vdGlmaWNhdGlvbnMiXSwiaGFzaF91dWlkIjoiOGNmYzk3NjEtNDU3MS00MDFjLTg3OTYtMmM3OGNmMWJjYjc0IiwiYXBpX2RvbWFpbiI6ImFwaS1iLmFtb2NybS5ydSJ9.dQL8pbD-LpJ10rM-qInX1L0fbHjjiMqljPnqLqQYQNJk0g7jDGv415VVIDiva38L9zcN-1hMjBI-E5Vj2Xx2zjrK85-YRrxG532QrQ0Krlq68XCOBYxoSs1lE87ctdiMai5JJgMqjFxy75V1bBGqE1rqgLoQwzqndMRHHnsEtYwCnsy9s38MIzUyhjP0VF_rjai9O8_Izs06s0eoQ8FbPFpMD-WEaXZ1CBuL_ifEugqiRwWkp0p6nxXIrdB6UBzTLzWeYjTqiiLXWaRKXl_MsFgPKfNYsUb0nBCKttrp0alY1HEBKHrb66jD-q4LEhed8G5Pkbuur7N10KVn-Uvszw';
    }

    public function send() {
        // Валидация данных
        if (empty($this->name) || empty($this->email) || empty($this->phone)) {
            throw new Exception('Не все обязательные поля заполнены.');
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Некорректный формат email.');
        }

        $subdomain = 'dles13';
        $link = 'https://' . $subdomain . '.amocrm.ru/api/v4/leads/complex';
        
        // Подготовка данных для отправки
       $data = [
    [
        "name" => "Новая сделка",
        "price" => intval($this->price),
        "_embedded" => [
            "contacts" => [
                [
                    "first_name" => $this->name,
                    "created_at" => time(),
                    "updated_by" => 0,
                    "custom_fields_values" => [
                        [
                            "field_id" => 429401,
                            "values" => [
                                [
                                    "value" => $this->email,
                                ],
                            ],
                        ],
                        [
                            "field_id" => 429399,
                            "values" => [
                                [
                                    "value" => $this->phone,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        "created_at" => time(),
    ],
];

        // Использование cURL для отправки данных
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->access_token,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code != 200) {
            throw new Exception('Ошибка при отправке данных: ' . $response);
        }

        curl_close($ch);
        
        return json_decode($response, true);
    }
	
	
	
}
$b = new Amocrm;
$b->send();
?>