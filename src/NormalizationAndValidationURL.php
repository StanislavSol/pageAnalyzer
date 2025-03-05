<?php

namespace App;

use Valitron\Validator;

class NormalizationAndValidationURL
{
    private string $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function getUnparseUrl()
    {
        $parsedUrl = parse_url($this->url);
        $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $resultUrl = "{$scheme}{$host}";
        
        return $resultUrl;
    }

    public function getErrors()
    {
        $validation = new Validator(array('URL' => $this->url));
        $validation->rule('required', 'URL')->message('URL не должен быть пустым');
        $validation->rule('url', 'URL')->message('Неккоректный URL');
        $validation->validate();
        return $validation->errors();
    }

}
