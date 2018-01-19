<?php

namespace Coolloc\Model;

use Coolloc\Model\Model;
use Doctrine\DBAL\Connection;
use Silex\Application;

class SearchAnnonceModelDAO{

    private $db;

    function __construct(Connection $connect) {
        $this->db = $connect;
    }

    protected function getDB() {
        return $this->db;
    }

    public function searchAnnonce(array $arraySearch, Application $app){
        if (!empty($arraySearch)) {

            // SI CITY N'EST PAS VIDE
            (empty($arraySearch['city'])) ? $city = "" : $city = 'AND city.ville_nom_reel = "' . $arraySearch['city'] . '"';
            // SI MINRENT N'EST PAS VIDE
            (empty($arraySearch['minRent'])) ? $minRent = "" : $minRent = 'AND user_post_annonce.rent >= "' . $arraySearch['minRent'] . '"';
            // SI MAXRENT N'EST PAS VIDE
            (empty($arraySearch['maxRent'])) ? $maxRent = "" : $maxRent = 'AND user_post_annonce.rent <= "' . $arraySearch['maxRent'] . '"';
            // SI DATE_DIPSO_ANNONCE N'EST PAS VIDE
            (empty($arraySearch['date_dispo_annonce'])) ? $date_dispo = "" : $date_dispo = 'AND options.date_dispo >= "' . $arraySearch['date_dispo_annonce'] . '"';
            // SI HOUSING_TYPE N'EST PAS VIDE
            (empty($arraySearch['housing_type'])) ? $housing_type = "" : $housing_type = 'AND user_post_annonce.housing_type = "' . $arraySearch['housing_type'] . '"';
            // SI NB_ROOMMATES N'EST PAS VIDE
            (empty($arraySearch['nb_roommates'])) ? $nb_roommates = "" : $nb_roommates = 'AND user_post_annonce.nb_roommates = "' . $arraySearch['nb_roommates'] . '"';
            if (empty($arraySearch['nb_roommates'])) {
                $nb_roommates = "";
            }else {
                if ($arraySearch['nb_roommates'] == 0) {
                    $nb_roommates = 'AND user_post_annonce.nb_roommates >= "0" AND user_post_annonce.nb_roommates <= "2"';
                }else if ($arraySearch['nb_roommates'] == 1) {
                    $nb_roommates = 'AND user_post_annonce.nb_roommates >= "0" AND user_post_annonce.nb_roommates <= "4"';
                }else if ($arraySearch['nb_roommates'] == 2) {
                    $nb_roommates = 'AND user_post_annonce.nb_roommates >= "0" AND user_post_annonce.nb_roommates <= "6"';
                }else if ($arraySearch['nb_roommates'] == 3) {
                    $nb_roommates = 'AND user_post_annonce.nb_roommates >= "0" AND user_post_annonce.nb_roommates <= "8"';
                }else if ($arraySearch['nb_roommates'] == 4) {
                    $nb_roommates = 'AND user_post_annonce.nb_roommates >= "0"';
                }
            }
            // SI SURFACE N'EST PAS VIDE
            (empty($arraySearch['surface'])) ? $surface = "" : $surface = 'AND user_post_annonce.surface = "' . $arraySearch['surface'] . '"';
            // SI SEX_ROOMMATES N'EST PAS VIDE
            (empty($arraySearch['sex_roommates']) || $arraySearch['sex_roommates'] == 'mixte') ? $sex_roommates = "" : $sex_roommates = 'AND options.sex_roommates = "' . $arraySearch['sex_roommates'] . '"';
            // SI HANDICAP_ACCESS N'EST PAS VIDE
            if (empty($arraySearch['handicap_access']) || $arraySearch['handicap_access'] == 'peu importe') {
                $handicap_access = "";
            }else {
                if ($arraySearch['handicap_access'] == 'oui') {
                    $handicap_access = 'AND options.handicap_access != "non"';
                }else if ($arraySearch['handicap_access'] == 'non') {
                    $handicap_access = 'AND options.handicap_access != "oui"';
                }
            }
            // SI SMOKING N'EST PAS VIDE
            if (empty($arraySearch['smoking']) || $arraySearch['smoking'] == 'peu importe') {
                $smoking = "";
            }else {
                if ($arraySearch['smoking'] == 'oui') {
                    $smoking = 'AND options.smoking != "non"';
                }else if ($arraySearch['smoking'] == 'non') {
                    $smoking = 'AND options.smoking != "oui"';
                }
            }
            // SI ANIMALS N'EST PAS VIDE
            if (empty($arraySearch['animals']) || $arraySearch['animals'] == 'peu importe') {
                $animals = "";
            }else {
                if ($arraySearch['animals'] == 'oui') {
                    $animals = 'AND options.animals != "non"';
                }else if ($arraySearch['animals'] == 'non') {
                    $animals = 'AND options.animals != "oui"';
                }
            }
            // SI FURNITURE N'EST PAS VIDE
            if (empty($arraySearch['furniture']) || $arraySearch['furniture'] == 'peu importe') {
                $furniture = "";
            }else {
                if ($arraySearch['furniture'] == 'oui') {
                    $furniture = 'AND options.furniture != "non"';
                }else if ($arraySearch['furniture'] == 'non') {
                    $furniture = 'AND options.furniture != "oui"';
                }
            }
            // SI GARDEN N'EST PAS VIDE
            if (empty($arraySearch['garden']) || $arraySearch['garden'] == 'peu importe') {
                $garden = "";
            }else {
                if ($arraySearch['garden'] == 'oui') {
                    $garden = 'AND options.garden != "non"';
                }else if ($arraySearch['garden'] == 'non') {
                    $garden = 'AND options.garden != "oui"';
                }
            }
            // SI BALCONY N'EST PAS VIDE
            if (empty($arraySearch['balcony']) || $arraySearch['balcony'] == 'peu importe') {
                $balcony = "";
            }else {
                if ($arraySearch['balcony'] == 'oui') {
                    $balcony = 'AND options.balcony != "non"';
                }else if ($arraySearch['balcony'] == 'non') {
                    $balcony = 'AND options.balcony != "oui"';
                }
            }
            // SI PARKING N'EST PAS VIDE
            if (empty($arraySearch['parking']) || $arraySearch['parking'] == 'peu importe') {
                $parking = "";
            }else {
                if ($arraySearch['parking'] == 'oui') {
                    $parking = 'AND options.parking != "non"';
                }else if ($arraySearch['parking'] == 'non') {
                    $parking = 'AND options.parking != "oui"';
                }
            }
            // SI ACTIVITY N'EST PAS VIDE
            (empty($arraySearch['activity'])) ? $activity = "" : $activity = 'AND user.activity = "' . $arraySearch['activity'] . '"';

            $conditionWhere = "$city $minRent $maxRent $date_dispo $housing_type $nb_roommates $surface $sex_roommates $handicap_access $smoking $animals $furniture $garden $balcony $parking $activity " . $arraySearch['district'] . $arraySearch['equipment'] . $arraySearch['member_profil'] . $arraySearch['hobbies'];

            // REQUETE DE SELECTION
            $sql = "SELECT * FROM user, user_post_annonce, options, city, media WHERE user_post_annonce.user_id = user.id_user AND user_post_annonce.ville_id = city.ville_id AND user_post_annonce.options_id = options.id_options AND user_post_annonce.id_user_post_annonce = media.user_post_annonce_id " . $conditionWhere . " GROUP BY user_post_annonce.id_user_post_annonce";
            $response = $app['db']->fetchAll($sql);

            // echo $conditionWhere;
            echo "<pre>";
            var_dump($response);
            echo "</pre>";
            // var_dump($city);
            // die();

            // Si tout est OK retour de la réponse
            return $response;
        }else {
            return false;
        }
    }
}
