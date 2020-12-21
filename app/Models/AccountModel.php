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
            ->where('u.idUser = ', $idUser)
            ->get()
            ->getRow();
    }

    function removeAccount($idUser)
    {
        // TO DO 
        // USUWANIE plików jeżeli istnieją

        $sql = "DELETE u, ud FROM users u 
        JOIN usersData ud ON ud.idUser = u.idUser
        WHERE u.idUser = $idUser";

        return $this->db->query($sql);
    }

    function sendInvite($data)
    {

        $exist = $this->db->query("SELECT * FROM friendList f WHERE f.idUser =" . $data->idUser . " AND f.idFriend= " . $data->idFriend)->getResult();

        if (count($exist) == 0) {
            $sql = "INSERT INTO friendList
            (idUser, idFriend, friendStatus)
            VALUES ($data->idUser, $data->idFriend, 0)";
            $this->db->query($sql);
            return true;
        }

        return false;
    }

    function acceptInvite($data)
    {
        $updateInvite = "UPDATE friendList
        SET
            friendStatus=1
        WHERE idUser = $data->idUser AND idFriend = $data->idFriend";

        if ($this->db->query($updateInvite)) {
            $acceptInvite = "INSERT INTO friendList
            (idUser, idFriend, friendStatus)
            VALUES ($data->idFriend, $data->idUser, 1)";

            $this->db->query($acceptInvite);
            return true;
        } else {
            $declineInvite = "DELETE FROM friendList WHERE idUser = $data->idUser AND $data->idFriend";
            $this->db->query($declineInvite);
            return false;
        }
        return null;
    }

    function getFriends($idUser, $invites = true)
    {
        $friendStatus = 0;
        if ($invites)
            $friendStatus = 1;

        $sql = "SELECT u.idUser,  fs.statusName ,u.email ,ud.name,ud.surname,ud.phone, ud.address, ud.zipCode, ud.city, ud.country FROM users u 
        JOIN usersData ud ON ud.idUser = u.idUser
        JOIN friendList fl ON fl.idUser = u.idUser
        JOIN friendStatus fs ON fs.status = fl.friendStatus
        WHERE u.idUser = $idUser AND fl.friendStatus = $friendStatus";

        $inviteList = $this->db->query($sql)->getResult();
        if (count($inviteList) != 0)
            return $inviteList;
        else
            return false;
    }

    function removeFriend($idUser, $idFriend)
    {
        $sql = "DELETE f FROM friendList f WHERE (f.idUser = $idUser AND f.idFriend = $idFriend) OR (f.idUser = $idFriend  AND f.idFriend = $idUser)";

        $this->db->query($sql)->getResult();
        return true;
    }
}
