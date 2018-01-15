<?php

namespace Coolloc\Controller;


use Silex\Application;
use Coolloc\controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;




class registerController extends Controller
{

    //fonction d'analyse des champs saisie

    public function registerAction(Application $app, Request $request)
    {
        $password = strip_tags(trim($request->get("password")));
        $password_repeat = strip_tags(trim($request->get("password_repeat")));
        $email = strip_tags(trim($request->get("mail")));
        $birthdate = strip_tags(trim($request->get("birthdate")));
        $birthdateFormatage = str_replace("-", "", $birthdate);
        $date = date("Ymd"); // date du jour
        $first_name = strip_tags(trim($request->get("firstname")));
        $last_name = strip_tags(trim($request->get("lastname")));
        $tel = strip_tags(trim($request->get("tel")));

        //prénom : supérieur ou = a 2 inférieur a 50
        (iconv_strlen($first_name) >= 2 && iconv_strlen($first_name) <= 50) ? : $this->erreur .= 'Votre prénom doit être compris entre 2 et 50 caractères';


        //nom : supérieur ou = a 2 inférieur a 50
        (iconv_strlen($last_name) >= 2 && iconv_strlen($last_name) <= 50) ? : $this->erreur .= 'Votre nom doit être compris entre 2 et 50 caractères';


        //date de naissance : avoir au moin 18ans
        (($date - $birthdateFormatage) > 180000) ? : $this->erreur .= 'Vous êtes trop jeune';


        //mdp : entre 6 et 20 caractère et mdp 1 = mdp 2
        (!$this->verifCorrespondanceMdp($password, $password_repeat)) ?  $this->erreur .= 'Les mot de passe ne correspondent pas' :
            ($this->verifMdp($password)) ? $password = password_hash($password, PASSWORD_DEFAULT) . md5("bruh") : $this->erreur .= 'Format mot de passe incorrect';


        //Vérification d'email :
        ($this->verifEmail($email)) ?  : $this->erreur .= 'Email invalide';


        //tel : type number et 10 caractère et rajouter +33 et si 9 chiffre ne rien suppr si 10 suppr le 1er
        ($this->verifTel($tel)) ? $tel = $this->modifyTel($tel) : $this->erreur .= 'Format de téléphone incorect';


        //sexe : une des 2 value (femme / homme)
        //activité : au moin 1 activité de choisie
        //status : value soit cherche colocataire soit cherche colocation
        //condition : doit être égal a 1

        // SI IL Y A DES ERREURS
        if (!empty($this->erreur)) {
            return $app['twig']->render('formulaires/register.html.twig', array(
                "error" => $this->erreur,
            ));
        }

    }



}
