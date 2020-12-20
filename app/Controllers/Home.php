<?php

namespace App\Controllers;

use App\Models\SearchModel;
use CodeIgniter\API\ResponseTrait;
use ResponseHelper;

class Home extends BaseController
{
    use ResponseTrait;
	
	protected $db;
	protected $search;

	function __construct()
	{

		$db = db_connect();
		$this->search = new SearchModel($db);

		helper("response_helper");
	}

	public function index()
	{
		return view('welcome_message');
	}


	public function search()
	{
		$getData = $this->request->getJSON();
		$getData = $getData->data;

		$listOfUsers = $this->search->findUser($getData);

		// ResponseHelper::responseContent(ResponseHelper::ERROR, "", [], $this->user->errors());
		return $this->respondCreated($listOfUsers);
	}
}
