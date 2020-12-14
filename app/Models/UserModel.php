<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model {
    protected $table = 'users';
    protected $primaryKey = 'idUser';
    protected $allowedFields = ['idPrivacy','email', 'password','registerDate','token','tokenValid','active'];

}