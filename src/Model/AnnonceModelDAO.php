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
            // $user['id']
            $sql = "SELECT ville_id FROM city WHERE ville_code_postal = ?";

            $ville_id = $this->db->fetchAssoc($sql, array((int) $arrayAnnonce['postal_code']));

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

            // var_dump($ajoutAnnonce);
            // die();
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
                            return true;
                        }
                    }else {
                        return true;
                    }

                }
            }
        }else {
            return false;
        }
    }

    public function allAnnoncesSelected(){

        $sql = "SELECT * FROM annonce_options_city";
        $users = $this->getDb()->fetchAll($sql, array());

        return $users;
    }
}
