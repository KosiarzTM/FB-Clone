<?php

namespace App\Controllers;

use App\Models\UserDataModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class Auth extends BaseController
{

    public function register()
    {

        $validationRules = [
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]'
        ];

        $validationMessages = [
            'email' => [
                'is_unique' => 'Przepraszamy, podany adres email jest już zajęty.',
                'valid_email' => 'Proszę podać poprawny adres email',
                'required' => 'To pole jest wymagane'
            ],
            'password' => [
                'required' => 'To pole jest wymagane',
                'min_length' => 'Hasło musi zawierać minimum 8 znaków, conajmniej jedną małą i dużą literę, cyfrę oraz znak specjalny'

            ],
            'password_confirm' => [
                'matches' => 'Hasła są różne',
                'required' => 'To pole jest wymagane',
            ]
        ];

        $input = $this->getRequestInput($this->request);
        if (!$this->validateRequest($input, $validationRules, $validationMessages)) {
            return $this
                ->getResponse(
                    $this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }

        $input['registerDate'] = time();
        $userModel = new UserModel();
        $idUser =  $userModel->insert($input);

        if ($idUser) {
            $userDataModel = new UserDataModel();
            $userDataModel->insert(['idUser' => $idUser]);
        }

        return $this
            ->getJWTForUser(
                $input['email'],
                ResponseInterface::HTTP_CREATED
            );
    }

    public function login()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[8]|max_length[255]|validateUser[email, password]'
        ];

        $errors = [
            'password' => [
                'validateUser' => 'Niepoprawne dane logowania'
            ]
        ];

        $input = $this->getRequestInput($this->request);

        if (!$this->validateRequest($input, $rules, $errors)) {
            return $this
                ->getResponse(
                    $this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
        return $this->getJWTForUser($input['email']);
    }

    private function getJWTForUser(string $emailAddress, int $responseCode = ResponseInterface::HTTP_OK)
    {
        try {
            $model = new UserModel();
            $user = $model->findUserByCollumn($emailAddress);
            unset($user['password']);

            helper('jwt');
            $token = getSignedJWTForUser($emailAddress);
            
            $userModel = new UserModel();
            $user = $userModel->set("token",$token)->where('idUser',$user['idUser'])->update();

            return $this->getResponse([
                'user' => $user,
                'token' => $token
            ]);
        } catch (Exception $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], $responseCode);
        }
    }
}
