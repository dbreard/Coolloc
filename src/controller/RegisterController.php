<?php

namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;




class RegisterController extends Controller
{

    //fonction d'analise des champs saisie

    public function registerAction(Application $app, Request $request)
    {
        $password = strip_tags(trim($request->get("password")));
        $password_repeat = strip_tags(trim($request->get("password_repeat")));
        $email = strip_tags(trim($request->get("mail")));

        //prénom : supérieur ou = a 2 inférieur a 50
        //nom : supérieur ou = a 2 inférieur a 50
        //date de naissance : avoir au moin 18ans

        //mdp : entre 6 et 20 caractère et mdp 1 = mdp 2
        ($this->verifCorrespondanceMdp($password, $password_repeat)) ?  : $this->erreur .= 'Les mots de passe ne correspondent pas';
        ($this->verifMdp($password)) ?  : $this->erreur .= 'Format mot de passe incorrect';

        //Vérification d'email :
        ($this->verifEmail($email)) ?  : $this->erreur .= 'Email invalide';

        //tel : type number et 10 caractère et rajouter +33 et si 9 chiffre ne rien suppr si 10 suppr le 1er
        //sexe : une des 2 value (femme / homme)
        //activité : au moin 1 activité de choisie
        //status : value soit cherche colocataire soit cherche colocation
        //condition : doit être égal a 1

        if (strlen($this->erreur) > 0) {
            return $app['twig']->render('formulaires/register.html.twig', array(
                "error" => $this->erreur,
            ));
        }

    }

}
