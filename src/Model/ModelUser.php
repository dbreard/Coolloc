<?php

use Silex\Application;
use Doctrine\DBAL\Connection;

class ModelUser {

    private $db;

    function __construct(Connection $connect){
        $this->db = $connect;
    }

    function verifEmailBdd($email) {
            $sql = "SELECT * FROM user WHERE mail = ?";
            $user = $app['db']->fetchAssoc($sql, array((string) $email));
        
            $resultat = ($user->rowCount()) ? true : false ;
            return $resultat;
    }
    
}