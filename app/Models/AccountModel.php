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
        $user = $this->user->findUserByCollumn($data['token'], 'token');
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
        $tmpToken = $data['token'];
        unset($data['token']);

        $mainDataUpdateValues = '';
        $personalDataUpdateValues = '';

        $mainDataUpdateKeys = '';
        $personalDataUpdateKeys = '';
        $personalDataUpdateKeys2 = '';
        
        foreach ($data as $dataKey => $dataType) {

            foreach ($dataType as $key => $value) {

                if ($dataKey == 'mainData') {
                    if ($key == 'password')
                        $value = password_hash($value, PASSWORD_DEFAULT);

                    $mainDataUpdateValues .= "'" . $value . "',";
                    $mainDataUpdateKeys .=  $key . ",";
                } else if ($dataKey == 'personalData') {
                    $personalDataUpdateValues .= "'" . $value . "',";
                    $personalDataUpdateKeys .= $key . ",";
                    $personalDataUpdateKeys2 .= " $key = VALUES(".$key . "),";
                }
            }
        }


        if ($personalDataUpdateValues != '')
            $personalDataUpdateValues .=  $user['idUser'].",";

        if ($personalDataUpdateKeys != '')
            $personalDataUpdateKeys .= "idUser,";


        $mainDataUpdateKeys = rtrim($mainDataUpdateKeys, ',');
        $personalDataUpdateKeys2 = rtrim($personalDataUpdateKeys2, ',');
        $mainDataUpdateValues = rtrim($mainDataUpdateValues, ',');

        $personalDataUpdateValues = rtrim($personalDataUpdateValues, ',');
        $personalDataUpdateKeys = rtrim($personalDataUpdateKeys, ',');
   

        $updateMainDataSQL = "INSERT INTO 
        usersData($mainDataUpdateKeys) 
            VALUES 
                ($mainDataUpdateValues) 
            ON DUPLICATE KEY UPDATE idUser = " . $user['idUser'];

        $updatePersonalDataSQL = "INSERT INTO 
            usersData($personalDataUpdateKeys) 
            VALUES 
            ($personalDataUpdateValues) 
            ON DUPLICATE KEY UPDATE " . $personalDataUpdateKeys2;


        if ($mainDataUpdateValues != '')
            $this->db->query($updateMainDataSQL);

        if ($personalDataUpdateValues != '')
            $this->db->query($updatePersonalDataSQL);

        
        return $this->getAccount($tmpToken);
    }

    function getAccount($token)
    { 
        $getData = $this->db->query("SELECT u.idUser,u.idPrivacy,u.email,u.registerDate,ud.* FROM users u 
        LEFT JOIN usersData ud ON ud.idUser = u.idUser
        WHERE u.token = '$token'")->getResultArray();

        return $getData;
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
        $user = $this->user->findUserByCollumn($data['token'], 'token');
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

        $user = $this->user->findUserByCollumn($data['token'], 'token');

        $updateInvite = "UPDATE friendList
        SET
            friendStatus=1
            WHERE idUser = " . $data['idFriend'] . " AND idFriend = " . $user['idUser'];

        if ($this->db->query($updateInvite)) {
            $acceptInvite = "INSERT INTO friendList
            (idUser, idFriend, friendStatus)
            VALUES (" . $user['idUser'] . ", " . $data['idFriend'] . ", 1)";

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
        $user = $this->user->findUserByCollumn($token, 'token');

        $friendStatus = 1;
        if ($invites) {
            $friendStatus = 0;
        }

  
        $sql ="SELECT f.*,ud.* FROM friendList f 
        JOIN users u ON u.idUser = f.idFriend
        LEFT JOIN usersData ud ON ud.idUser = f.idUser
        WHERE f.idFriend = ".$user['idUser'] ." AND f.friendStatus = ".$friendStatus;

        $inviteList = $this->db->query($sql)->getResultArray();
        if (count($inviteList) > 0) {
            return $inviteList;
        } else {
            return [];
        }
    }

    function removeFriend($data)
    {
        $user = $this->user->findUserByCollumn($data['token'], 'token');
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
