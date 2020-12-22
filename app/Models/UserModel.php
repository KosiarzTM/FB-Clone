<?php
namespace App\Models;
use CodeIgniter\Model;
use Exception;

class UserModel extends Model {
    protected $table = 'users';
    protected $primaryKey = 'idUser';
    protected $allowedFields = ['idPrivacy','email', 'password','registerDate','token','tokenValid','active'];
    protected $skipValidation = false;
    
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    public function findUserByEmailAddress(string $emailAddress)
    {
        $user = $this
            ->asArray()
            ->where(['email' => $emailAddress])
            ->first();

        if (!$user) 
            throw new Exception('Użytkownik o podanym adresie nie istnieje');

        return $user;
    }

    protected function hashPassword(array $data){

        if (!isset($data['data']['password'])) return $data;
    
        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
       
        return $data;
    }
}