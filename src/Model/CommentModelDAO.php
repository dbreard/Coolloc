<?php
namespace Coolloc\Model;

use Doctrine\DBAL\Connection;

class CommentModelDAO
{

    private $db;

    function __construct(Connection $connect){
        $this->db = $connect;
    }


        // Insert un commentaire
        public function createComment(int $id_user, string $comment){
            $rowAffected = $this->getDb()->insert('comments',  array(
                'user_id' => $id_user,
                'comment' => $comment,

            ));

        return $rowAffected;

        }

        // selectionne tout les commentaires
        public function selectComment(){
            $sql = "SELECT c.*, u.firstname, u.lastname, u.status, u.profil_picture FROM comments c, user u WHERE c.user_id = u.id_user ORDER BY RAND() LIMIT 0, 9";
            $comments = $this->getDb()->fetchAll($sql, array());

            return $comments;

        }

        public function deleteComment(int $idComment){
            $resultat = $this->getDb()->delete("comments", array("id_comments" => $idComment));
            return $resultat;
        }

    //******* GETTER *********//

    public function getDb(){
        return $this->db;
    }
}
