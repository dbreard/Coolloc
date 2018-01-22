<?php

namespace Coolloc\Model;

use Silex\Application;
use Doctrine\DBAL\Connection;
use Coolloc\Controller\Controller;

class Model {

    private $db;

    function __construct(Connection $connect){
        $this->db = $connect;
    }

    protected function getDB() {
        return $this->db;
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

    // Retourne toutes les annonces enregistré et le nombre total d'annonce
    public static function searchAllAnnonceExist(Application $app) {
        // REQUETE DE SELECTION
        $sql = "SELECT * FROM user, user_post_annonce, options, city, media WHERE user_post_annonce.user_id = user.id_user AND user_post_annonce.ville_id = city.ville_id AND user_post_annonce.options_id = options.id_options AND user_post_annonce.id_user_post_annonce = media.user_post_annonce_id AND media.type = 'photo' GROUP BY user_post_annonce.id_user_post_annonce";

        $response['search'] = $app['db']->fetchAll($sql);

        $response['count'] = count($response['search']);

        return $response;
    }

    // SELECTION DE L'ID UTILISATEUR EN FONCTION DE L'ID DE L'ANNONCE
    public static function verifUserToAnnonce(int $id_annonce, Application $app) {
        // REQUETE DE SELECTION DE L'ID USER
        $sql = "SELECT user.id_user FROM user, user_post_annonce WHERE user_post_annonce.user_id = user.id_user AND user_post_annonce.id_user_post_annonce = ?";

        $response = $app['db']->fetchAssoc($sql, array((int) $id_annonce));

        return $response;
    }

    // Retourne l'annonces enregistré en fonction de l'id de celle ci
    public static function selectAnnonceById(int $id_annonce, Application $app) {

        // REQUETE DE SELECTION DE L'ANNONCE
        $sql = "SELECT * FROM user, user_post_annonce, options, city WHERE user_post_annonce.user_id = user.id_user AND user_post_annonce.ville_id = city.ville_id AND user_post_annonce.options_id = options.id_options AND user_post_annonce.id_user_post_annonce = ?";

        $responseAnnonce = $app['db']->fetchAssoc($sql, array((int) $id_annonce));

        // REQUETE DE SELECTION DES PHOTOS
        $sql = "SELECT media.url_media FROM user_post_annonce, media WHERE user_post_annonce.id_user_post_annonce = media.user_post_annonce_id AND user_post_annonce.id_user_post_annonce = ? AND media.type = 'photo'";

        $responsePhoto = $app['db']->fetchAll($sql, array((int) $id_annonce));

        // REQUETE DE SELECTION DE LA VIDEO
        $sql = "SELECT media.url_media FROM user_post_annonce, media WHERE user_post_annonce.id_user_post_annonce = media.user_post_annonce_id AND user_post_annonce.id_user_post_annonce = ? AND media.type = 'video'";
        $responseVideo = $app['db']->fetchAssoc($sql, array((int) $id_annonce));

        $response = array();

        $i = 1;
        foreach ($responsePhoto as $key => $value) {
            $response['array_photo']["photo$i"] = $value;
            $i++;
        }

        $response['district'] = Controller::stringToArray($responseAnnonce['district']);
        $response['equipment'] = Controller::stringToArray($responseAnnonce['equipment']);
        $response['hobbies'] = Controller::stringToArray($responseAnnonce['hobbies']);
        $response['member_profil'] = Controller::stringToArray($responseAnnonce['member_profil']);

        $date = date('Ymd');
        $verifDate = str_replace('-', '', $responseAnnonce['date_dispo']);

        $responseAnnonce['date_dispo'] = ($date > $verifDate) ? date('Y-m-d') : $responseAnnonce['date_dispo'];

        $response['annonce'] = $responseAnnonce;
        $response['photo'] = $responsePhoto;
        $response['video'] = $responseVideo;

        return $response;

    }
}
