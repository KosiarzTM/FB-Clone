<?php

namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use Error;
use Exception;

class AccountModel extends Model
{
    protected $db;
    protected $user;

    public function __construct(ConnectionInterface &$db)
    {
        $this->db = &$db;
        $this->user = new UserModel();
    }

    function editAccount($data)
    {
        $user = $this->user->findUserByCollumn($data['token'],'token');
        // $data = [
        //     'mainData' => [
        //         'password' => 'Qwertyyyy',
        //         'email' => 'email@mail.com'
        //     ],
        //     'personalData' => [
        //         'name' => 'Jan',
        //         'surname' => 'Now123ak',
        //         'phone' => '666666666',
        //         'address' => 'Janówek',
        //         'zipCode' => '00-000',
        //         'city' => 'Janowo',
        //         'country' => 'Januszowo',
        //     ]
        // ];

        $mainDataUpdateValues = '';
        $personalDataUpdateValues = '';

        foreach ($data as $dataKey => $dataType) {
            foreach ($dataType as $key => $value) {
                if ($dataKey == 'mainData') {
                    if ($key == 'password')
                        $value = password_hash($value, PASSWORD_DEFAULT);

                    $mainDataUpdateValues .= $key . "='" . $value . "',";
                } else if ($dataKey == 'personalData') {
                    $personalDataUpdateValues .= $key . "='" . $value . "',";
                }
            }
        }

        $mainDataUpdateValues = rtrim($mainDataUpdateValues, ',');
        $personalDataUpdateValues = rtrim($personalDataUpdateValues, ',');


        $updateMainDataSQL = "UPDATE users SET " . $mainDataUpdateValues . " WHERE idUser = ". $user['idUser'] ;
        $updatePersonalDataSQL = "UPDATE usersData SET " . $personalDataUpdateValues . " WHERE idUser = ". $user['idUser'];

        if ($mainDataUpdateValues != '')
            $this->db->query($updateMainDataSQL);

        if ($personalDataUpdateValues != '')
            $this->db->query($updatePersonalDataSQL);

        return $data;
    }

    function getAccount($idUser)
    {
        return  $this->db->table('users u')
            ->join('usersData ud', 'u.idUser = ud.idUser')
            ->select('u.idUser,u.idPrivacy,u.email,u.registerDate, ud.*')
            ->where('u.idUser = ', $idUser)
            ->get()
            ->getRow();
    }

    function removeAccount($token)
    {
        // TO DO 
        // USUWANIE plików jeżeli istnieją

        $sql = "DELETE ud,u,fl FROM users u
        LEFT JOIN usersData ud ON ud.idUser = u.idUser
        LEFT JOIN friendList fl ON fl.idUser = u.idUser
        WHERE u.token = '$token'";

        if (!$this->db->query($sql)) {
            throw new Exception('Błąd podczas usuwania konta');
        }
    }

    function sendInvite($data)
    {
        $user = $this->user->findUserByCollumn($data['token'],'token');
        $exist = $this->db->query("SELECT f.* FROM friendList f
        JOIN users u ON u.idUser = f.idUser
        WHERE u.token = '" . $user['token'] . "' AND f.idFriend = " . $data['idFriend'])->getResult();
        if (count($exist) == 0) {
            $sql = "INSERT INTO friendList
            (idUser, idFriend, friendStatus)
            VALUES (" . $user['idUser'] . ", " . $data['idFriend'] . ", 0)";

            if (!$this->db->query($sql)) {
                throw new Exception('Błąd podczas wysyłania zaproszenia');
            }
            return true;
        } else {
            throw new Exception('Zaproszono już tą osobę do znajomych');
        }
    }

    function acceptInvite($data)
    {

        $user = $this->user->findUserByCollumn($data['token'],'token');

        $updateInvite = "UPDATE friendList
        SET
            friendStatus=1
            WHERE idUser = " . $user['idUser'] . " AND idFriend = " . $data['idFriend'];

        if ($this->db->query($updateInvite)) {
            $acceptInvite = "INSERT INTO friendList
            (idUser, idFriend, friendStatus)
            VALUES (" . $data['idFriend'] . ", " . $user['idUser'] . ", 1)";

            if (!$this->db->query($acceptInvite)) {
                throw new Exception('Błąd podczas akceptacji zaproszenia');
            }
        } else {
            $declineInvite = "DELETE FROM friendList WHERE idUser = " . $user['idUser'];
            if (!$this->db->query($declineInvite)) {
                throw new Exception('Błąd podczas odrzucenia zaproszenia');
            }
        }
        return true;
    }

    function getFriends($token, $invites = true)
    {
        $friendStatus = 1;
        if ($invites)
            $friendStatus = 0;


        $sql = "SELECT uf.idUser, uf.email,udf.name,udf.surname,udf.phone,udf.address,udf.zipCode,udf.city,udf.country FROM users u
        JOIN usersData ud ON ud.idUser = u.idUser
        JOIN friendList f ON f.idUser = u.idUser
        JOIN usersData udf ON udf.idUser = f.idFriend 
        JOIN users uf ON uf.idUser = udf.idUser
        WHERE u.token = '" . $token . "' 
        AND f.friendStatus =" . $friendStatus;

        $inviteList = $this->db->query($sql)->getResultArray();
        if (count($inviteList) > 0) {
            return $inviteList;
        } else {
            return [];
        }
    }

    function removeFriend($data)
    {
        $user = $this->user->findUserByCollumn($data['token'],'token');
        $sql = "DELETE f FROM friendList f WHERE (f.idUser = " . $user['idUser'] . " AND f.idFriend = " . $data['idFriend'] . ") OR (f.idUser = " . $data['idFriend'] . "  AND f.idFriend = " . $user['idUser'] . ")";

        if (!$this->db->query($sql)) {
            throw new Exception('Błąd podczas usówania znajomego');
        }

        return true;
    }

    function viewAccount($data)
    {
        $sql = "SELECT u.idUser,u.idPrivacy,u.email,u.active,ud.name,ud.surname,ud.phone,ud.address,ud.zipCode,ud.city,ud.country FROM users u
        JOIN usersData ud ON ud.idUser = u.idUser 
        WHERE u.idPrivacy IN (1,2) AND u.idUser =" . $data['idFriend'];

        $user = $this->db->query($sql)->getResult();
        if (count($user) == 0) {
            throw new Exception('Nie możesz zobaczyć konta tego użytkownika');
        } else {
            return $user;
        }
    }
}
