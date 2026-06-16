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
            $request = $client->request('GET', $this->url);
            $document = new Document($this->url, true);

            $resultCheck["statusCode"] = $request->getStatusCode();
            
            $h1Elements = $document->find("h1");
            $resultCheck["h1"] = isset($h1Elements[0]) ? $h1Elements[0]->text() : null;
            
            $titleElements = $document->find("title");
            $resultCheck["title"] = isset($titleElements[0]) ? $titleElements[0]->text() : null;

            $descElements = $document->find('meta[name=description]');
            if (isset($descElements[0])) {
                $resultCheck["description"] = $descElements[0]->content;
            }
            
            $resultCheck["message"] = ["success", "Страница успешно проверена"];

        } catch (\GuzzleHttp\Exception\ClientException $e) { 
            $resultCheck["message"] = ["warning", "Проверка была выполнена успешно, но клиент ответил с ошибкой"];
            $resultCheck["statusCode"] = 403;
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $resultCheck["message"] = ["danger", "Произошла ошибка при проверке, не удалось подключиться"];
        } catch (\Exception $e) {
            $resultCheck["message"] = ["danger", "Произошла ошибка: " . $e->getMessage()];
        }
        
        return $resultCheck;
    }
}
