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
        helper('building');
    }

    public function addPost($data)
    {
        $user = $this->user->findUserByCollumn($data['token'],'token');

        $sql = "INSERT INTO posts
        (idPostOwner, post,date,likes) VALUES ";

        $sql .= "(" . $user['idUser'] . " , '" . $data['postContent'] . "',".time().",'0')";


        if (!$this->db->query($sql)) {
            throw new Exception("Błąd podczas dodawania posta");
        }

        return true;
    }

    public function likePost($data,$commentLike = false)
    {
        if(!$commentLike)
            $checkForLikeSQL = "SELECT likes FROM posts p WHERE p.idPost =" . $data['postId'];
        else{
            if(!isset($data['idComment'])|| empty($data['idComment']))
                throw new Exception('Brakujące id komentarza');

            $checkForLikeSQL = "SELECT likes FROM postComments pc WHERE pc.idComment =" . $data['idComment'];
        }

        $checkForLikes = $this->db->query($checkForLikeSQL)->getResult();
        $user = $this->user->findUserByCollumn($data['token'],'token');

        $likesString = '';
        $status = '';
        if (empty($checkForLikes[0]->likes)) {
            $status = "Polubiono!";
            $likesString = $user['idUser'];
        } else {

            $tmpArr = explode(',', $checkForLikes[0]->likes);

            if (!in_array($user['idUser'], $tmpArr)) {
                $status = "Polubiono!";
                $likesString = $checkForLikes[0]->likes . "," . $user['idUser'];
            } else {
                $getIndex = array_search($user['idUser'], $tmpArr);
                $status = "Nie lubię!";

                unset($tmpArr[$getIndex]);
                $tmpArr = array_values($tmpArr);
                $likesString = implode(',', $tmpArr);
            }
        }
        if(!$commentLike) {
            $insertSQL = "UPDATE posts
            SET
                likes='" . $likesString . "'
            WHERE idPost=" . $data['postId'];
        }else {
            $insertSQL = "UPDATE postComments
            SET
                likes='" . $likesString . "'
            WHERE idComment=" . $data['idComment'];
        }


        if (!$this->db->query($insertSQL)) {
            throw new Exception('Błąd');
        }
        return $status;
    }

    public function getPosts($token)
    {
        // $sql = 'SELECT * FROM posts';
        // $posts = $this->db->query($sql)->getResultArray();
        $sqlPosts = "SELECT DISTINCT p.*,ud.* FROM posts p JOIN usersData ud ON ud.idUser = p.idPostOwner
        JOIN friendList fl ON fl.idUser = ud.idUser
        JOIN users u ON u.idUser = ud.idUser
        WHERE u.token ='$token'";
        
        $sqlComments = "SELECT pc.* FROM postComments pc";

        $posts = $this->db->query($sqlPosts)->getResultArray();
        $comments = $this->db->query($sqlComments)->getResultArray();

        $postsWthComments = [];
        foreach ($posts as $keyPost => $valuePost) {
            $postsWthComments[$valuePost['idPost']] = $valuePost;
            $postsWthComments[$valuePost['idPost']]['date'] =date('Y.m.d',$valuePost['date']);

            if($valuePost['likes'] == null) {
                $tmpLikes = 0;
            }else if($valuePost['likes'] != null) {
                $tmpLikes = explode(',',$valuePost['likes']);
                $tmpLikes = count($tmpLikes);
            }
                $postsWthComments[$valuePost['idPost']]['likes'] = $tmpLikes ;
            foreach ($comments as $kC => $vC) {
                if($valuePost['idPost'] == $vC['idPost']){
                    $postsWthComments[$valuePost['idPost']]['comments'][$kC]= $vC;  
                    $postsWthComments[$valuePost['idPost']]['comments'][$kC]['date']= date('Y.m.d',$vC['date']);  

                }
            }
        }
        return array_values($postsWthComments);
    }

    public function editPost($data)
    {
        $user = $this->user->findUserByCollumn($data['token'],'token');

        $post = $this->db->query("SELECT * FROM posts p WHERE p.idPost = " . $data['postId'] . " AND p.idPostOwner =" . $user['idUser'])->getResult();

        if (count($post) == 0)
            throw new Exception("To nie twój post");
        else {
            $updateSQL = "UPDATE posts
            SET
                post= '" . $data['postContent'] . "'
            WHERE idPost = " . $data['postId'] . " AND idPostOwner=" . $user['idUser'];

            if (!$this->db->query($updateSQL))
                throw new Exception('Błąd podczas aktualizacji, spróbuj ponownie później');
        }

        return true;
    }

    public function removePost($data)
    {
        $user = $this->user->findUserByCollumn($data['token'],'token');

        $sql = "DELETE p, pc FROM posts p 
        JOIN postComments pc ON pc.idPost = p.idPost 
        WHERE p.idPost = " . $data['postId'] . " AND pc.idPost = " . $data['postId'] . " AND p.idPostOwner = " . $user['idUser'];

        if (!$this->db->query($sql))
            throw new Exception('Błąd usuwania');

        return true;
    }

    public function addComment($data)
    {
        $user = $this->user->findUserByCollumn($data['token'],'token');
        $sql = "INSERT INTO postComments
        (idCommentOwner, idPost, `comment`,date)
        VALUES (" . $user['idUser'] . "," . $data['postId'] . ", '" . $data['commentContent'] . "',".time().")";

        if (!$this->db->query($sql)) {
            throw new Exception("Błąd podczas dodawania komentarza");
        }

        return true;
    }

    public function removeComment($data)
    {
        $user = $this->user->findUserByCollumn($data['token'],'token');
        $sql = "DELETE FROM postComments WHERE idComment = " . $data['idComment'] . " AND idCommentOwner = " . $user['idUser'] . " AND idPost = " . $data['postId'];

        if (!$this->db->query($sql)) {
            throw new Exception("Błąd podczas usuwania komentarza");
        }

        return true;
    }
}
