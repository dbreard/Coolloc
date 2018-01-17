<?php

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

    public static function userByTokenSession(string $token): array {
        $sql = "SELECT uo.* FROM user_options uo, token t WHERE t.token = ? AND t.user_id = uo.id_user";
        $user = $app['db']->fetchAssoc($sql, array((string) $token));
        return $user;
    }
}
