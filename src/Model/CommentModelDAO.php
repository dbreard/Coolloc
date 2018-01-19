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
            $sql = "SELECT * FROM comments";
            $comments = $this->getDb()->fetchAll($sql, array());

            return $comments;

        }

    //******* GETTER *********//

    public function getDb(){
        return $this->db;
    }
}
