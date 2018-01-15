<?php

namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class ChangePassController extends Controller
{

    //fonction d'analise des champs saisie
    public function changePassAction(Application $app, Request $request)
    {
        //mdp : entre 6 et 20 caractère et mdp 1 = mdp 2
        (!$this->verifCorrespondanceMdp($password, $password_repeat)) ? $this->erreur .= 'Les mot de passe ne correspondent pas' :
            ($this->verifMdp($password)) ?  : $this->erreur .= 'Format mot de passe incorrect';

        if (!empty($this->erreur)) {
            return $app['twig']->render('formulaires/change-password.html.twig', array(
                "error" => $this->erreur,
            ));
        }
    }

    
    public function verifCorrespondanceMdp(string $password,string $password_repeat)
    {
        ($password === $password_repeat) ? false : $this->verifMdp($password);
    }

    public function changePass() {
         
     }

}
