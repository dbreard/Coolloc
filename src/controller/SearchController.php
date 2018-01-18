<?php

namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\SearchAnnonceModelDAO;


class SearchController extends Controller
{
    public function searchAction(Application $app, Request $request) {

        // ARRAY DES CHAMPS SELECT A MULTIPLES CHOIX
        $arrayDistrict = array('Proche de commerces', 'Proche d\'écoles', 'Proche de transports', 'Calme', 'Animé');
        $arrayEquipments = array('TV', 'Hifi', 'Wifi', 'Fibre optique', 'Salle de jeux', 'Machine à laver');
        $arrayMemberProfil = array('Timide', 'Bavard', 'Solitaire', 'Casanier', 'Discret', 'Convivial', 'Cool', 'Extraverti', 'Ordonné', 'Tolérant', 'Sportif', 'Fétard', 'Studieux', 'Curieux', 'Joyeux', 'Respectueux');
        $arrayHobbies = array('Ciné - TV - Série', 'Littérature', 'Musique', 'Jeux vidéo', 'Jeux plateau - Société', 'Mode', 'Shopping', 'Sport', 'Cuisine - Pâtisserie', 'Sorties culturelles', 'Voyages', 'Autres');

        // RECUPERATION ET NETTOYAGE DES CHAMPS
        // CITY
        $city = $this->formatCity($request->get('city'));

        // LOYER MIN / MAX
        // MIN
        $minRent = strip_tags(trim($request->get('min-rent')));
        if (!empty($minRent)) {
            if (!is_numeric($minRent) || $minRent < 0) {
                array_push($this->erreur, 'Prix minimum saisis invalide');
            }
        }
        // MAX
        $maxRent = strip_tags(trim($request->get('max-rent')));
        if (!empty($maxRent)) {
            if (!is_numeric($maxRent) || $maxRent <= 0) {
                array_push($this->erreur, 'Prix maximum saisis invalide');
            }
        }
        // COMPARE MIN ET MAX
        if ($maxRent < $minRent) {
            array_push($this->erreur, 'Le prix maximum ne peut pas être inférieur au prix minimum');
        }

        // DATE DE DISPO DE L'ANNONCE
        $date_dispo_annonce = strip_tags(trim($request->get('date_dispo_annonce')));
        if (!empty($date_dispo_annonce)) {
            $compareDate = str_replace('-', '', $date_dispo_annonce);
            if (($this->getDate() - $compareDate) >= 0) {
                array_push($this->erreur, "La date saisis est invalide");
            }
        }

        // NOMBRE DE COLOC
        $nb_roommates = strip_tags(trim($request->get('nb_roommates')));
        if (!empty($nb_roommates)) {
            if (!is_numeric($nb_roommates) || $nb_roommates < 0 || $nb_roommates > 4) {
                array_push($this->erreur, "Saisi invalide sur le champs 'Nombre de coloc'");
            }
        }

        // TYPE DE BIEN
        $housing_type = strip_tags(trim($request->get('housing_type')));
        if (!empty($housing_type)) {
            if ($housing_type != 'maison' && $housing_type != 'appartement' && $housing_type != 'loft' && $housing_type != 'hotel particulier' && $housing_type != 'corps de ferme' && $housing_type != 'autre') {
                array_push($this->erreur, "Saisie incorrect sur le champs 'Type de bien immobilier'");
            }
        }

        // SURFACE
        $surface = strip_tags(trim($request->get('surface')));
        if (!empty($surface)) {
            if (!is_numeric($surface) || $surface <= 0) {
                array_push($this->erreur, 'Surface saisie invalide');
            }
        }

        // SEX_ROOMMATES
        $sex_roommates = strip_tags(trim($request->get('sex_roommates')));
        if ($sex_roommates != 'homme' && $sex_roommates != 'femme' && $sex_roommates != 'mixte') {
            array_push($this->erreur, "Saisie incorrect sur le champs 'Composition de la coloc'");
        }

        // HANDICAP_ACCESS
        $handicap_access = $this->validSelect($request->get('handicap_access'), "Accés handicapé");

        // SMOKING
        $smoking = $this->validSelect($request->get('smoking'), "Fumeur");

        // ANIMALS
        $animals = $this->validSelect($request->get('animals'), "Animaux");

        // FURNITURE
        $furniture = $this->validSelect($request->get('furniture'), "Meublé");

        // GARDEN
        $garden = $this->validSelect($request->get('garden'), "Jardin");

        // BALCONY
        $balcony = $this->validSelect($request->get('balcony'), "Balcon");

        // PARKING
        $parking = $this->validSelect($request->get('parking'), "Parking");

        // DISTRICT[]
        (!$request->request->has('district')) ? $district = "" : $district = $this->validArray($request->get('district'), $arrayDistrict, 'Quartier');

        // EQUIPMENT[]
        (!$request->request->has('equipment')) ? $equipment = "" : $equipment = $this->validArray($request->get('equipment'), $arrayEquipments, 'Equipements');

        // EQUIPMENT[]
        (!$request->request->has('equipment')) ? $equipment = "" : $equipment = $this->validArray($request->get('equipment'), $arrayEquipments, 'Equipements');

        // MEMBER_PROFIL[]
        (!$request->request->has('member_profil')) ? $memberProfil = "" : $memberProfil = $this->validArray($request->get('member_profil'), $arrayMemberProfil, 'Profil collocataires');

        // HOBBIES[]
        (!$request->request->has('hobbies')) ? $hobbies = "" : $hobbies = $this->validArray($request->get('hobbies'), $arrayHobbies, 'Centre d\'intérêts');

        // ACTIVITY
        if (!empty($request->get('activity'))) {
            $activity = strip_tags(trim($request->get('activity')));

            if ($activity != 'étudiant' && $activity != 'activité pro' && $activity != 'retraité' && $activity != 'sans activité') {
                array_push($this->erreur, "Saisie incorrect sur le champs 'Choix d'activité'");
            }
        }else {
            $activity = "";
        }

        // SI IL Y A DES ERREURS
        if (!empty($this->erreur)) {
            return $app['twig']->render('/details-annonce.html.twig', array(
                "error" => $this->erreur,
            ));
        }else {
            $arraySearch = array(
                "city" => $city,
                "minRent" => $minRent,
                "maxRent" => $maxRent,
                "date_dispo_annonce" => $date_dispo_annonce,
                "housing_type" => $housing_type,
                "nb_roommates" => $nb_roommates,
                "surface" => $surface,
                "sex_roommates" => $sex_roommates,
                "handicap_access" => $handicap_access,
                "smoking" => $smoking,
                "animals" => $animals,
                "furniture" => $furniture,
                "garden" => $garden,
                "balcony" => $balcony,
                "parking" => $parking,
                "activity" => $activity,
                "district" => $district,
                "equipment" => $equipment,
                "member_profil" => $memberProfil,
                "hobbies" => $hobbies,
            );

            // echo "<pre>";
            // var_dump($arraySearch);
            // echo "</pre>";
            // die();

            $searchAnnonce = new SearchAnnonceModelDAO($app['db']);

            if ($searchAnnonce->searchAnnonce($arraySearch, $app)) {
                return $app['twig']->render('details-annonce.html.twig');
            }else {
                return $app['twig']->render('index.html.twig', array("error" => "Erreur lors de la recherche, veuillez réessayer"));
            }
        }
    }
}
