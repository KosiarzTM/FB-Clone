<?php namespace App\Controllers;

use App\Models\UserModel;

class Home extends BaseController
{
	public function index()
	{
		$userModel = new UserModel();
		$getUsers = $userModel->findAll();
		file_put_contents('users.txt','================'."\n".print_r($getUsers,true)."\n",FILE_APPEND);
		return view('welcome_message');
	}

	//--------------------------------------------------------------------

}
