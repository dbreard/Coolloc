<?php

namespace Coolloc\Model;

use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Doctrine\DBAL\Connection;
use Silex\Application;

class AnnonceModelDAO{

    private $db;

    function __construct(Connection $connect) {
        $this->db = $connect;
    }

    protected function getDB() {
        return $this->db;
    }

    // public function listeVille() {
    //     $sql = "SELECT ville_id, ville_nom_reel FROM city";
    //     $ville = $this->db->fetchAll($sql);
    //
    //     return $ville;
    // }

    public function createAnnonce(array $arrayAnnonce, array $arrayMedia, Application $app){

        // On vérifie dans la BDD que la ville et code postal sont correctes
        $sql = "SELECT ville_id FROM city WHERE ville_nom_reel = ?";

        $ville_id = $this->db->fetchAssoc($sql, array((string) $arrayAnnonce['ville']));

        // Si c'est faux on stop la requête
        if ($ville_id == false)
            return "ville_invalid";

        // echo "<pre>";
        // var_dump($arrayAnnonce);
        // echo "</pre>";
        // die();

        $arrayAnnonce['handicap_access'] = ( !empty($arrayAnnonce['handicap_access']) ) ? $arrayAnnonce['handicap_access'] : 'non';
        $arrayAnnonce['smoking'] = ( !empty($arrayAnnonce['smoking']) ) ? $arrayAnnonce['smoking'] : 'non';
        $arrayAnnonce['animals'] = ( !empty($arrayAnnonce['animals']) ) ? $arrayAnnonce['animals']: 'non';
        $arrayAnnonce['sex_roommates'] = ( !empty($arrayAnnonce['sex_roommates']) ) ? $arrayAnnonce['sex_roommates'] : 'peu importe';
        $arrayAnnonce['furniture'] = ( !empty($arrayAnnonce['furniture']) ) ? $arrayAnnonce['furniture'] : 'peu importe';
        $arrayAnnonce['garden'] = ( !empty($arrayAnnonce['garden']) ) ? $arrayAnnonce['garden'] : 'non';
        $arrayAnnonce['balcony'] = ( !empty($arrayAnnonce['balcony']) ) ? $arrayAnnonce['balcony'] : 'non';
        $arrayAnnonce['parking'] = ( !empty($arrayAnnonce['parking']) ) ? $arrayAnnonce['parking'] : 'non';

        $ajoutOption = $this->db->insert('options', array(
            'handicap_access' => $arrayAnnonce['handicap_access'],
            'smoking' => $arrayAnnonce['smoking'],
            'animals' => $arrayAnnonce['animals'],
            'equipment' => $arrayAnnonce['equipment'],
            'sex_roommates' => $arrayAnnonce['sex_roommates'],
            'furniture' => $arrayAnnonce['furniture'],
            'garden' => $arrayAnnonce['garden'],
            'balcony' => $arrayAnnonce['balcony'],
            'parking' => $arrayAnnonce['parking'],
            'district' => $arrayAnnonce['district'],
            'hobbies' => $arrayAnnonce['hobbies'],
            'date_dispo' => $arrayAnnonce['date_dispo'],
            'member_profil' => $arrayAnnonce['member_profil']
        ));

        if ($ajoutOption) {
            $optionId = $this->db->lastInsertId();

            $user = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);

            $ajoutAnnonce = $this->db->insert('user_post_annonce', array(
                'user_id' => $user['id_user'],
                'options_id' => $optionId,
                'ville_id' => $ville_id['ville_id'],
                'name_coloc' => $arrayAnnonce['name_coloc'],
                'adress' => $arrayAnnonce['adress'],
                'adress_details' => $arrayAnnonce['adress_details'],
                'postal_code' => $arrayAnnonce['postal_code'],
                'mail_annonce' => $arrayAnnonce['mail_annonce'],
                'tel_annonce' => $arrayAnnonce['tel_annonce'],
                'rent' => $arrayAnnonce['rent'],
                'surface' => $arrayAnnonce['surface'],
                'nb_room' => $arrayAnnonce['nb_room'],
                'description' => $arrayAnnonce['description'],
                'housing_type' => $arrayAnnonce['housing_type'],
                'nb_roommates' => $arrayAnnonce['nb_roommates'],
                'conditions' => $arrayAnnonce['conditions'],
            ));

            if ($ajoutAnnonce) {
                $annonceId = $this->db->lastInsertId();

                foreach ($arrayMedia as $key => $value) {
                    if ($key == "video") {
                        $this->db->insert('media', array(
                            'user_post_annonce_id' => $annonceId,
                            'type' => 'video',
                            'url_media' => $value,
                        ));
                    }else {
                        $ajoutMedia = $this->db->insert('media', array(
                            'user_post_annonce_id' => $annonceId,
                            'type' => 'photo',
                            'url_media' => $value,
                        ));
                    }
                }

                if ($ajoutMedia) {
                    $user = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);

                    $updateStatus = new UserModelDAO($app['db']);

                    if ($user['status'] == "première connexion") {
                        $rowAffected = $updateStatus->updateUserStatus($user['id_user'], "cherche colocataire");

                        if ($rowAffected == 1) {
                            return $annonceId;
                        }
                    }else {
                        return $annonceId;
                    }

                }
            }
        }else {
            return false;
        }
    }


    // SELECTION DE TOUTES LES ANNONCES
    public function allAnnoncesSelected() : array{

        $sql = "SELECT * FROM annonce_options_city";
        $users = $this->getDb()->fetchAll($sql, array());

        return $users;
    }

    // SELECTION DE TOUTES LES ANNONCES RECENTE
    public function OrderAllAnnoncesSelected() : array{

        $sql = "SELECT * FROM user, user_post_annonce, media WHERE user_post_annonce.user_id = user.id_user AND user_post_annonce.id_user_post_annonce = media.user_post_annonce_id AND media.type = 'photo' GROUP BY user_post_annonce.id_user_post_annonce ORDER BY user_post_annonce.date_created DESC LIMIT 0,3";
        $response = $this->getDb()->fetchAll($sql);

        return $response;
    }


    // SELECTIONNE TOUTE LES INFO D'UNE ANNONCE PAR SON ID
    public function selectAnnonceById(int $id_annonce) {

        $sql = "SELECT * FROM user, user_post_annonce, options, city WHERE user_post_annonce.user_id = user.id_user AND user_post_annonce.ville_id = city.ville_id AND user_post_annonce.options_id = options.id_options AND user_post_annonce.id_user_post_annonce = ? GROUP BY user_post_annonce.id_user_post_annonce";
        $responseAnnonce = $this->getDb()->fetchAssoc($sql, array((int) $id_annonce));

        $sql = "SELECT media.url_media FROM user_post_annonce, media WHERE user_post_annonce.id_user_post_annonce = media.user_post_annonce_id AND user_post_annonce.id_user_post_annonce = ? AND type = 'photo'";
        $responsePhoto = $this->getDb()->fetchAll($sql, array((int) $id_annonce));

        $sql = "SELECT media.url_media FROM user_post_annonce, media WHERE user_post_annonce.id_user_post_annonce = media.user_post_annonce_id AND user_post_annonce.id_user_post_annonce = ? AND type = 'video'";
        $responseVideo = $this->getDb()->fetchAssoc($sql, array((int) $id_annonce));

        $response = array();

        $response['annonce'] = $responseAnnonce;
        $response['photo'] = $responsePhoto;
        $response['video'] = $responseVideo;

        return $response;

    }
}
