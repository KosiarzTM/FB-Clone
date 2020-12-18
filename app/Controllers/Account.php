<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\UserDataModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use ResponseHelper;

class Account extends BaseController
{
    use ResponseTrait;

    protected $user;
    protected $userData;
    protected $account;
    protected $db;

    function __construct()
    {   
        $this->user = new UserModel();
        $this->userData = new UserDataModel();
        
        $db = db_connect();
        $this->account = new AccountModel($db);

        helper("response_helper");
    }

    public function register()
    {

        $getData = $this->request->getJSON();

        $userID = null;
        $registerData = (array) $getData->data;
        $registerData['registerDate'] = time();

        $userID = $this->user->insert($registerData);

        if ($userID === false) {
            $resp = ResponseHelper::responseContent(ResponseHelper::ERROR, "", [], $this->user->errors());
        } else {

            $this->userData->skipValidation();

            if ($this->userData->insert(['idUser' => $userID])) {
                $resp = ResponseHelper::responseContent(ResponseHelper::SUCCESS, "Dodano konto");
            } else {
                $resp = ResponseHelper::responseContent(ResponseHelper::ERROR, "", [], $this->user->errors());
            }
        }

        return $this->respondCreated($resp);
    }

    public function login()
    {
        $getData = $this->request->getJSON();
        $getData = $getData->data;

        $user = $this->user->where('email', $getData->email)->find();
        $user = $user[0];

        if ($user && password_verify($getData->password, $user['password'])) {
            

            $token = self::createToken();
            $token['idUser'] = $user['idUser'];
            $this->user->save($token);
            
            $accountData = $this->account->getAccount($user['idUser']);

            $resp = ResponseHelper::responseContent(ResponseHelper::SUCCESS, "Zalogowano ponyślnie", $accountData);
        } else {
            $resp = ResponseHelper::responseContent(ResponseHelper::ERROR, "Niepoprawne hasło lub login");
        }
        return $this->respondCreated($resp);
    }

    public function update() {
        $getData = $this->request->getJSON();
        $getData = $getData->data;
        
        $mainData = (array)$getData->mainData;
        $personalData = (array)$getData->personalData;

        $userRules = $this->user->validationRules;
        $userDataRules = $this->userData->validationRules;

        /*
            array_intersect_key - zwraca tablicę zasad walidacji tylko dla użytych w aktualizacji pól
        */
        $userRules = array_intersect_key($userRules,$mainData);
        if(key_exists('email',$userRules))
            $userRules['email'] = 'valid_email';

        $userDataRules = array_intersect_key($userDataRules,$personalData);


        $this->user->setValidationRules($userRules);
        $this->userData->setValidationRules($userDataRules);

        $updateMain = $this->user->where('idUser',$mainData['idUser'])->set($mainData)->update();
        $updatePersonal = $this->userData->where('idUser',$mainData['idUser'])->set($personalData)->update();

        if($updateMain !== false || $updatePersonal !== false) {
            $resp = ResponseHelper::responseContent(ResponseHelper::SUCCESS, "Zmieniono dane ponyślnie", $getData);
        }else {
            $error = [
               'mainData' => $this->user->errors(),
               'personalData' => $this->userData->errors()
            ];
            $resp = ResponseHelper::responseContent(ResponseHelper::ERROR, "Błąd podczas edycji konta", $error);
        }

        return $this->respondCreated($resp);


    }

    public function remove() {
        $getData = $this->request->getJSON();
        $getData = $getData->data;

        if($this->account->removeAccount($getData->idUser))
            $resp = ResponseHelper::responseContent(ResponseHelper::SUCCESS, "Pomyślnie usunięto konto");
        else
            $resp = ResponseHelper::responseContent(ResponseHelper::ERROR, "Wystąpił błąd podczas usuwania konta",[],$this->account->errors());

        return $this->respondCreated($resp);


    }

    private static function  createToken($tokenActiveHours = 4)
    {
        return [
            "token" => hash("sha512", substr(md5(mt_rand()), 0, 40)),
            "tokenValid" => time() + ($tokenActiveHours * 3600)
        ];
    }

}


// UPDATE
// "data": {
//     "mainData": {
//     "idUser": 49,
//     "email" : "test2@teset.com",
//     "password": "Qwerty338*"
//     },
//     "personalData": {
//         "name": "Imiee",
//         "surname": "Nazwiesko"
//     }
// }

// LOGIN
// {
//     "data": {
//         "enmail": "test2@test.com",
//         "password": "pass"
//     }
// }

// REGISTER
// {
//     "data": {
//         "enmail": "test2@test.com",
//         "password": "pass"
//         "password_confirm": "pass"
//     }
// }

// REMOVE
// {
//     "data": {
//        idUser: ID
//     }
// }
