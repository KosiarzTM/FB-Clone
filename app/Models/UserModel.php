<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model {
    protected $table = 'users';
    protected $primaryKey = 'idUser';
    protected $allowedFields = ['idPrivacy','email', 'password','registerDate','token','tokenValid','active'];
    protected $skipValidation = false;
    
    protected $beforeInsert = ['hashPassword'];

    protected $validationRules    = [
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[8]',
        'password_confirm' => 'required|matches[password]'
    ];

    protected $validationMessages = [
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



    protected function hashPassword(array $data){
        if (!isset($data['data']['password'])) return $data;
    
        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
       
        return $data;
    }
}