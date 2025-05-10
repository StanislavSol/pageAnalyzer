<?php

namespace App;

use DiDom\Document;

class HtmlCheck
{ 
    private $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function getInfoUrl()
    {
        $client = new \GuzzleHttp\Client();
        $resultCheck = [
            "statusCode" => null,
            "h1" => null,
            "title" => null,
            "description" => null
        ];
        try {
            $request = $client->request('GET',  $this->url);
            $document = new Document($this->url, true);

            $resultCheck["statusCode"] = $request->getStatusCode();
            $resultCheck["h1"] = $document->find("h1")[0]->text();
            $resultCheck["title"] = $document->find("title")[0]->text();

            if ($document->has('meta[name=description]')) {
                $resultCheck["description"] = $document->find('meta[name=description]')[0]->content;
            }
            $resultCheck["message"] = ["success", "Страница успешно проверена"];

        } catch (\GuzzleHttp\Exception\ClientException $e) { 
            $resultCheck["message"] = ["warning", "Проверка была выполнена успешно, но сервер ответил с ошибкой"];
            $resultCheck["statusCode"] = 403;
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $resultCheck["message"] = ["danger", "Произошла ошибка при проверке, не удалось подключиться"];
        }

        return $resultCheck;

    }

}
