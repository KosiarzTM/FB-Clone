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


    function getAccount($idUser)
    {
        return  $this->db->table('users u')
            ->join('usersData ud', 'u.idUser = ud.idUser')
            ->select('u.idUser,u.idPrivacy,u.email,u.registerDate, ud.*')
            ->where('u.idUser = ', $idUser)
            ->get()
            ->getRow();
    }

    function removeAccount($email)
    {
        // TO DO 
        // USUWANIE plików jeżeli istnieją

        $sql = "DELETE ud,u,fl FROM users u
        LEFT JOIN usersData ud ON ud.idUser = u.idUser
        LEFT JOIN friendList fl ON fl.idUser = u.idUser
        WHERE u.email = '$email'";

        if (!$this->db->query($sql)) {
            throw new Exception('Błąd podczas usuwania konta');
        }
    }

    function sendInvite($data)
    {

        $exist = $this->db->query("SELECT f.* FROM friendList f
        JOIN users u ON u.idUser = f.idUser
        WHERE u.email = '" . $data['email'] . "' AND f.idFriend = " . $data['idFriend'])->getResult();
        if (count($exist) == 0) {
            $sql = "INSERT INTO friendList
            (idUser, idFriend, friendStatus)
            VALUES (" . $data['idUser'] . ", " . $data['idFriend'] . ", 0)";

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

        $user = $this->user->findUserByEmailAddress($data['email']);

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

    function getFriends($email, $invites = true)
    {
        $friendStatus = 1;
        if ($invites)
            $friendStatus = 0;


        $sql = "SELECT uf.idUser, uf.email,udf.name,udf.surname,udf.phone,udf.address,udf.zipCode,udf.city,udf.country FROM users u
        JOIN usersData ud ON ud.idUser = u.idUser
        JOIN friendList f ON f.idUser = u.idUser
        JOIN usersData udf ON udf.idUser = f.idFriend 
        JOIN users uf ON uf.idUser = udf.idUser
        WHERE u.email = '" . $email . "' 
        AND f.friendStatus =" . $friendStatus;

        $inviteList = $this->db->query($sql)->getResult();
        if (count($inviteList) > 0) {
            return $inviteList;
        } else {
            return [];
        }
    }

    function removeFriend($data)
    {
        $user = $this->user->findUserByEmailAddress($data['email']);
        $sql = "DELETE f FROM friendList f WHERE (f.idUser = " . $user['idUser'] . " AND f.idFriend = " . $data['idFriend'] . ") OR (f.idUser = " . $data['idFriend'] . "  AND f.idFriend = " . $user['idUser'] . ")";

        if (!$this->db->query($sql)) {
            throw new Exception('Błąd podczas usówania znajomego');
        }

        return true;
    }
}
