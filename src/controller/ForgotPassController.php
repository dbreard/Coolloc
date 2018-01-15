<?php

namespace Coolloc\Controller;

use Coolloc\Controller\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
//use Webforce\Model\TokensDAO;



class ForgotPassController extends Controller
{

    //fonction d'analise des champs saisie
    public function forgotPassAction(Application $app, Request $request)
    {
        //email : filter var et doit être égal à un email register

        ($this->verifEmail($email)) ?  : $this->erreur .= 'Email invalide';
        // et email = email register

        if (!empty($this->erreur)) {
            return $app['twig']->render('formulaires/forgotten-password.html.twig', array(
                "error" => $this->erreur,
            ));
        }
    }

}
