<?php

namespace Coolloc\Model;

use Silex\Application;
use Doctrine\DBAL\Connection;

class Model {

    private $db;

    function __construct(Connection $connect){
        $this->db = $connect;
    }

    // SELECTION USER + OPTION EN FONCTION DU TOKEN SESSION
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
      $sql = "SELECT a.id_user_post_annonce, a.name_coloc, a.description, m.url_media FROM annonce_options_city a, media m WHERE a.id_user_post_annonce = m.user_post_annonce_id AND a.user_id = ? AND m.type = 'photo' GROUP BY a.id_user_post_annonce";
      $userAnnonce = $app['db']->fetchAll($sql, array((string) $idUser));

      return $userAnnonce;
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
