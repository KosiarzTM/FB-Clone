<?php

namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class PostModel extends Model
{
    protected $db;
    protected $user;

    function __construct()
    {
        $this->user = new UserModel();
        $this->db = db_connect();

        helper('jwt');
    }


    public function addPost($data)
    {
        $user = $this->user->findUserByEmailAddress($data['email']);

        $sql = "INSERT INTO posts
        (idParent, idPostOwner, post) VALUES ";

        if (!isset($data['parentId']))
            $sql .= "(0, " . $user['idUser'] . " , '" . $data['postContent'] . "')";
        else
            $sql .= "(" . $data['parentId'] . ", " . $user['idUser'] . " , '" . $data['postContent'] . "')";

        if (!$this->db->query($sql)) {
            throw new Exception("Błąd podczas dodawania posta");
        }

        return true;
    }

    public function likePost($data)
    {

        $checkForLikeSQL = "SELECT likes FROM posts p WHERE p.idPost =" . $data['postId'];
        $checkForLikes = $this->db->query($checkForLikeSQL)->getResult();
        $user = $this->user->findUserByEmailAddress($data['email']);

        $likesString = '';

        if (!$checkForLikes[0]->likes) {
            $likesString = $user['idUser'];
        } else {

            $likesString = $checkForLikes[0]->likes . "," . $user['idUser'];
        }

        $insertSQL = "UPDATE posts
        SET
            likes='" . $likesString . "'
        WHERE idPost=" . $data['postId'];

        if (!$this->db->query($insertSQL)) {
            throw new Exception('Błąd');
        }
        return true;
    }

    public function getPosts()
    {
    }
}
