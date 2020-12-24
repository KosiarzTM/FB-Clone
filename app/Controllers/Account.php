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
        $this->validateEmail($input, $this->mainRules, $this->mainRulesErrors);
        try {
            
            $edit = $this->account->editAccount($input);

            return $this->getResponse([
                'message' => 'Pomyślnie zedytowano konto',
                'data' => $edit,
                'access_token' => getSignedJWTForUser($input['email'])
            ]);
        } catch (Exception $exception) {
            return $this->getResponse(['error' => $exception->getMessage(),], ResponseInterface::HTTP_OK);
        }
    }

    public function remove()
    {

        $input = $this->getRequestInput($this->request);
        $this->validateEmail($input, $this->mainRules, $this->mainRulesErrors);

        try {
            $this->account->removeAccount($input['email']);

            return $this->getResponse([
                'message' => 'Użytkownik pomyślnie usunięty',
                'access_token' => getSignedJWTForUser($input['email'])
            ]);
        } catch (Exception $exception) {
            return $this->getResponse(['error' => $exception->getMessage(),], ResponseInterface::HTTP_OK);
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

        $rules = array_merge($rules, $this->mainRules);
        $messages = array_merge($messages, $this->mainRulesErrors);

        if (!$this->validateRequest($input, $rules, $messages)) {
            return $this->getResponse(
                $this->validator->getErrors(),
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->account->sendInvite($input);
            return $this->getResponse([
                'message' => 'Wysłano zaproszenie do znajomych',
                'access_token' => getSignedJWTForUser($input['email'])
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage(),], ResponseInterface::HTTP_OK);
        }
    }

    public function acceptInvite()
    {
        $input = $this->getRequestInput($this->request);
        $rules = [
            'idFriend' => 'required',
        ];

        $missingData = 'Wystąpił błąd podczas przesyłania, brak danych, spróbuj ponownie póżniej';
        $messages = [
            'idFriend' => [
                'required' => $missingData
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
            $this->account->acceptInvite($input);
            return $this->getResponse([
                'message' => 'Zaakceptowano zaporoszenie',
                'access_token' => getSignedJWTForUser($input['email'])
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage(),], ResponseInterface::HTTP_OK);
        }
    }

    public function getInvites()
    {
        $input = $this->getRequestInput($this->request);

        $this->validateEmail($input, $this->mainRules, $this->mainRulesErrors);

        try {
            $invites = $this->account->getFriends($input['email']);
            return $this->getResponse([
                'data' => $invites,
                'access_token' => getSignedJWTForUser($input['email'])
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage(),], ResponseInterface::HTTP_OK);
        }
    }

    public function getFriends()
    {
        $input = $this->getRequestInput($this->request);

        $this->validateEmail($input, $this->mainRules, $this->mainRulesErrors);

        try {
            $invites = $this->account->getFriends($input['email'], false);
            return $this->getResponse([
                'data' => $invites,
                'access_token' => getSignedJWTForUser($input['email'])
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage(),], ResponseInterface::HTTP_OK);
        }
    }

    public function removeFriend()
    {
        $input = $this->getRequestInput($this->request);
        $this->validateEmail($input, $this->mainRules, $this->mainRulesErrors);

        try {
            $this->account->removeFriend($input);
            return $this->getResponse([
                'message' => "Usunięto z listy znajomych",
                'access_token' => getSignedJWTForUser($input['email'])
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage(),], ResponseInterface::HTTP_OK);
        }
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
                'access_token' => getSignedJWTForUser($input['email'])
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage(),], ResponseInterface::HTTP_OK);
        }
    }
}
