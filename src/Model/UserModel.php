<?php

namespace Coolloc\Model;

use Doctrine\DBAL\Connection;

class UserModel {

    private $db;

    function __construct(Connection $connect){
        $this->db = $connect;
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



    //******** GETTER ********//

    public function getDb(){
        return $this->db;
    }


}