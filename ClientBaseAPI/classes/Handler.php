<?php
/**
 *    Client Base API Handler ver. 0.5
 *    Copyright (C) 2020  Dmitry Shumilin (dr.noisier@yandex.ru)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
namespace ClientBaseAPI;

class Handler implements HandlerInterface
{

    private $url;
    private $login;
    private $key;

    public function __construct(string $url, string $login, string $key)
    {
        
        $this->url = $url;
        $this->login = $login;
        $this->key = $key;

    }

    protected function sendCommandToServer(string $url, array $command)
    {

        // Кодируем команду в JSON и создаём cURL-подключение к КБ.
        $data = json_encode($command);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-type: application/json',
            'Content-length: '.strlen($data)
        ]);

        // Запускаем подключение и декодируем полученный JSON-ответ.
        $answer = curl_exec($ch);
        $result = json_decode($answer, true);

        // В случае успеха декодирования из JSON возвращаем полученный массив,
        // в противном случае возвращаем полученный ответ как есть.
        if (is_array($result)) return $result;
        else return $answer;

    }

    protected function auth()
    {

        // Получаем соль. Запрашиваем для будущего ID доступа время жизни 60 секунд.
        $request = $this->sendCommandToServer($this->url.'api/auth/request', ['v' => '1.0', 'login' => $this->login, 'life_time' => 60]);

        if ($request['code'] === 0) {

            // Добавляем соль перед ключом, шифруем в MD5 и посылаем запрос ID доступа.
            $auth = $this->sendCommandToServer($this->url.'api/auth/auth', ['v' => '1.0', 'login' => $this->login, 'hash' => md5($request['salt'].$this->key)]);

            if ($auth['code'] === 0) $result = $auth['access_id'];
            else $result = false;

        } else $result = false;

        // Возвращаем ID доступа в случае успеха и false в случае провала.
        return $result;

    }

    public function dataCreate(array $command)
    {

        return $this->crud('create', $command);

    }

    public function dataRead(array $command)
    {

        return $this->crud('read', $command);
        
    }

    public function dataUpdate(array $command)
    {

        return $this->crud('update', $command);

    }

    public function dataDelete(array $command)
    {

        return $this->crud('delete', $command);

    }

    public function crud(string $action, array $command)
    {

        // Каждый экшн имеет отдельный API-маршрут.
        if ($action === 'create' || $action === 'read' || $action === 'update' || $action === 'delete') {
            
            $request_uri = 'api/data/'.$action;

            // Внутри команды обязательно должен быть ID доступа.
            // Один ID доступа можно использовать неограниченное количество раз в течение его времени жизни,
            // но для верности всё равно запрашиваем каждый раз новый. Ничто не мешает нам так делать.
            $access_id = $this->auth();

            if ($access_id) {

                $command['access_id'] = $access_id;

                $result = $this->sendCommandToServer($this->url.$request_uri, $command);

            } else $result = false;
        
        } else $result = false;

        // В случае успеха возвращаем ответ, полученный от КБ, в случае неудачи — false.
        return $result;

    }

}
