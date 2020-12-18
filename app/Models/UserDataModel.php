<?php
namespace App\Models;
use CodeIgniter\Model;

class UserDataModel extends Model {
    protected $table      = 'usersData';
    protected $primaryKey = 'idUserData';
    protected $allowedFields = ['idUser','name', 'surname','phone','address','zip-code','city','country'];

    protected $validationRules    = [
        'name' => "alpha_dash",
        'surname' => "alpha_dash",
        'phone' => "regex_match[/(?<!\w)(\(?(\+|00)?48\)?)?[ -]?\d{3}[ -]?\d{3}[ -]?\d{3}(?!\w)/]",
    ];
    protected $validationMessages = [
        'name' => [
            'alpha_dash' => 'To pole może zawierać jedynie litery oraz znak -'
        ],
        'surname' => [
            'alpha_dash' => 'To pole może zawierać jedynie litery oraz znak -'
        ],
        'phone' => [
            'regex_match' => 'Wprowadzono niepoprawny numer telefonu'
        ],
    ];
}