<?php
namespace Coolloc\Model;

use Doctrine\DBAL\Connection;

class CommentModelDAO
{

    private $db;

    function __construct(Connection $connect){
        $this->db = $connect;
    }



public function createComment(int $id_user, string $comment){
   $rowAffected = $this->getDb()->insert('comments',  array(
        'user_id' => $id_user,
        'comment' => $comment,
        
    ));

return $rowAffected;


}

    //******* GETTER *********//

    public function getDb(){
        return $this->db;
    }
}
