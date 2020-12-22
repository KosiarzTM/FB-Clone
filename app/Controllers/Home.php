<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\SearchModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class Home extends BaseController
{

    protected $db;
    protected $search;
    protected $mainRules;
    protected $mainRulesErrors;
    protected $postModel;

    function __construct()
    {

        $db = db_connect();
        $this->search = new SearchModel($db);
        $this->postModel = new PostModel();

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

    public function index()
    {
        return view('welcome_message');
    }


    public function search()
    {

        $input = $this->getRequestInput($this->request);
        $rules = [
            'user' => 'required'
        ];

        $messages = [
            'user' => [
                'required' => 'Zdecyduj się kogo chcesz znaleźć'
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
            $usersList = $this->search->findUser($input);
            return $this->getResponse([
                'data' => $usersList,
                'access_token' => getSignedJWTForUser($input['email'])
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage(),], ResponseInterface::HTTP_OK);
        }
    }

    public function addPost()
    {
        $input = $this->getRequestInput($this->request);

        $rules = [
            'postContent' => 'required|min_length[1]'
        ];

        $messages = [
            'postContent' => [
                'required' => 'Nie można wysłać pustego posta',
                'min_length' => "Post musi zawierać conajmniej 1 znak"
            ]
        ];

        if (isset($input['response'])) {
            $rules = [
                'parentId' => 'required'
            ];

            $messages = [
                'parentId' => [
                    'required' => 'Nie można znaleźć posta',
                ]
            ];
        }

        $rules = array_merge($rules, $this->mainRules);
        $messages = array_merge($messages, $this->mainRulesErrors);

        if (!$this->validateRequest($input, $rules, $messages)) {
            return $this->getResponse(
                $this->validator->getErrors(),
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }
        try {
            $this->postModel->addPost($input);

            return $this->getResponse([
                'message' => "Dodano post",
                // 'access_token' => getSignedJWTForUser($input['user'])
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage(), Response::HTTP_OK]);
        }
    }

    public function likePost()
    {
        $input = $this->getRequestInput($this->request);
        $this->validateEmail($input, $this->mainRules, $this->mainRulesErrors);

        try {
            $likeStatus = $this->postModel->likePost($input);

            return $this->getResponse([
                'message' => $likeStatus,
                'access_token' => getSignedJWTForUser($input['email'])
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage(),], ResponseInterface::HTTP_OK);
        }
    }
}

//SEARCH
// {
// 	"data": {
// 		  "user": "Imie Nazwisko email", (dowolna kolejność oddzielone spacjami)
// 		  "userCity": "City15"  (opcjonalne)
// 	  }   
//   }