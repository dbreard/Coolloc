<?php

namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;

class ChangePassController extends Controller
{

    //fonction d'analyse des champs saisie
    public function changePassAction(Application $app, Request $request)
    {
        $password = strip_tags(trim($request->get("password")));
        $password_repeat = strip_tags(trim($request->get("password_repeat")));

        //mdp : doit faire entre 6 et 20 caractères
        //mdp : mdp 1 = mdp 2
        //mdp : UPDATE en BDD

        ($this->verifCorrespondanceMdp($password, $password_repeat)) ?  : array_push($this->erreur, 'Les mots de passe ne correspondent pas ');
        ($this->verifMdp($password)) ?  : array_push($this->erreur, 'Format du mot de passe incorrect  ');


        if (!empty($this->erreur)) {
            return $app['twig']->render('basic/change-password.html.twig', array(
                "error" => $this->erreur,
            ));
        }
        else
        {
            $resultat = new UserModelDAO($app['db']);
            $userChangeMdp = $resultat->changeMdpBdd($password);
            ($userChangeMdp) ?  : array_push($this->erreur, 'mdp pas changé ');
            return $app['twig']->render('basic/change-password.html.twig', array(
                "error" => $this->erreur,
            ));
        }
    }

}
