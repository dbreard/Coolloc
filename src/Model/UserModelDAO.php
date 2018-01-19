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

    // CHANGEMENT DE MOT DE PASSE
    function changeMdpBdd(string $password)
    {
            $sql = "UPDATE user SET password = ?";
            $resultat = $this->getDb()->fetchAssoc($sql, array((string) $password));
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

    public function updateUserStatus(string $idUser, string $status): int{
      $sql = "UPDATE user SET status = ? WHERE id_user = ? ";
      $rowAffected = $this->getDb()->executeUpdate( $sql, array((string) $status, (int) $idUser));

      return $rowAffected;
    }




}
