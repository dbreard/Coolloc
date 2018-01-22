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

    public function deleteFaq(int $idFaq){
        $resultat = $this->getDb()->delete("faq", array("id" => $idFaq));
        return $resultat;
    }

    public function selectFaq(int $idFaq){
        $sql = "SELECT * FROM faq WHERE id = ?";
        $faq = $this->getDb()->fetchAssoc($sql, array((int) $idFaq));

        return $faq;
    }

    public function updateFaq(int $idFaq, string $question, string $reponse){
        $sql = "UPDATE faq SET question = :question, reponse = :reponse WHERE id = :id ";
        $rowAffected = $this->getDb()->executeUpdate($sql, array('question' => $question, 'reponse' => $reponse, 'id' => $idFaq));

        return $rowAffected;
    }


    //******* GETTER *********//

    public function getDb(){
        return $this->db;
    }

}
