<?php

namespace Coolloc\Model;

use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Doctrine\DBAL\Connection;
use Silex\Application;

class UpdateAnnonceModelDAO extends Model{

    private $db;

    function __construct(Connection $connect) {
        $this->db = $connect;
    }

    // public function listeVille() {
    //     $sql = "SELECT ville_id, ville_nom_reel FROM city";
    //     $ville = $this->db->fetchAll($sql);
    //
    //     return $ville;
    // }

    public function updateAnnonceAction(array $arrayAnnonce, array $arrayMedia, Application $app){

        // On vérifie dans la BDD que la ville est correcte
        $sql = "SELECT ville_id FROM city WHERE ville_nom_reel = ?";

        $ville_id = $this->db->fetchAssoc($sql, array((string) $arrayAnnonce['ville']));

        // Si c'est faux on stop la requête
        if ($ville_id == false)
            return "ville_invalid";

        // Selection ID options
        $sql = "SELECT options.id_options FROM options, user_post_annonce WHERE user_post_annonce.options_id = options.id_options AND user_post_annonce.id_user_post_annonce = ?";
        $optionsId = $this->db->fetchAssoc( $sql, array((int) $arrayAnnonce['id_annonce']) );

        // Selection des photos
        $sql = "SELECT media.id_media, media.url_media FROM media, user_post_annonce WHERE user_post_annonce.id_user_post_annonce = media.user_post_annonce_id AND user_post_annonce.id_user_post_annonce = ? AND type = 'photo'";
        $photo = $this->db->fetchAll( $sql, array((int) $arrayAnnonce['id_annonce']) );
        $nbPhotoInBDD = count($photo);

        // Selection de l'ID video
        $sql = "SELECT media.id_media FROM media, user_post_annonce WHERE user_post_annonce.id_user_post_annonce = media.user_post_annonce_id AND user_post_annonce.id_user_post_annonce = ? AND type = 'video'";
        $videoId = $this->db->fetchAssoc( $sql, array((int) $arrayAnnonce['id_annonce']) );

        // // fonction permettant de controler combien de champs remplis du formulaire sont identiques a ceux deja présents dans la base de données.
        // $sql = "SELECT * FROM options WHERE id_options = ?"; // on selectionne les infos enregistrées de l'utilisateur
        // $resultat = $this->db->fetchAssoc($sql, array((int) $optionsId));
        // $countOptions = 1; // on créé une variable compteur definie a 0 par defaut
        // foreach ($resultat as $key => $value){ // on parcour les valeur des champs saisis par l'utilisateur
        //   if(isset($arrayAnnonce[$key]) && $arrayAnnonce[$key] == $value) // on compare ses valeurs de champs saisis à ceux de la BDD
        //     $countOptions++; // si la valeur d'un champs saisi est identique a la valeur du champs deja present dans la BDD, on incrémente le compteur de 1.
        // }
        //
        // // fonction permettant de controler combien de champs remplis du formulaire sont identiques a ceux deja présents dans la base de données.
        // $sql = "SELECT * FROM user_post_annonce WHERE id_user_post_annonce = ?"; // on selectionne les infos enregistrées de l'utilisateur
        // $resultat = $this->db->fetchAssoc($sql, array((int) $arrayAnnonce['id_annonce']));
        // $countAnnonce = 1; // on créé une variable compteur definie a 0 par defaut
        // foreach ($resultat as $key => $value){ // on parcour les valeur des champs saisis par l'utilisateur
        //   if(isset($arrayAnnonce[$key]) && $arrayAnnonce[$key] == $value) // on compare ses valeurs de champs saisis à ceux de la BDD
        //     $countAnnonce++; // si la valeur d'un champs saisi est identique a la valeur du champs deja present dans la BDD, on incrémente le compteur de 1.
        // }

        // var_dump($countAnnonce);
        // var_dump($nbPhotoInBDD);
        // die();

        $arrayAnnonce['handicap_access'] = ( !empty($arrayAnnonce['handicap_access']) ) ? $arrayAnnonce['handicap_access'] : 'non';
        $arrayAnnonce['smoking'] = ( !empty($arrayAnnonce['smoking']) ) ? $arrayAnnonce['smoking'] : 'non';
        $arrayAnnonce['animals'] = ( !empty($arrayAnnonce['animals']) ) ? $arrayAnnonce['animals']: 'non';
        $arrayAnnonce['sex_roommates'] = ( !empty($arrayAnnonce['sex_roommates']) ) ? $arrayAnnonce['sex_roommates'] : 'peu importe';
        $arrayAnnonce['furniture'] = ( !empty($arrayAnnonce['furniture']) ) ? $arrayAnnonce['furniture'] : 'peu importe';
        $arrayAnnonce['garden'] = ( !empty($arrayAnnonce['garden']) ) ? $arrayAnnonce['garden'] : 'non';
        $arrayAnnonce['balcony'] = ( !empty($arrayAnnonce['balcony']) ) ? $arrayAnnonce['balcony'] : 'non';
        $arrayAnnonce['parking'] = ( !empty($arrayAnnonce['parking']) ) ? $arrayAnnonce['parking'] : 'non';

        $sql = "UPDATE options SET handicap_access = ?, smoking = ?, animals = ?, equipment = ?, sex_roommates = ?, furniture = ?, garden = ?, balcony = ?, parking = ?, district = ?, hobbies = ?, date_dispo = ?, member_profil = ? WHERE id_options = ?";

        $updateOption = $this->db->executeUpdate( $sql, array(
            (string) $arrayAnnonce['handicap_access'],
            (string) $arrayAnnonce['smoking'],
            (string) $arrayAnnonce['animals'],
            (string) $arrayAnnonce['equipment'],
            (string) $arrayAnnonce['sex_roommates'],
            (string) $arrayAnnonce['furniture'],
            (string) $arrayAnnonce['garden'],
            (string) $arrayAnnonce['balcony'],
            (string) $arrayAnnonce['parking'],
            (string) $arrayAnnonce['district'],
            (string) $arrayAnnonce['hobbies'],
            (string) $arrayAnnonce['date_dispo'],
            (string) $arrayAnnonce['member_profil'],
            (int) $optionsId['id_options']
        ));

        // if ($countOptions == 12){
        //     $updateOption = 1;
        // }

        $sql = "UPDATE user_post_annonce SET ville_id = ?, name_coloc = ?, adress = ?, adress_details = ?, postal_code = ?, mail_annonce = ?, tel_annonce = ?, rent = ?, surface= ?, nb_room = ?, description = ?, housing_type = ?, nb_roommates = ? WHERE id_user_post_annonce = ?";

        $updateAnnonce = $this->db->executeUpdate( $sql, array(
            (int) $ville_id['ville_id'],
            (string) $arrayAnnonce['name_coloc'],
            (string) $arrayAnnonce['adress'],
            (string) $arrayAnnonce['adress_details'],
            (int) $arrayAnnonce['postal_code'],
            (string) $arrayAnnonce['mail_annonce'],
            (string) $arrayAnnonce['tel_annonce'],
            (float) $arrayAnnonce['rent'],
            (float) $arrayAnnonce['surface'],
            (int) $arrayAnnonce['nb_room'],
            (string) $arrayAnnonce['description'],
            (string) $arrayAnnonce['housing_type'],
            (int) $arrayAnnonce['nb_roommates'],
            (int) $arrayAnnonce['id_annonce']
        ));

        // if ($countAnnonce == 13)
        //     $updateAnnonce = 1;

        if (isset($arrayMedia)) {

            $countUpdateMedia = 0;

            foreach ($arrayMedia as $key => $value) {
                if ($key == "video") {

                    if ($videoId != false) {
                        $sql = "UPDATE media SET url_media = ? WHERE user_post_annonce_id = ? AND type='video'";

                        $updateMedia = $this->db->executeUpdate($sql, array(
                            (string) $value,
                            (int) $arrayAnnonce['id_annonce']
                        ));

                        if ($updateMedia == 1) {
                            $countUpdateMedia++;
                        }
                    }else {
                        $updateMedia = $this->db->insert('media', array(
                            'user_post_annonce_id' => $arrayAnnonce['id_annonce'],
                            'type' => 'video',
                            'url_media' => $value,
                        ));
                        if ($updateMedia == 1) {
                            $countUpdateMedia++;
                        }
                    }

                }else {

                    for ($i = 1; $i <= $nbPhotoInBDD; $i++) {

                        if (isset($arrayMedia["photo$i"])) {

                            $index = ($i - 1);

                            $sql = "UPDATE media SET url_media = ? WHERE id_media = ?";

                            $updateMedia = $this->db->executeUpdate($sql, array(
                                (string) $arrayMedia["photo$i"],
                                (int) $photo[$index]['id_media']
                            ));

                            if ($updateMedia == 1) {
                                $countUpdateMedia++;
                            }
                        }

                    }

                    for ($i = ($nbPhotoInBDD + 1); $i < 13; $i++) {

                        if (isset($arrayMedia["photo$i"])) {

                            $updateMedia = $this->db->insert('media', array(
                                'user_post_annonce_id' => $arrayAnnonce['id_annonce'],
                                'type' => 'photo',
                                'url_media' => $arrayMedia["photo$i"],
                            ));

                            if ($updateMedia == 1) {
                                $countUpdateMedia++;
                            }

                        }
                    }
                }
            }
        }
        // var_dump($updateOption);
        // var_dump($updateAnnonce);
        // var_dump($updateMedia);
        // die();
        if ($updateOption == 1 || $updateAnnonce == 1 || $countUpdateMedia != 0) {
            return "OK";
        }else {
            return false;
        }
    }

    public function allAnnoncesSelected(){

        $sql = "SELECT * FROM annonce_options_city";
        $users = $this->getDb()->fetchAll($sql, array());

        return $users;
    }

    public static function allPhotosByIdAnnonce(int $id_annonce, Application $app){

        $sql = "SELECT media.url_media FROM media, user_post_annonce WHERE user_post_annonce.id_user_post_annonce = media.user_post_annonce_id AND user_post_annonce.id_user_post_annonce = ? AND type = 'photo'";
        $photo = $app['db']->fetchAll($sql, array((int) $id_annonce));

        return $photo;
    }
}
