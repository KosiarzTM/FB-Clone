<?php

namespace App\Controllers;

use App\Models\UserModel;

class Account extends BaseController {

    protected $user;
    function __construct()
    {
        $this->user = new UserModel();
    }

    public function register() {
        $data = [
            'email' => 'd.vader@theempire.com',
            'password'    => 'ZAQ!',
        ];
        
        $this->user->insert($data);
        // echo '<pre>';
        // print_r($this->UserM);
        // echo '</pre>';
        return view('welcome_message');
    }
}