<?php

namespace App\Controllers;

use App\Models\SearchModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class Home extends BaseController
{
	
	protected $db;
	protected $search;
    protected $mainRules;
	protected $mainRulesErrors;
	
	function __construct()
	{

		$db = db_connect();
		$this->search = new SearchModel($db);

		helper('jwt');

        $this->mainRules = [
            'email' => 'required|valid_email',
        ];

        $this->mainRulesErrors = [
            'email' => [
                'required' => "Błąd, brakujący adres email",
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
		$this->validateEmail($input,$this->mainRules,$this->mainRulesErrors);

        try {
            $usersList = $this->search->findUser($input);
            return $this->getResponse([
                'data' => $usersList,
                'access_token' => getSignedJWTForUser($input['email'])
            ]);
        } catch (\Throwable $exception) {
            return $this->getResponse(['error' => $exception->getMessage(),], ResponseInterface::HTTP_OK);
        }


		// $getData = $this->request->getJSON();
		// $getData = $getData->data;

		// $listOfUsers = $this->search->findUser($getData);

		// // ResponseHelper::responseContent(ResponseHelper::ERROR, "", [], $this->user->errors());
		// return $this->respondCreated($listOfUsers);
	}
}

//SEARCH
// {
// 	"data": {
// 		  "user": "Imie Nazwisko email", (dowolna kolejność oddzielone spacjami)
// 		  "userCity": "City15"  (opcjonalne)
// 	  }   
//   }