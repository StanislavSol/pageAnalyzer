<?php

namespace App;

class Validator
{

    public function getErrors($url)
    {
        $validation = new Valitron\Validator(array('URL' => $url));
        $validation->rule('url', 'URL');
        return $validation->errors();
    }
}
