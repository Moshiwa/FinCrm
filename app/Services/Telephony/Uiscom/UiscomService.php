<?php

namespace App\Services\Telephony\Uiscom;

use App\Services\Dadata\DadataService;
use GuzzleHttp\Client;
//ToDo Дописать Необходимо использовать softphone uis
class UiscomService
{
    private UiscomClient $client;
    protected string $error = '';

    public function __construct()
    {
        $this->client = new UiscomClient();
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function cleanPhone($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (empty($phone) || strlen($phone) != 11) {
            $this->error = 'Неверный формат номера телефона';
            return '';
        }

        return $phone;
    }

    public function authManager($login, $password)
    {
        $params = [
            'login' => $login,
            'password' => $password
        ];
        $client = new Client();
        $response = $client->post('https://my.uiscom.ru/sup/auth/login', [
            'body' => json_encode($params)
        ]);

        $response = $response->getBody()->getContents();
        $data = json_decode($response, true);

        return $data['data']['employee_id'] ?? '';
    }

    public function call($phone)
    {
        $params = [
            'access_token' => backpack_user()->uiscom_token,
            'first_call' => 'employee',
            'virtual_phone_number' => backpack_user()->uiscom_virtual_number,
            'contact' => $phone,
            'employee' => [
                'id' => (int)backpack_user()->uiscom_employee_id
            ]
        ];

        $result = $this->client->post('start.employee_call', $params);

        if (isset($result['error'])) {
            $this->error = $this->errorsList($result['error']);
        }

        return $result['result']['data']['call_session_id'] ?? '';
    }

    private function errorsList(array $error)
    {

        $errors = [
            'Invalid Request The JSON sent is not a valid Request object' => 'Неверный запрос. Отправленный JSON не является допустимым объектом запроса.',
            'Access token has been expired' => 'Срок действия токена доступа истек.',
            'Access token has been blocked' => 'Токен доступа заблокирован.',
            'Access token is invalid' => 'Токен доступа недействителен.',
            'Login or password is wrong' => 'Логин или пароль неверный.',
            'Your account has been disabled, contact the support service' => 'Ваш аккаунт отключен, обратитесь в службу поддержки.',
            'Call session not found' => 'Сеанс вызова не найден.',
            'Internal error, contact the support service' => 'Внутренняя ошибка, обратитесь в службу поддержки Uis.',
            'Data supplied is of wrong type' => 'Предоставленные данные имеют неправильный тип.',
            'Permission denied' => 'Доступ запрещен.',
            'Invalid JSON was received by the server' => 'Сервер получил неверный JSON.',
            'Batch operations not supported' => 'Пакетные операции не поддерживаются.',
            'Notifications not supported' => 'Уведомления не поддерживаются.',
            'The required parameter has been missed' => 'Не указан нужный параметр.',
            'Invalid parameter value' => 'Недопустимое значение параметра.',
        ];

        return $errors[$error['message']] ?? $error['message'];
    }
}
