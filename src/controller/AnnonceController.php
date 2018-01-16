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
        if ($app["formulaire"]["verifParamAnnonce"]["error"])
            return  $app['twig']->render('/connected/ajout-annonce.html.twig', $app["formulaire"]["verifParamAnnonce"]["value_form"]);

        // CHAMPS OBLIGATOIRES --- SUPPRESSIONS DES BALISES PHP ET DES ESPACES FORCER
        $name_coloc = strip_tags(trim($request->get('name_coloc')));
        $rent = strip_tags(trim($request->get('rent')));
        $description = strip_tags(trim($request->get('description')));
        $postal_code = strip_tags(trim($request->get('postal_code')));
        $adress = strip_tags(trim($request->get('adress')));
        $city = strip_tags(trim($request->get('city')));
        $date_dispo = strip_tags(trim($request->get('date_dispo')));
        $nb_roommates = strip_tags(trim($request->get('nb_roommates')));
        $conditions = strip_tags(trim($request->get('conditions')));

        // CHAMPS OBLIGATOIRES && SAISI FACULTATIVES --- SUPPRESSIONS DES BALISES PHP ET DES ESPACES FORCER
        if (!empty($mail_annonce)) {
            $mail_annonce = strip_tags(trim($request->get('mail_annonce')));

            ($this->verifEmail($mail_annonce)) ? : array_push($this->erreur, 'Email invalide');
        }else {
            // charger l'email du profil de l'utilisateur
            // $annonceMail = new AnnonceModel($app['db']);
            // $annonceMail->searchMail($_SESSION['id']);
        }

        // if (!empty($tel_annonce)) {
        //      $tel_annonce = strip_tags(trim($request->get('tel_annonce')));
        //
        //
        //     ($this->verifTel($tel_annonce)) ? $tel_annonce = $this->modifyTel($tel_annonce) : array_push($this->erreur, 'Numéro de téléphone invalide');
        // }else {
        //         charger le numéro de téléphone du profil de l'utilisateur
        //         $annonceTel = new AnnonceModel($app['db']);
        //         $annonceTel->searchTel($_SESSION['id']);
        // }

        // CHAMPS FACULTATIFS --- SUPPRESSIONS DES BALISES PHP ET DES ESPACES FORCER

        // (!empty($adress_details)) ? $adress_details = strip_tags(trim($request->get('adress_details'))) : ;
        //
        // (!empty($surface) && is_numeric($surface) && $surface > 0) ? $surface = strip_tags(trim($request->get('surface'))) : ;
        //
        // (!empty($nb_room) && is_integer($nb_room) && $nb_room > 0) ? $nb_room = strip_tags(trim($request->get('nb_room'))) : ;

        $handicap_access = strip_tags(trim($request->get('handicap_access')));
        $smoking = strip_tags(trim($request->get('smoking')));
        $animals = strip_tags(trim($request->get('animals')));
        $sex_roommates = strip_tags(trim($request->get('sex_roommates')));
        $furniture = strip_tags(trim($request->get('furniture')));
        $garden = strip_tags(trim($request->get('garden')));
        $balcony = strip_tags(trim($request->get('balcony')));
        $parking = strip_tags(trim($request->get('parking')));

        // SECURISATION DES VALEURS DES TABLEAUX
        foreach ($request->get('district') as $key => $value) {
            $value = strip_tags(trim($value));
        }
        foreach ($request->get('equipments') as $key => $value) {
            $value = strip_tags(trim($value));
        }
        foreach ($request->get('member_profil') as $key => $value) {
            $value = strip_tags(trim($value));
        }
        foreach ($request->get('hobbies') as $key => $value) {
            $value = strip_tags(trim($value));
        }

        (iconv_strlen($name_coloc) > 2 || iconv_strlen($name_coloc) <= 40) ? : array_push($this->erreur, 'Nom de coloc invalide');

        ($rent > 0 && is_numeric($rent)) ? : array_push($this->erreur, 'Loyer saisi incorrect');

        (iconv_strlen($description) <= 600) ? : array_push($this->erreur, 'Longueur de la description incorrect');

        (iconv_strlen($postal_code) == 5 && is_integer($postal_code) && preg_match('#^[0-9]{1}[1-7]{1}[0-9]{3}$#',$postal_code)) ? : array_push($this->erreur, 'Longueur de la description incorrect');

        ($date_dispo >= $this->getDate()) ? : array_push($this->erreur, 'Longueur de la description incorrect');
        $date_dispo = strip_tags(trim($request->get('date_dispo')));

        // var_dump($_FILES);

        if (!empty($this->erreur)) {
            return $app['twig']->render('connected/ajout-annonce.html.twig', array(
                "error" => $this->erreur,
            ));
        }
    }
}
