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
            (empty($arraySearch['city'])) ? : $city = "AND city.ville_nom_reel = " . $arraySearch['city'] . " ";
            // SI MINRENT N'EST PAS VIDE
            (empty($arraySearch['minRent'])) ? : $minRent = "AND user_post_annonce.rent >= " . $arraySearch['minRent'] . " ";
            // SI MAXRENT N'EST PAS VIDE
            (empty($arraySearch['maxRent'])) ? : $maxRent = "AND user_post_annonce.rent <= " . $arraySearch['maxRent'] . " ";
            // SI DATE_DIPSO_ANNONCE N'EST PAS VIDE
            (empty($arraySearch['date_dispo_annonce'])) ? : $date_dispo = "AND options.date_dispo >= " . $arraySearch['date_dispo_annonce'] . " ";
            // SI HOUSING_TYPE N'EST PAS VIDE
            (empty($arraySearch['housing_type'])) ? : $housing_type = "AND user_post_annonce.housing_type = " . $arraySearch['housing_type'] . " ";
            // SI NB_ROOMMATES N'EST PAS VIDE
            (empty($arraySearch['nb_roommates'])) ? : $nb_roommates = "AND user_post_annonce.nb_roommates = " . $arraySearch['nb_roommates'] . " ";
            // SI SURFACE N'EST PAS VIDE
            (empty($arraySearch['surface'])) ? : $surface = "AND user_post_annonce.surface = " . $arraySearch['surface'] . " ";
            // SI SEX_ROOMMATES N'EST PAS VIDE
            (empty($arraySearch['sex_roommates'])) ? : $sex_roommates = "AND user_post_annonce.sex_roommates = " . $arraySearch['sex_roommates'] . " ";
            // SI HANDICAP_ACCESS N'EST PAS VIDE
            (empty($arraySearch['handicap_access'])) ? : $handicap_access = "AND options.handicap_access = " . $arraySearch['handicap_access'] . " ";
            // SI SMOKING N'EST PAS VIDE
            (empty($arraySearch['smoking'])) ? : $smoking = "AND options.smoking = " . $arraySearch['smoking'] . " ";
            // SI ANIMALS N'EST PAS VIDE
            (empty($arraySearch['animals'])) ? : $animals = "AND options.animals = " . $arraySearch['animals'] . " ";
            // SI FURNITURE N'EST PAS VIDE
            (empty($arraySearch['furniture'])) ? : $furniture = "AND options.furniture = " . $arraySearch['furniture'] . " ";
            // SI GARDEN N'EST PAS VIDE
            (empty($arraySearch['garden'])) ? : $garden = "AND options.garden = " . $arraySearch['garden'] . " ";
            // SI BALCONY N'EST PAS VIDE
            (empty($arraySearch['balcony'])) ? : $balcony = "AND options.balcony = " . $arraySearch['balcony'] . " ";
            // SI PARKING N'EST PAS VIDE
            (empty($arraySearch['parking'])) ? : $parking = "AND options.parking = " . $arraySearch['parking'] . " ";
            // SI ACTIVITY N'EST PAS VIDE
            (empty($arraySearch['activity'])) ? : $activity = "AND options.activity = " . $arraySearch['activity'] . " ";
            // SI ACTIVITY N'EST PAS VIDE
            // (empty($arraySearch['activity'])) ? : $activity = "AND options.activity = " . $arraySearch['activity'] . " ";


            echo "<pre>";
            var_dump($arraySearch);
            var_dump($city);
            echo "</pre>";
            die();


            return true;
        }else {
            return false;
        }
    }
}
