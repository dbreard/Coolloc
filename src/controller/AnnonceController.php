<?php

namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AnnonceController extends Controller
{
    public function annonceAction(Application $app, Request $request) {

        // Si il y a des erreurs enregistré par le middleware on redirige vers la page ajout-annonce
        if ($app["formulaire"]["verifParamAnnonce"]["error"] == true)
            return  $app['twig']->render('/connected/ajout-annonce.html.twig', $app["formulaire"]["verifParamAnnonce"]["value_form"]);

        // ARRAY DES CHAMPS SELECT A MULTIPLES CHOIX
        $arrayDistrict = array('Proche de commerces', 'Proche d\'écoles', 'Proche de transports', 'Calme', 'Animé');
        $arrayEquipments = array('TV', 'Hifi', 'Wifi', 'Fibre optique', 'Salle de jeux', 'Machine à laver');
        $arrayMemberProfil = array('Timide', 'Bavard', 'Solitaire', 'Casanier', 'Discret', 'Convivial', 'Cool', 'Extraverti', 'Ordonné', 'Tolérant', 'Sportif', 'Fétard', 'Studieux', 'Curieux', 'Joyeux', 'Respectueux');
        $arrayHobbies = array('Ciné - TV - Série', 'Littérature', 'Musique', 'Jeux vidéo', 'Jeux plateau - Société', 'Mode', 'Shopping', 'Sport', 'Cuisine - Pâtisserie', 'Sorties culturelles', 'Voyages', 'Autres');

        // CHAMPS OBLIGATOIRES --- SUPPRESSIONS DES BALISES PHP ET DES ESPACES FORCER
        $name_coloc = strip_tags(trim($request->get('name_coloc')));
        $rent = strip_tags(trim($request->get('rent')));
        $description = strip_tags(trim($request->get('description')));
        $postal_code = strip_tags(trim($request->get('postal_code')));
        $adress = strip_tags(trim($request->get('adress')));
        $city = strip_tags(trim($request->get('city')));
        $date_dispo = strip_tags(trim($request->get('date_dispo')));
        $dateFormatage = str_replace("-", "", $date_dispo);
        $nb_roommates = strip_tags(trim($request->get('nb_roommates')));
        $conditions = strip_tags(trim($request->get('conditions')));

        // CHAMPS OBLIGATOIRES && saisie FACULTATIVES --- SUPPRESSIONS DES BALISES PHP ET DES ESPACES FORCER
        // EMAIL
        // $mail_annonce = strip_tags(trim($request->get('mail_annonce')));
        // if (!empty($mail_annonce)) {
        //     ($this->verifEmail($mail_annonce)) ? : array_push($this->erreur, 'Email invalide');
        // }else {
        //     charger l'email du profil de l'utilisateur
        //     $annonceMail = new AnnonceModel($app['db']);
        //     $annonceMail->searchMail($_SESSION['id']);
        // }

        // TEL
        // $tel_annonce = strip_tags(trim($request->get('tel_annonce')));
        // if (!empty($tel_annonce)) {
        //     ($this->verifTel($tel_annonce)) ? $tel_annonce = $this->modifyTel($tel_annonce) : array_push($this->erreur, 'Numéro de téléphone invalide');
        // }else {
        //         charger le numéro de téléphone du profil de l'utilisateur
        //         $annonceTel = new AnnonceModel($app['db']);
        //         $annonceTel->searchTel($_SESSION['id']);
        // }

        // CHAMPS FACULTATIFS
        // ADRESSE DETAILLES
        $adress_details = strip_tags(trim($request->get('adress_details')));
        if (!empty($adress_details)) {
            if (iconv_strlen($adress_details) >= 300) {
                array_push($this->erreur, 'Adresse détaillée invalide');
            }
        }


        // SURFACE
        $surface = strip_tags(trim($request->get('surface')));
        if (!empty($surface)) {
            if (!is_numeric($surface) || $surface <= 0) {
                array_push($this->erreur, 'Surface saisie invalide');
            }
        }

        // NB_ROOM
        $nb_room = strip_tags(trim($request->get('nb_room')));
        if (!empty($nb_room)) {
            if (!is_numeric($nb_room) || $nb_room <= 0) {
                array_push($this->erreur, 'Nombre de pièces saisie invalide');
            }
        }

        // HANDICAP_ACCESS
        $handicap_access = strip_tags(trim($request->get('handicap_access')));
        if (!empty($handicap_access)) {
            if ($handicap_access != 'oui' || $handicap_access != 'non' || $handicap_access != 'peu importe') {
                array_push($this->erreur, "saisie incorrect sur le champs 'Accés handicapé'");
            }
        }

        // SMOKING
        $smoking = strip_tags(trim($request->get('smoking')));
        if (!empty($smoking)) {
            if ($smoking != 'oui' || $smoking != 'non' || $smoking != 'peu importe') {
                array_push($this->erreur, "saisie incorrect sur le champs 'Fumeur'");
            }
        }

        // ANIMALS
        $animals = strip_tags(trim($request->get('animals')));
        if (!empty($animals)) {
            if ($animals != 'oui' || $animals != 'non' || $animals != 'peu importe') {
                array_push($this->erreur, "saisie incorrect sur le champs 'Animaux'");
            }
        }

        // SEX_ROOMMATES
        $sex_roommates = strip_tags(trim($request->get('sex_roommates')));
        if (!empty($sex_roommates)) {
            if ($sex_roommates != 'homme' || $sex_roommates != 'femme' || $sex_roommates != 'mixte') {
                array_push($this->erreur, "saisie incorrect sur le champs 'Sexe'");
            }
        }

        // FURNITURE
        $furniture = strip_tags(trim($request->get('furniture')));
        if (!empty($furniture)) {
            if ($furniture != 'oui' || $furniture != 'non' || $furniture != 'peu importe') {
                array_push($this->erreur, "saisie incorrect sur le champs 'Meublé'");
            }
        }

        // GARDEN
        $garden = strip_tags(trim($request->get('garden')));
        if (!empty($garden)) {
            if ($garden != 'oui' || $garden != 'non' || $garden != 'peu importe') {
                array_push($this->erreur, "saisie incorrect sur le champs 'Garden'");
            }
        }

        // BALCONY
        $balcony = strip_tags(trim($request->get('balcony')));
        if (!empty($balcony)) {
            if ($balcony != 'oui' || $balcony != 'non' || $balcony != 'peu importe') {
                array_push($this->erreur, "saisie incorrect sur le champs 'Balcon'");
            }
        }

        // PARKING
        $parking = strip_tags(trim($request->get('parking')));
        if (!empty($parking)) {
            if ($parking != 'oui' || $parking != 'non' || $parking != 'peu importe') {
                array_push($this->erreur, "saisie incorrect sur le champs 'Parking'");
            }
        }

        // VIDEOS
        $video = strip_tags(trim($request->get('video')));
        if (!empty($video)) {
            if ( !preg_match(" #youtube.com|vimeo.com# " , $video) ){
                array_push($this->erreur, "l'URL de la vidéo est invalide");
            }
        }

        // SECURITE DES VALEURS DES TABLEAUX
        // DISTRICT
        if ($request->request->has('district')) {
            $arrayDistrictCheck = array();
            foreach ($request->get('district') as $key => $value) {
                $value = strip_tags(trim($value));
                foreach ($arrayDistrict as $key2 => $value2) {
                    if ($value == $value2) {
                        array_push($arrayDistrictCheck, "check");
                    }
                }
            }
            if (count($arrayDistrictCheck) != count($request->get('district'))) {
                array_push($this->erreur, "Problème de selection dans 'Quartier'");
            }
        }

        // EQUIPMENTS
        if ($request->request->has('equipments')) {
            $arrayEquipmentsCheck = array();
            foreach ($request->get('equipments') as $key => $value) {
                $value = strip_tags(trim($value));
                foreach ($arrayEquipments as $key2 => $value2) {
                    if ($value == $value2) {
                        array_push($arrayEquipmentsCheck, "check");
                    }
                }
            }
            if (count($arrayEquipmentsCheck) != count($request->get('equipments'))) {
                array_push($this->erreur, "Problème de selection dans 'Equipements'");
            }
        }

        // MEMBER PROFIL
        if ($request->request->has('member_profil')) {
            $arrayMemberProfilCheck = array();
            foreach ($request->get('member_profil') as $key => $value) {
                $value = strip_tags(trim($value));
                foreach ($arrayMemberProfil as $key2 => $value2) {
                    if ($value == $value2) {
                        array_push($arrayMemberProfilCheck, "check");
                    }
                }
            }
            if (count($arrayMemberProfilCheck) != count($request->get('member_profil'))) {
                array_push($this->erreur, "Problème de selection dans 'Profil de colocataire recherché'");
            }
        }

        // HOBBIES
        if ($request->request->has('hobbies')) {
            $arrayHobbiesCheck = array();
            foreach ($request->get('hobbies') as $key => $value) {
                $value = strip_tags(trim($value));
                foreach ($arrayHobbies as $key2 => $value2) {
                    if ($value == $value2) {
                        array_push($arrayHobbiesCheck, "check");
                    }
                }
            }
            if (count($arrayHobbiesCheck) != count($request->get('hobbies'))) {
                array_push($this->erreur, "Problème de selection dans 'Centre d'intérêts'");
            }
        }

        // VERIF LONGUEUR NOM DE COLOC
        (iconv_strlen($name_coloc) > 2 || iconv_strlen($name_coloc) <= 40) ? : array_push($this->erreur, 'Nom de coloc invalide');

        // VERIF LOYER CORRECT
        ($rent > 0 && is_numeric($rent)) ? : array_push($this->erreur, 'Loyer saisie incorrect');

        // VERIF DESCRIPTION PAS TROP LONGUE
        (iconv_strlen($description) <= 600) ? : array_push($this->erreur, 'Longueur de la description incorrect');

        // VERIF STRUCTURE DU CODE POSTAL
/*/!\*/ (iconv_strlen($postal_code) != 5 && preg_match('#^[0-9]{5,5}$#',$postal_code)) ? : array_push($this->erreur, 'Code postal saisie incorrect');

        // VERIF DATE DE DISPO VALIDE
        // (($this->getDate() - $dateFormatage) <= 0) ? : array_push($this->erreur, 'La date de disponibilité est invalide');

        // SI IL Y A DES ERREURS
        if (!empty($this->erreur)) {
            return $app['twig']->render('connected/ajout-annonce.html.twig', array(
                "error" => $this->erreur,
                "value" => $app["formulaire"]["verifParamAnnonce"]["value_form"]
            ));
        }
    }
}
