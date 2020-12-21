<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\UserDataModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use ResponseHelper;
use CodeIgniter\RESTful\ResourceController;
// class Account extends BaseController
class Account extends ResourceController
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

    public function remove()
    {
        $getData = $this->request->getJSON();
        $getData = $getData->data;

        if ($this->account->removeAccount($getData->idUser))
            $resp = ResponseHelper::responseContent(ResponseHelper::SUCCESS, "Pomyślnie usunięto konto");
        else
            $resp = ResponseHelper::responseContent(ResponseHelper::ERROR, "Wystąpił błąd podczas usuwania konta", [], $this->account->errors());

        return $this->respondCreated($resp);
    }

    public function sendInvite()
    {
        $getData = $this->request->getJSON();
        $getData = $getData->data;

        if (isset($getData->idFriend) && !empty($getData->idFriend)) {
            $sendInvite = $this->account->sendInvite($getData);

            if ($sendInvite) {
                $resp = ResponseHelper::responseContent(ResponseHelper::SUCCESS, "Wysłano zaproszenie do znajomych");
            } else {
                $resp = ResponseHelper::responseContent(ResponseHelper::ERROR, "Wysłano już zaproszenie do znajomych");
            }
        } else {
            $resp = ResponseHelper::responseContent(ResponseHelper::ERROR, "Błąd podczas wysyłania zaporoszenia");
        }

        return $this->respondCreated($resp);
    }

    public function acceptInvite()
    {
        $getData = $this->request->getJSON();
        $getData = $getData->data;

        $accepted = $this->account->acceptInvite($getData);

        if ($accepted) {
            $resp = ResponseHelper::responseContent(ResponseHelper::SUCCESS, "Zaakceptowano zaproszenie");
        } else {
            $resp = ResponseHelper::responseContent(ResponseHelper::SUCCESS, "Odrzucono zaproszenie");
        }

        return $this->respondCreated($resp);
    }

    public function getInvites()
    {
        $getData = $this->request->getJSON();
        $getData = $getData->data;

        if (isset($getData) && !empty($getData)) {
            $getList = $this->account->getFriends($getData->idUser);
            if ($getList)
                $resp = ResponseHelper::responseContent(ResponseHelper::SUCCESS, "", $getList);
            else
                $resp = ResponseHelper::responseContent(ResponseHelper::SUCCESS, "Nie znaleziono zaproszeń");
        } else {
            $resp = ResponseHelper::responseContent(ResponseHelper::ERROR, "Błąd");
        }
        return $this->respondCreated($resp);
    }

    public function getFriends()
    {
        $getData = $this->request->getJSON();
        $getData = $getData->data;

        if (isset($getData) && !empty($getData)) {
            $getList = $this->account->getFriends($getData->idUser, true);
            if ($getList)
                $resp = ResponseHelper::responseContent(ResponseHelper::SUCCESS, "", $getList);
            else
                $resp = ResponseHelper::responseContent(ResponseHelper::SUCCESS, "Nie znaleziono przyjaciół");
        } else {
            $resp = ResponseHelper::responseContent(ResponseHelper::ERROR, "Błąd");
        }
        return $this->respondCreated($resp);
    }

    public function removeFriend()
    {
        $getData = $this->request->getJSON();
        $getData = $getData->data;

        if (isset($getData->idUser) && !empty($getData->idUser) && isset($getData->idFriend) && !empty($getData->idFriend)) {
            $removed = $this->account->removeFriend($getData->idUser, $getData->idFriend);

            if ($removed) {
                $resp = ResponseHelper::responseContent(ResponseHelper::SUCCESS, "Usunięto znajomego");
            }
        } else {
            $resp = ResponseHelper::responseContent(ResponseHelper::ERROR, "Błąd");
        }
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
//     "idUser": 49,
//     "mainData": {
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

//GETFRIENDS / GETINVITES / REMOVE 
// {
//     "data": {
//           "idUser": ID
//       }   
//   }

//ACCEPTINVITE
// {
//     "data": {
//           "idUser": ID,
//           "idFriend": ID
//       }   
//   }