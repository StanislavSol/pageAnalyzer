<?php

namespace App;

class HtmlCheck
{ 
    public function getStatusCode($url)
    {
        $client = new \GuzzleHttp\Client();
        $result = $client->request('GET',  $url);

        return $result->getStatusCode();
    }
}
