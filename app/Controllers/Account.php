<?php

namespace App\Controllers;

use App\Models\UserDataModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class Account extends BaseController
{
    use ResponseTrait;

    protected $user;
    protected $userData;
    protected $input;

    function __construct()
    {
        $this->user = new UserModel();
        $this->userData = new UserDataModel();
    }

    public function register()
    {

        $getData = $this->request->getJSON();
        $userID = null;
        $userID = $this->user->insert((array)($getData->data));

        if ($userID === false) {
            $resp = [
                'status' => 201,
                'messages' => $this->user->errors()
            ];
            return $this->respondCreated($resp);
        } else {

            $this->userData->skipValidation();
            if ($this->userData->insert(['idUser' => $userID])) {
                $resp = [
                    'status' => 200,
                    'messages' => "Dodano konto"
                ];
            }else {
                $resp = [
                    'status' => 201,
                    'messages' => $this->userData->errors()
                ];
            }

            return $this->respondCreated($resp);
        }

        return view('welcome_message');
    }

    public function login()
    {
        $getData = $this->request->getJSON();

        $user = $this->user->findAll();

        return $this->respondCreated($user);
    }
}
