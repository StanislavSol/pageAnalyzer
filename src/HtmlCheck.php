<?php

namespace App;

class HtmlCheck
{ 
    private $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function getStatusCode()
    {
        $client = new \GuzzleHttp\Client();
        try
        {
            $result = $client->request('GET',  $this->url);
            $statusCode = $result->getStatusCode();
            $message = ["succes", "Страница успешно проверена"];

        } catch (\GuzzleHttp\Exception\ClientException $e)
        { 
            $message = ["warning", "Страница проверена, но доступ к запрашиваемой странице запрещен"];
            $statusCode = 403;
        } catch (\GuzzleHttp\Exception\ConnectException $e)
        {
            $message = ["danger", "Во время проверки произошла ошибка"];
            $statusCode = null;
        }

        return [$statusCode, $message];

    }
}
