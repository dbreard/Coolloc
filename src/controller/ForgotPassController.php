<?php

namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;

class ForgotPassController extends Controller
{

    //fonction d'analyse des champs saisie
    public function forgotPassAction(Application $app, Request $request)
    {
        $email = strip_tags(trim($request->get("mail")));

        //email : filter var
        //email : doit être égal à un email register
        //email : envoie email

        ($this->verifEmail($email)) ?  : array_push($this->erreur, 'Format de l\'email incorrect ');


        if (!empty($this->erreur)) {
            return $app['twig']->render('basic/forgotten-password.html.twig', array(
                "error" => $this->erreur,
            ));
        }

        else
        {
            $resultat = new UserModelDAO($app['db']);
            $userVerifEmail = $resultat->verifEmailBdd($email);
            ($userVerifEmail) ?  : array_push($this->erreur, 'L\'email n\'est pas associé à un compte ');
            return $app['twig']->render('basic/forgotten-password.html.twig', array(
                "error" => $this->erreur,
            ));
        }
    }

}
