<?php
namespace Coolloc\Model;

use Doctrine\DBAL\Connection;

class FaqModelDAO
{

    private $db;

    function __construct(Connection $connect){
        $this->db = $connect;
    }

    public function insertFaq(string $question, string $reponse){
        $rowAffected = $this->getDb()->insert('faq',  array(
            'question' => $question,
            'reponse' => $reponse,
        ));

        return $rowAffected;

    }

    public function allFaqSelected(){
        $sql = "SELECT * FROM faq";
        $faq = $this->getDb()->fetchAll($sql, array());

        return $faq;
    }


    //******* GETTER *********//

    public function getDb(){
        return $this->db;
    }

}
