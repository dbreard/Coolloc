<?php

namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\AnnonceModelDAO;


class AnnonceController extends Controller
{
    // public function callVille(Application $app) {
    //     $listeVille = new AnnonceModelDAO($app['db']);
    //     $ville = $listeVille->listeVille();
    //
    //     if (!empty($ville)) {
    //         return $app['twig']->render('/connected/ajout-annonce.html.twig', array('ville' => $ville));
    //     }else {
    //         return $app['twig']->render('/connected/ajout-annonce.html.twig', array());
    //     }
    // }

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
        $mail_annonce = strip_tags(trim($request->get('mail_annonce')));
        if (!empty($mail_annonce)) {
            ($this->verifEmail($mail_annonce)) ? : array_push($this->erreur, 'Email saisi invalide');
        }else if ($_SESSION['membre']['zoubida']){
            $user = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);
            // charger l'email du profil de l'utilisateur
            $mail_annonce = $user['Email'];
        }else {
            array_push($this->erreur, 'Problème lors de la vérification de l\'Email, veuillez vérifier');
        }

        // TEL
        $tel_annonce = strip_tags(trim($request->get('tel_annonce')));
        if (!empty($tel_annonce)) {
            ($this->verifTel($tel_annonce)) ? $tel_annonce = $this->modifyTel($tel_annonce) : array_push($this->erreur, 'Numéro de téléphone saisi invalide');
        }else if ($_SESSION['membre']['zoubida']){
            $user = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);
            //charger le numéro de téléphone du profil de l'utilisateur
            $tel_annonce = $user['Téléphone'];
        }else {
            array_push($this->erreur, 'Problème lors de la vérification du téléphone, veuillez vérifier');
        }

        // CHAMPS FACULTATIFS
        // ADRESSE DETAILLES
        $adress_details = strip_tags(trim($request->get('adress_details')));
        if (!empty($adress_details)) {
            if (iconv_strlen($adress_details) >= 300) {
                array_push($this->erreur, 'Adresse détaillée invalide');
            }
        }

        // ADRESSE DETAILLES
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

        // NB_ROOM
        $nb_room = strip_tags(trim($request->get('nb_room')));
        if (!empty($nb_room)) {
            if (!is_numeric($nb_room) || $nb_room <= 0) {
                array_push($this->erreur, 'Nombre de pièces saisies invalide');
            }
        }

        // HANDICAP_ACCESS
        $handicap_access = strip_tags(trim($request->get('handicap_access')));
        if (!empty($handicap_access)) {
            if ($handicap_access != 'oui' && $handicap_access != 'non' && $handicap_access != 'peu importe') {
                array_push($this->erreur, "Saisie incorrect sur le champs 'Accés handicapé'");
            }
        }

        // SMOKING
        $smoking = strip_tags(trim($request->get('smoking')));
        if (!empty($smoking)) {
            if ($smoking != 'oui' && $smoking != 'non' && $smoking != "peu importe") {
                array_push($this->erreur, "Saisie incorrect sur le champs 'Fumeur'");
            }
        }

        // ANIMALS
        $animals = strip_tags(trim($request->get('animals')));
        if (!empty($animals)) {
            if ($animals != 'oui' && $animals != 'non' && $animals != 'peu importe') {
                array_push($this->erreur, "Saisie incorrect sur le champs 'Animaux'");
            }
        }

        // SEX_ROOMMATES
        $sex_roommates = strip_tags(trim($request->get('sex_roommates')));
        if (!empty($sex_roommates)) {
            if ($sex_roommates != 'homme' && $sex_roommates != 'femme' && $sex_roommates != 'mixte') {
                array_push($this->erreur, "Saisie incorrect sur le champs 'Sexe'");
            }
        }

        // FURNITURE
        $furniture = strip_tags(trim($request->get('furniture')));
        if (!empty($furniture)) {
            if ($furniture != 'oui' && $furniture != 'non' && $furniture != 'peu importe') {
                array_push($this->erreur, "Saisie incorrect sur le champs 'Meublé'");
            }
        }

        // GARDEN
        $garden = strip_tags(trim($request->get('garden')));
        if (!empty($garden)) {
            if ($garden != 'oui' && $garden != 'non' && $garden != 'peu importe') {
                array_push($this->erreur, "Saisie incorrect sur le champs 'Garden'");
            }
        }

        // BALCONY
        $balcony = strip_tags(trim($request->get('balcony')));
        if (!empty($balcony)) {
            if ($balcony != 'oui' && $balcony != 'non' && $balcony != 'peu importe') {
                array_push($this->erreur, "Saisie incorrect sur le champs 'Balcon'");
            }
        }

        // PARKING
        $parking = strip_tags(trim($request->get('parking')));
        if (!empty($parking)) {
            if ($parking != 'oui' && $parking != 'non' && $parking != "peu importe") {
                array_push($this->erreur, "Saisie incorrect sur le champs 'Parking'");
            }
        }

        // SECURITE DES VALEURS DES TABLEAUX
        // DISTRICT
        // Si district est défini
        if ($request->request->has('district')) {
            // on créer un tableau de réception
            $district = array();
            // on créer un tableau pour faire le comparatif
            $arrayDistrictCheck = array();
            // boucle sur l'ensemble des données
            foreach ($request->get('district') as $key => $value) {
                // on nettoi chaque valeur
                $value = strip_tags(trim($value));
                // pour chaque valeur on vérifie si elle est valide
                foreach ($arrayDistrict as $key2 => $value2) {
                    // Si oui on check
                    if ($value == $value2) {
                        array_push($arrayDistrictCheck, "check");
                    }
                }
            }
            // Si le nombre de valeur ne correspond pas au nombre de check erreur
            if (count($arrayDistrictCheck) != count($request->get('district'))) {
                array_push($this->erreur, "Problème de selection dans 'Quartier'");
            }else { // Sinon on serialize notre tableau en string
                $district = serialize($request->get('district'));
            }
        }else {// si il n'est pas défini
            $district = "";
        }

        // EQUIPMENTS
        if ($request->request->has('equipments')) {
            $equipment = array();
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
            }else {
                $equipment = serialize($request->get('equipments'));
            }
        }else {
            $equipment = "";
        }

        // MEMBER PROFIL
        if ($request->request->has('member_profil')) {
            $memberProfil = array();
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
            }else {
                $memberProfil = serialize($request->get('member_profil'));
            }
        }else {
            $memberProfil = "";
        }

        // HOBBIES
        if ($request->request->has('hobbies')) {
            $hobbies = array();
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
            }else {
                $hobbies = serialize($request->get('hobbies'));
            }
        }else {
            $hobbies = "";
        }

        // VERIF LONGUEUR NOM DE COLOC
        (iconv_strlen($name_coloc) > 2 || iconv_strlen($name_coloc) <= 40) ? : array_push($this->erreur, 'Nom de coloc invalide');

        // VERIF LOYER CORRECT
        ($rent > 0 && is_numeric($rent)) ? : array_push($this->erreur, 'Loyer saisie incorrect');

        // VERIF DESCRIPTION PAS TROP LONGUE
        (iconv_strlen($description) <= 600) ? : array_push($this->erreur, 'Longueur de la description incorrect');

        // VERIF STRUCTURE DU CODE POSTAL
        (iconv_strlen($postal_code) == 5 && preg_match('#^[0-9]{5,5}$#',$postal_code)) ? : array_push($this->erreur, 'Code postal saisie incorrect');

        // VERIF DATE DE DISPO VALIDE
        (($this->getDate() - $dateFormatage) <= 0) ? : array_push($this->erreur, 'La date de disponibilité est invalide');

        // TABLEAU DES MEDIAS
        $arrayMedia = array();

        //-------------- VIDEO ---------------
        $video = strip_tags(trim($request->get('video')));
        if (!empty($video)) {
            if ( !preg_match(" #youtube.com|vimeo.com# " , $video) ){
                array_push($this->erreur, "l'URL de la vidéo est invalide");
            }else {
                $arrayMedia['video'] = $video;
            }
        }

        //-------------- PHOTOS ---------------
        // Boucle sur les photos
        for ($i = 1; $i < 13; $i++) {

            $photo = "photo" . $i;
            // Si photo 1
            if ($i == 1) {
                // Si elle n'est pas vide
                if (!empty($_FILES["photo$i"]['name'])) {
                    // Je créer un préfixe pour mon image
                    $photo_name_coloc = str_replace(' ', '_', $name_coloc);
                    // Je créer le nom de la photo
                    $nom_photo = $photo_name_coloc . '-' . $_FILES["photo$i"]['name'];
                    // Je créer l'URL a insérer dans la BDD
                    $photo_bdd = $this->URL . "photos/$nom_photo";
                    // Je créer le chemin d'accés pour aller copier la photo dans le dossier "photos"
                    $photo_dossier = __DIR__ . "/../../public/photos/$nom_photo";
                    // Si elle à un nom temporaire
                    if (!empty($_FILES["photo$i"]['tmp_name'])) {
                        // Je copie la photo dans le dossier
                        copy($_FILES["photo$i"]['tmp_name'], $photo_dossier);
                        // J'ajoute la photo dans mon tableau pour la BDD
                        $arrayMedia["$photo"] = $photo_bdd;
                    }else { // sinon erreur
                        array_push($this->erreur, "Il manque un paramètre important sur la photo N°$i, veuillez choisir une autre photo");
                    }
                }else {// si elle est vide erreur
                    array_push($this->erreur, "La photo N°$i doit être définie obligatoirement, veuillez choisir une photo");
                }
            }else if (!empty($_FILES["photo$i"]['name'])) {
                // Je créer un préfixe pour mon image
                $photo_name_coloc = str_replace(' ', '_', $name_coloc);
                // Je créer le nom de la photo
                $nom_photo = $photo_name_coloc . '-' . $_FILES["photo$i"]['name'];
                // Je créer l'URL a insérer dans la BDD
                $photo_bdd = $this->URL . "photos/$nom_photo";
                // Je créer le chemin d'accés pour aller copier la photo dans le dossier "photos"
                $photo_dossier = __DIR__ . "/../../public/photos/$nom_photo";
                // Si elle à un nom temporaire
                if (!empty($_FILES["photo$i"]['tmp_name'])) {
                    // Je copie la photo dans le dossier
                    copy($_FILES["photo$i"]['tmp_name'], $photo_dossier);
                    // J'ajoute la photo dans mon tableau pour la BDD
                    $arrayMedia["$photo"] = $photo_bdd;
                }else {// sinon erreur
                    array_push($this->erreur, "Il manque un paramètre important sur la photo N°$i, veuillez choisir une autre photo");
                }
            }
        }

        // CONDITIONS
        $conditions = strip_tags(trim($request->get('conditions')));
        if (!empty($conditions)) {
            if ($conditions != 'on') {
                array_push($this->erreur, "Vous devez accepter les conditions pour pouvoir poster votre annonce");
            }else {
                $conditions = true;
            }
        }

        // echo "<pre>";
        // var_dump($arrayMedia);
        // echo "</pre>";

        // SI IL Y A DES ERREURS
        if (!empty($this->erreur)) {
            return $app['twig']->render('connected/ajout-annonce.html.twig', array(
                "error" => $this->erreur,
                "value" => $app["formulaire"]["verifParamAnnonce"]["value_form"]
            ));
        }else {
            $arrayAnnonce = array(
                "name_coloc" => $name_coloc,
                "rent" => $rent,
                "description" => $description,
                "postal_code" => $postal_code,
                "adress" => $adress,
                "housing_type" => $housing_type,
                "date_dispo" => $date_dispo,
                "nb_roommates" => $nb_roommates,
                "conditions" => $conditions,
                "mail_annonce" => $mail_annonce,
                "tel_annonce" => $tel_annonce,
                "adress_details" => $adress_details,
                "surface" => $surface,
                "nb_room" => $nb_room,
                "handicap_access" => $handicap_access,
                "smoking" => $smoking,
                "animals" => $animals,
                "sex_roommates" => $sex_roommates,
                "furniture" => $furniture,
                "garden" => $garden,
                "balcony" => $balcony,
                "parking" => $parking,
                "district" => $district,
                "equipment" => $equipment,
                "member_profil" => $memberProfil,
                "hobbies" => $hobbies,
            );

            // echo "<pre>";
            // var_dump($arrayAnnonce);
            // echo "</pre>";
            // die();

            $annonce = new AnnonceModelDAO($app['db']);
            $annonce->createAnnonce($arrayAnnonce, $arrayMedia, $app);
            // $createAnnonce = $annonce->createAnnonce(string $name_coloc, float $rent, string $description, int $postal_code, string $adress, string $city, string $date_dispo, int $nb_roommates, bool $conditions, string $mail_annonce, int $tel_annonce, string $adress_details, float $surface, int $nb_room, string $handicap_access, string $smoking, string $sex_roommates, string $furniture, string $garden, string $balcony, string $parking, string $video, string $district, string $equipment, string $memberProfil, string $hobbies);
        }
    }
}
