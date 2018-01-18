<?php

namespace Coolloc\Model;

use Silex\Application;
use Doctrine\DBAL\Connection;

class UserModelDAO {

    private $db;

    function __construct(Connection $connect){
        $this->db = $connect;
    }

    //******* GETTER *********//
    protected function getDb() {
        return $this->db;
    }

    // COMPARAISON DE L'EMAIL AVEC LA BDD
    function verifEmailBdd(string $email)
    {
            $sql = "SELECT * FROM user WHERE mail = ?";
            $resultat = $this->getDb()->fetchAssoc($sql, array((string) $email));
            return $resultat;
    }
    
      
    public function insertUSer(string $first_name,string $last_name,string $birthdate,string $password,string $email,string $tel,string $sexe,string $activite,int     $condition){

        $this->getDb()->insert('options', array());


        $this->getDb()->insert('user', array(
            'options_id' => $this->db->lastInsertId(),
            'firstname' => $first_name,
            'lastname' => $last_name,
            'birthdate' => $birthdate,
            'password' => $password,
            'mail' => $email,
            'tel' => $tel,
            'activity' => $activite,
            'sex' => $sexe,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'conditions' => $condition,
        ));

        return $this->db->lastInsertId();
    }

    public function selectUserFromToken(string $token) :array{

        $sql = "SELECT user_id FROM tokens WHERE token = ? AND type LIKE 'email'";
        $idUser = $this->getDb()->fetchAssoc($sql, array((string) $token));

        return $idUser;

    }

    public function updateUserFromToken(array $idUser) :int{

        $sql = "UPDATE user SET account = 'actif' WHERE id_user = ? ";
        $rowAffected = $this->getDb()->executeUpdate( $sql, array((int) $idUser["user_id"]) );

        return $rowAffected;

    }

    // CHANGEMENT DE MOT DE PASSE
    public function modifyPasswordFromToken( string $password, int $idUser ) {
        
        $sql = "UPDATE user SET password = :password WHERE id_user = :id ";
        $rowAffected = $this->getDb()->executeUpdate( $sql, array('password' => $password, 'id' => $idUser ));

        return $rowAffected;

    }

    //SELECTION DE TOUT LES USERS EN BDD
    public function allUsersSelected(){

        $sql = "SELECT * FROM user_options";
        $users = $this->getDb()->fetchAll($sql, array());

        return $users;
    }



}
