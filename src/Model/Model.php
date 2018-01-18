<?php

namespace Coolloc\Model;

use Silex\Application;
use Doctrine\DBAL\Connection;

class Model {

    private $db;

    function __construct(Connection $connect){
        $this->db = $connect;
    }

    public function verifEmailBdd(string $email){
        $sql = "SELECT email FROM user WHERE token = ? AND type LIKE 'email'";
        $idUser = $app['db']->fetchAssoc($sql, array((string) $token));
    }



    public static function userByTokenSession(string $token, Application $app): array {
        $sql = "SELECT user_options.* FROM user_options, tokens WHERE tokens.token = ? AND tokens.user_id = user_options.id";
        $user = $app['db']->fetchAssoc($sql, array((string) $token));
        return $user;
    }

    // verification id_user
    public static function verifIdUserExist(int $user, Application $app): bool {
        $sql = "SELECT id_user FROM user WHERE id_user = ? ";
        $userId = $app['db']->fetchAssoc($sql, array((int)$user));
        if(count($userId) == 1 ){
            return true;
        }
        else {
            return false;
        }
        
    }

}
