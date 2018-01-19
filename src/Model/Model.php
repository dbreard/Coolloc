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
        $sql = "SELECT user_options.* FROM user_options, tokens WHERE tokens.token = ? AND tokens.user_id = user_options.id_user";
        $user = $app['db']->fetchAssoc($sql, array((string) $token));
        return $user;
    }

    // SELECTION DES OPTIONS DE L'UTILISATEUR EN RECHERCHE DE COLOCATAIRES (UNIQUEMENT LES OPTIONS)
    public static function userOptionOnly(string $idUser, Application $app){
      $sql = "SELECT o.* FROM options o, user u WHERE u.options_id = o.id_options AND u.id_user = ?";
      $optionUser = $app['db']->fetchAssoc($sql, array((string) $idUser));
      return $optionUser;
    }

    //SELECTION DES ANNONCES CORRESPONDANT A SON PROPRIETAIRE (INFOS DE BASE)
    public static function annonceByUser(string $idUser, Application $app){
      $sql = "SELECT a.name_coloc, a.description, m.url_media FROM user_post_annonce a, user u, media m WHERE a.user_id = u.id_user AND m.user_post_annonce_id = a.id_user_post_annonce AND u.id_user = ?";
      $userAnnonce = $app['db']->fetchAssoc($sql, array((string) $idUser));
      return $userAnnonce;
    }

}
