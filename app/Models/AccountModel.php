<?php

namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class AccountModel extends Model
{
    protected $db;

    public function __construct(ConnectionInterface &$db)
    {
        $this->db = &$db;
    }


    function getAccount($idUser)
    {
        return  $this->db->table('users u')
            ->join('usersData ud', 'u.idUser = ud.idUser')
            ->select('u.idUser,u.idPrivacy,u.email,u.registerDate, ud.*')
            ->where('u.idUser = ',$idUser)
            ->get()
            ->getRow();
    }

    function removeAccount($idUser) {
        // TO DO 
        // USUWANIE plików jeżeli istnieją
        
        $sql = "DELETE u, ud FROM users u 
        JOIN usersData ud ON ud.idUser = u.idUser
        WHERE u.idUser = $idUser";

        return $this->db->query($sql);
    }
}
