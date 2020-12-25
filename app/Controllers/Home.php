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
        try {
            $token = $this->validateToken();
            $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }

        
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
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }
    }

    public function addPost()
    {
        $input = $this->getRequestInput($this->request);

        try {
            $token = $this->validateToken();
            $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }

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

        // $rules = array_merge($rules, $this->mainRules);
        // $messages = array_merge($messages, $this->mainRulesErrors);

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
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage(), Response::HTTP_OK]);
        }
    }

    public function likePost()
    {
        $input = $this->getRequestInput($this->request);
        try {
            $token = $this->validateToken();
            $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }

        try {
            $likeStatus = $this->postModel->likePost($input);

            return $this->getResponse([
                'message' => $likeStatus,
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }
    }

    public function likeComment()
    {
        $input = $this->getRequestInput($this->request);
        try {
            $token = $this->validateToken();
            $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }

        try {
            $likeStatus = $this->postModel->likePost($input,true);

            return $this->getResponse([
                'message' => $likeStatus
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }
    }

    public function getPosts()
    {
        $postTree = $this->postModel->getPosts();
        try {
            $token = $this->validateToken();
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }

        return $this->getResponse([
            'data' => $postTree
        ]);
    }

    public function editPost()
    {
        $input = $this->getRequestInput($this->request);
        $rules = [
            'postContent' => 'required',
            'postId' => 'required'
        ];

        $messages = [
            'user' => [
                'required' => 'Brak danych'
            ],
            'postId' => [
                'required' => 'Brak danych, nie znaleziono postu'
            ],
            'postContent' => [
                'required' => 'Post nie może być pusty'
            ]
        ];

        try {
            $token = $this->validateToken();
            $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }

        if (!$this->validateRequest($input, $rules, $messages)) {
            return $this->getResponse(
                $this->validator->getErrors(),
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        try {
            $edit = $this->postModel->editPost($input);
            return $this->getResponse([
                'message' => "Zedytowano post!",
                'dane' => $edit,
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }
    }

    public function removePost()
    {
        $input = $this->getRequestInput($this->request);
        $rules = [
            'postId' => 'required'
        ];

        $messages = [
            'user' => [
                'required' => 'Zdecyduj się co chcesz usunąć'
            ]
        ];

        try {
            $token = $this->validateToken();
            $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }

        if (!$this->validateRequest($input, $rules, $messages)) {
            return $this->getResponse(
                $this->validator->getErrors(),
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        try {
            $removed = $this->postModel->removePost($input);

            return $this->getResponse([
                'message' => "Usunięto post",
                'dataa' => $removed,
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }
    }

    public function addComment()
    {
        $input = $this->getRequestInput($this->request);
        $rules = [
            'postId' => 'required',
            'commentContent' => 'required'
        ];

        $messages = [
            'postId' => [
                'required' => 'Zdecyduj się co chcesz komentować'
            ],
            'commentContent' => [
                'required' => 'Komentaż nie może być pusty'
            ]
        ];

        try {
            $token = $this->validateToken();
            $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }

        if (!$this->validateRequest($input, $rules, $messages)) {
            return $this->getResponse(
                $this->validator->getErrors(),
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        try {
            $commented = $this->postModel->addComment($input);

            return $this->getResponse([
                'message' => $commented,
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }
    }

    public function removeComment()
    {
        $input = $this->getRequestInput($this->request);
        $rules = [
            'postId' => 'required',
            'idComment' => 'required'
        ];

        $messages = [
            'postId' => [
                'required' => 'Zdecyduj się co chcesz usunąć'
            ],
            'idComment' => [
                'required' => 'Zdecyduj się co chcesz usunąć'
            ]
        ];

        try {
            $token = $this->validateToken();
            $input['token'] = $token;
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
        }

        if (!$this->validateRequest($input, $rules, $messages)) {
            return $this->getResponse(
                $this->validator->getErrors(),
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        try {
            $comment = $this->postModel->removeComment($input);

            return $this->getResponse([
                'message' => $comment
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage()], ResponseInterface::HTTP_OK);
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