<?php

namespace App;

use Valitron\Validator;

class Validation
{

    public function getErrors($url)
    {
        $validation = new Validator(array('URL' => $url));
        $validation->rule('required', 'URL')->message('URL не должен быть пустым');
        $validation->validate();
        return $validation->errors();
    }
}
