<?php

namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;


class SearchModel extends Model
{
    protected $db;

    public function __construct(ConnectionInterface &$db)
    {
        $this->db = &$db;
    }

    function findUser($data)
    {

        $searchArray = explode(" ", $data->user);

        $searchQuerry = "";
        $findByMail = "";

        foreach ($searchArray as $key => $value) {
            if (strpos($value, "@") !== false) {
                $findByMail = "u.email LIKE '%" . $value . "%'";
            } else {
                $searchQuerry .= " OR ud.name LIKE '%" . $value . "%' OR ud.surname LIKE '%" . $value . "%'";
            }
        }

        if ($findByMail !== "") {
            if ($searchQuerry !== "") {
                $searchQuerry .= " AND " . $findByMail;
            } else {
                $searchQuerry .= $findByMail;
            }
        } else {
            $searchQuerry = rtrim($searchQuerry, "OR");
        }
        $searchQuerry = ltrim($searchQuerry, " OR");

        $searchByCity = "";
        if (isset($data->userCity) && !empty($data->userCity)) {
            $searchByCity = " AND ud.city LIKE '%" . $data->userCity . "%'";
        }

        $sql = "SELECT u.idUser, u.email, ud.* FROM users u 
        JOIN usersData ud ON ud.idUser = u.idUser
        WHERE u.idPrivacy = 0 AND ($searchQuerry)". $searchByCity;
        $getData = $this->db->query($sql)->getResult();

        return $getData;
    }
}
