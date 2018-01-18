<?php
namespace Coolloc\Model;

use Doctrine\DBAL\Connection;

class CommentModelDAO
{

    private $db;

    function __construct(Connection $connect){
        $this->db = $connect;
    }



public function createComment(int $id_user,string $comment): ? string{
    $this->getDb()->insert('comments', array(
        'comment' => $comment,
        'user_id' => $id_user,
    ));

}

public function DeleteComment(int $id_user,string $comment): ? string{
        $this->getDb()->insert('comments', array(
        'comment' => $comment,
        'user_id' => $id_user,

));
    return $comment;
}

    //******* GETTER *********//

    public function getDb(){
        return $this->db;
    }
}
