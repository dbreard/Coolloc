<?php

namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Coolloc\Model\SearchAnnonceModelDAO;


class SearchController extends Controller
{
    public function searchAction(Application $app, Request $request) {

        $isconnected = Controller::ifConnected();
        $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();
        $userSearchColocation = Controller::userSearchColocation($app);

        // ARRAY DES CHAMPS SELECT A MULTIPLES CHOIX
        $arrayDistrict = array('Proche de commerces', 'Proche d\'écoles', 'Proche de transports', 'Calme', 'Animé');
        $arrayEquipment = array('TV', 'Hifi', 'Wifi', 'Fibre optique', 'Salle de jeux', 'Machine à laver');
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
            if (!is_numeric($maxRent) || $maxRent < 0) {
                array_push($this->erreur, 'Prix maximum saisis invalide');
            }
        }else {
            $maxRent = "9999999";
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

        // ACTIVITY
        if (!empty($request->get('activity'))) {
            $activity = strip_tags(trim($request->get('activity')));

            if ($activity != 'étudiant' && $activity != 'activité pro' && $activity != 'retraité' && $activity != 'sans activité') {
                array_push($this->erreur, "Saisie incorrect sur le champs 'Choix d'activité'");
            }
        }else {
            $activity = "";
        }

        // Je vérifie l'éxistance du champs 'district' dans mon POST et s'il existe je le traite
        ($request->request->has('district')) ? $district = $this->verifArrayAndFormat($request->get('district'), $arrayDistrict, 'Quartier', 'SELECT') : $district = "";

        // Je vérifie l'éxistance du champs 'equipment' dans mon POST et s'il existe je le traite
        ($request->request->has('equipment')) ? $equipment = $this->verifArrayAndFormat($request->get('equipment'), $arrayEquipment, 'Equipement', 'SELECT') : $equipment = "";

        // Je vérifie l'éxistance du champs 'member_profil' dans mon POST et s'il existe je le traite
        ($request->request->has('member_profil')) ? $member_profil = $this->verifArrayAndFormat($request->get('member_profil'), $arrayMemberProfil, 'Profil colocataires', 'SELECT') : $member_profil = "";

        // Je vérifie l'éxistance du champs 'hobbies' dans mon POST et s'il existe je le traite
        ($request->request->has('hobbies')) ? $hobbies = $this->verifArrayAndFormat($request->get('hobbies'), $arrayHobbies, "Centre d'intérêts", 'SELECT') : $hobbies = "";

        // SI IL Y A DES ERREURS
        if (!empty($this->erreur)) {
            if ($isConnectedAndAdmin){
                return $app['twig']->render('serp-annonce.html.twig', array(
                    "isConnectedAndAmin" => $isConnectedAndAdmin,
                    "connected" => $isconnected,
                    "error" => $this->erreur,
                ));
            }

            elseif ($isconnected) {
                return $app['twig']->render('serp-annonce.html.twig', array(
                    "connected" => $isconnected,
                    "error" => $this->erreur,
                ));
            } else {
                return $app['twig']->render('serp-annonce.html.twig', array(
                    "error" => $this->erreur,
                ));
            }
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
                "member_profil" => $member_profil,
                "hobbies" => $hobbies,
            );

            // echo "<pre>";
            // var_dump($arraySearch);
            // echo "</pre>";
            // die();

            $searchAnnonce = new SearchAnnonceModelDAO($app['db']);

            $response = $searchAnnonce->searchAnnonce($arraySearch, $app);

            $nbResponse = count($response);


            if ($response != false) {

                if ($isConnectedAndAdmin){
                    return $app['twig']->render('serp-annonce.html.twig', array(
                        "isConnectedAndAmin" => $isConnectedAndAdmin,
                        "connected" => $isconnected,
                        "affichage" => $response,
                        "nb_resultats" => $nbResponse,
                        "userSearchColocation" => $userSearchColocation,
                    ));
                }

                elseif ($isconnected) {
                    return $app['twig']->render('serp-annonce.html.twig', array(
                        "connected" => $isconnected,
                        "affichage" => $response,
                        "nb_resultats" => $nbResponse,
                        "userSearchColocation" => $userSearchColocation,
                    ));
                } else {
                    return $app['twig']->render('serp-annonce.html.twig', array(
                        "affichage" => $response,
                        "nb_resultats" => $nbResponse,
                    ));
                }

            }else {

                $membresAnnoncesInfo = new HomeController;
                $donneesMembresAnnonces = $membresAnnoncesInfo->homeAction($app);

                if ($isConnectedAndAdmin){
                    return $app['twig']->render('index.html.twig', array(
                        "isConnectedAndAmin" => $isConnectedAndAdmin,
                        "connected" => $isconnected,
                        "userSearchColocation" => $userSearchColocation,
                        "errorSearch" => "Aucun résultat pour votre recherche, changé quelques critères pour voir apparaître les annonces",
                        "affichage" => $donneesMembresAnnonces,
                    ));
                }

                elseif ($isconnected) {
                    return $app['twig']->render('index.html.twig', array(
                        "connected" => $isconnected,
                        "userSearchColocation" => $userSearchColocation,
                        "errorSearch" => "Aucun résultat pour votre recherche, changé quelques critères pour voir apparaître les annonces",
                        "affichage" => $donneesMembresAnnonces,
                    ));
                }
                else {
                    return $app['twig']->render('index.html.twig', array(
                        "errorSearch" => "Aucun résultat pour votre recherche, changé quelques critères pour voir apparaître les annonces",
                        "affichage" => $donneesMembresAnnonces,
                    ));
                }
            }
        }
    }

    public function searchAllAnnonce(Application $app, Request $request) {

        $isconnected = Controller::ifConnected();
        $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();

        $pageActuelle = strip_tags(trim($request->get("page")));

        // ajout pour verif si user = search colocation
        $userSearchColocation = Controller::userSearchColocation($app);
        //////////////////////////////

        $response = Model::searchAllAnnonceExist($app);

        $a = (int) $response['count'];
        $maxPage = (int)ceil( $a / $app['nbFilterAnnonce']);

        $annonce = array();

        // La fonction ceil() arrondit à l'entier supérieur.

        $page = ''; // Le numéro de la page que nous souhaitons visualiser
            if (isset($pageActuelle) && !empty($pageActuelle) && ctype_digit($pageActuelle) && $pageActuelle > 0 && $page <= $maxPage) // On vérifie si la page est bien un nombre compris entre 1 et $maxPage
            {
                $page = $pageActuelle;
            }
            else // Si le paramètre n'est pas spécifié ou n'est pas un nombre valide
            {
                $page = 1;
            }

        // Maintenant, nous avons le numéro de page. Nous pouvons en déduire les enregistrements à afficher :
        $offset = ($page - 1) * 12;   // Si on est à la page 1, (1-1)*10 = OFFSET 0, si on est à la page 2, (2-1)*10 = OFFSET 10, etc.

        $annonce['annonce'] = Model::searchAllAnnonceExistLimitDesc($app, $app['nbFilterAnnonce'], $offset);
        $annonce['page'] = $page;
        $annonce['maxPage'] = $maxPage;


        if ($isConnectedAndAdmin){
            return $app['twig']->render('serp-annonce.html.twig', array(
                "isConnectedAndAmin" => $isConnectedAndAdmin,
                "connected" => $isconnected,
                "userSearchColocation" => $userSearchColocation,
                "affichage" => $annonce['annonce']['search'],
                "nb_resultats" => $response['count'],
                "annonce" => $annonce,

            ));
        }

        elseif ($isconnected) {
            return $app['twig']->render('serp-annonce.html.twig', array(
                "connected" => $isconnected,
                "userSearchColocation" => $userSearchColocation,
                "affichage" => $annonce['annonce']['search'],
                "nb_resultats" => $response['count'],
                "annonce" => $annonce,

        ));
        } else {
            return $app['twig']->render('serp-annonce.html.twig', array(
                "affichage" => $annonce['annonce']['search'],
                "nb_resultats" => $response['count'],
                "annonce" => $annonce,
            ));
        }
    }



    // SELECTION DE TOUT LES PROFILS AVEC SYSTEME DE PAGINATION
    public function searchAllProfils(Application $app, Request $request){

        $membres = new UserModelDAO($app['db']);
        $pageActuelle = strip_tags(trim($request->get("page")));

        $nbProfils = $membres->countAllUsers();
        $a = (int) $nbProfils;
        $maxPage = (int)ceil( $a / $app['nbFilterProfil']);

        $membre = array();

        // La fonction ceil() arrondit à l'entier supérieur.

        $page = ''; // Le numéro de la page que nous souhaitons visualiser
            if (isset($pageActuelle) && !empty($pageActuelle) && ctype_digit($pageActuelle) && $pageActuelle > 0 && $page <= $maxPage) // On vérifie si la page est bien un nombre compris entre 1 et $maxPage
            {
                $page = $pageActuelle;
            }
            else // Si le paramètre n'est pas spécifié ou n'est pas un nombre valide
            {
                $page = 1;
            }

        // Maintenant, nous avons le numéro de page. Nous pouvons en déduire les enregistrements à afficher :
        $offset = ($page - 1) * 8;   // Si on est à la page 1, (1-1)*10 = OFFSET 0, si on est à la page 2, (2-1)*10 = OFFSET 10, etc.

        $membre['profil'] = $membres->UsersColocationSelectedLimitOffset($app['nbFilterProfil'], $offset);
        $membre['page'] = $page;
        $membre['maxPage'] = $maxPage;

        return $membre;
    }
}
