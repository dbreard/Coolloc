<?php

namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AnnonceController extends Controller
{
    public function annonceAction(Application $app, Request $request) {

        if ($app["formulaire"]["verifParamAnnonce"]["error"])
            return  $app['twig']->render('/connected/ajout-annonce.html.twig', $app["formulaire"]["verifParamAnnonce"]["value_form"]);

        // SUPPRESSIONS DES BALISES PHP ET DES ESPACES FORCER
        $name_coloc = strip_tags(trim($request->get('name_coloc')));
        $rent = strip_tags(trim($request->get('rent')));
        $tel_annonce = strip_tags(trim($request->get('tel_annonce')));
        $mail_annonce = strip_tags(trim($request->get('mail_annonce')));
        $description = strip_tags(trim($request->get('description')));
        $city = strip_tags(trim($request->get('city')));
        $adress = strip_tags(trim($request->get('adress')));
        $postal_code = strip_tags(trim($request->get('postal_code')));
        $adress_details = strip_tags(trim($request->get('adress_details')));
        $surface = strip_tags(trim($request->get('surface')));
        $nb_room = strip_tags(trim($request->get('nb_room')));
        $date_dispo = strip_tags(trim($request->get('date_dispo')));
        $nb_roommates = strip_tags(trim($request->get('nb_roommates')));
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

        if (!empty($mail_annonce)) {
            ($this->verifEmail($mail_annonce)) ? : $this->erreur .= 'Email invalide';
        }else {
            // charger l'email du profil de l'utilisateur
            // $annonceMail = new AnnonceModel($app['db']);
            // $annonceMail->searchMail($_SESSION['id']);
        }

        // if (!empty($tel_annonce)) {
        //     ($this->verifTel($tel_annonce)) ? $tel_annonce = $this->modifyTel($tel_annonce) : $this->erreur .= 'Numéro de téléphone invalide';
        // }else {
        //         charger le numéro de téléphone du profil de l'utilisateur
        //         $annonceTel = new AnnonceModel($app['db']);
        //         $annonceTel->searchTel($_SESSION['id']);
        // }

        var_dump($_FILES);

        if (!empty($this->erreur)) {
            return $app['twig']->render('connected/ajout-annonce.html.twig', array(
                "error" => $this->erreur,
            ));
        }
    }
}
