<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\UserDataModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class Account extends BaseController
{

    protected $user;
    protected $userData;
    protected $account;
    protected $db;
    protected $mainRules;
    protected $mainRulesErrors;


    function __construct()
    {
        $this->user = new UserModel();
        $this->userData = new UserDataModel();

        $db = db_connect();
        $this->account = new AccountModel($db);
        helper('jwt');

        $this->mainRules = [
            'email' => 'required|valid_email',
        ];

        $this->mainRulesErrors = [
            'email' => [
                'required' => "Błąd, brak adresu email",
                'valid_email' => "Nieprawidłowy adres email",
            ]
        ];
    }

    private function validateEmail($input, array $rules, array $messages = [])
    {
        if (!$this->validateRequest($input, $rules, $messages)) {
            return $this->getResponse(
                $this->validator->getErrors(),
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }
    }

    public function editAccount()
    {
        $input = $this->getRequestInput($this->request);

        try {
            $token = $this->validateToken();
            $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }

        if (!isset($input['mainData']) && !isset($input['personalData'])) {
            $exampleData =
                [
                    "error" => "Brak danych, wymagane conajmniej mainData lub personalData",

                    'mainData' => [
                        'password' => '',
                        'email' => ''
                    ],
                    'personalData' => [
                        'name' => '',
                        'surname' => '',
                        'phone' => '',
                        'address' => '',
                        'zipCode' => '',
                        'city' => '',
                        'country' => ''
                    ]

                ];
            return $this->getResponse(['erdor' => $exampleData], ResponseInterface::HTTP_BAD_REQUEST);
        }


        try {        

            $edit = $this->account->editAccount($input);

            return $this->getResponse([
                'message' => 'Pomyślnie zedytowano konto',
                'data' => $edit,
            ]);
        } catch (Exception $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_BAD_REQUEST);
        }
    }

    public function remove()
    {

        $input = $this->getRequestInput($this->request);
        try {
            $token = $this->validateToken();
            $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }

        try {
            $this->account->removeAccount($input['token']);

            return $this->getResponse([
                'message' => 'Użytkownik pomyślnie usunięty',
            ]);
        } catch (Exception $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }
    }

    public function sendInvite()
    {

        $input = $this->getRequestInput($this->request);
        $rules = [
            'idFriend' => 'required'
        ];

        $missingData = 'Wystąpił błąd podczas przesyłania, brak danych, spróbuj ponownie póżniej';
        $messages = [
            'idFriend' => [
                'required' => $missingData
            ]
        ];

        if (!$this->validateRequest($input, $rules, $messages)) {
            return $this->getResponse(
                $this->validator->getErrors(),
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }


        try {
            $token = $this->validateToken();
            $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }

        try {
            $this->account->sendInvite($input);
            return $this->getResponse([
                'message' => 'Wysłano zaproszenie do znajomych',
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }
    }

    public function acceptInvite()
    {
        $input = $this->getRequestInput($this->request);

        try {
            $token = $this->validateToken();
            $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }

        $rules = [
            'idFriend' => 'required',
        ];

        $missingData = 'Wystąpił błąd podczas przesyłania, brak danych, spróbuj ponownie póżniej';
        $messages = [
            'idFriend' => [
                'required' => $missingData
            ]
        ];

        if (!$this->validateRequest($input, $rules, $messages)) {
            return $this->getResponse(
                $this->validator->getErrors(),
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->account->acceptInvite($input);
            return $this->getResponse([
                'message' => 'Zaakceptowano zaporoszenie',
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }
    }

    public function getInvites()
    {
        $input = $this->getRequestInput($this->request);

        try {
            $token = $this->validateToken();
            $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }


        try {
            $invites = $this->account->getFriends($input['token']);
            return $this->getResponse([
                'data' => $invites,
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }
    }

    public function getFriends()
    {
        $input = $this->getRequestInput($this->request);

        try {
            $token = $this->validateToken();
            $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }

        try {
            $invites = $this->account->getFriends($input['token'], false);
            return $this->getResponse([
                'data' => $invites,
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }
    }

    public function removeFriend()
    {
        $input = $this->getRequestInput($this->request);

        try {
            $token = $this->validateToken();
            $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }


        try {
            $this->account->removeFriend($input);
            return $this->getResponse([
                'message' => "Usunięto z listy znajomych",
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }
    }

    public function getAccount() {
        try {
            $token = $this->validateToken();
            // $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }

        return $this->account->getAccount($token);
    }

    public function viewAccount()
    {

        $input = $this->getRequestInput($this->request);

        $rules = [
            'idFriend' => 'required',
        ];

        $messages = [
            'idFriend' => [
                'required' => "Nie znaleziono konta"
            ]
        ];

        $rules = array_merge($rules, $this->mainRules);
        $messages = array_merge($messages, $this->mainRulesErrors);

        if (!$this->validateRequest($input, $rules, $messages)) {
            return $this->getResponse(
                $this->validator->getErrors(),
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        try {
            $account = $this->account->viewAccount($input);
            return $this->getResponse([
                'data' => $account,
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }
    }
}
