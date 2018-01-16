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

    // COMPARAISON DU MDP AVEC L'USER EN BDD
    function verifUserBdd(string $email)
    {
            $sql = "SELECT * FROM user WHERE mail = ?";
            $resultat = $this->getDb()->fetchAssoc($sql, array((string) $email));
            return $resultat;
    }

    // CHANGEMENT DE MOT DE PASSE
    /*
    function changeMdpBdd(string $password)
    {
            $sql = "UPDATE user SET password = ?";
            $resultat = $this->getDb()->fetchAssoc($sql, array((string) $password));
            return $resultat;
    }
    */
}       
