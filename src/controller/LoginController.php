<?php

namespace Coolloc\Controller;

use Coolloc\Controller\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
//use Webforce\Model\TokensDAO;



class LoginController extends Controller
{

    //fonction d'analise des champs saisie
    public function loginAction(Application $app, Request $request)
    {
        $password = strip_tags(trim($request->get("password")));
        $email = strip_tags(trim($request->get("email")));

        //email : filter var et doit être égal à un email register
        //mdp : doit être égal au mdp register lié à l'email register

        ($this->verifEmail($email)) ?  : $this->erreur .= 'Email invalide';
        ($this->verifMdp($password)) ?  : $this->erreur .= 'Format mot de passe incorrect';
        

        // et email = email register
        // et mdp = mdp register

        if (!empty($this->erreur)) {
            return $app['twig']->render('formulaires/login.html.twig', array(
                "error" => $this->erreur,
            ));
        }
        else {
            $user = new UserModel($app['db']);
            $userVerifEmail = $user->verifEmailBdd($email);
        }
    }


}