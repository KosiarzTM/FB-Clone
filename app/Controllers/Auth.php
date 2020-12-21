<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use ReflectionException;

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
        if (!$this->validateRequest($input, $validationRules,$validationMessages)) {
            return $this
                ->getResponse(
                    $this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }

        $userModel = new UserModel();
        $userModel->save($input);

        return $this
            ->getJWTForUser(
                $input['email'],
                ResponseInterface::HTTP_CREATED
            );
    }

    private function getJWTForUser(string $emailAddress, int $responseCode = ResponseInterface::HTTP_OK)
    {
        try {
            $model = new UserModel();
            $user = $model->findUserByEmailAddress($emailAddress);
            unset($user['password']);

            helper('jwt');

            return $this->getResponse([
                'message' => 'Użytkownik pomyślnie zautoryzowany',
                'user' => $user,
                'access_token' => getSignedJWTForUser($emailAddress)
            ]);
        } catch (Exception $exception) {
            return $this->getResponse(['error' => $exception->getMessage(),], $responseCode);
        }
    }
}
