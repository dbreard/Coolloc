<?php

namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;

class LoginController extends Controller
{

    //fonction d'analyse des champs saisie
    public function loginAction(Application $app, Request $request)
    {
        $password = strip_tags(trim($request->get("password")));
        $email = strip_tags(trim($request->get("mail")));

        //email : filter var
        //email : doit être égal à un email register
        //mdp : doit faire entre 6 et 20 caractères
        //mdp : doit être égal au mdp register lié à l'email register

        ($this->verifEmail($email)) ?  : array_push($this->erreur,'Format de l\'email incorrect ');
        ($this->verifMdp($password)) ?  : array_push($this->erreur, 'Format du mot de passe incorrect ');
        

        if (!empty($this->erreur)) {
            return $app['twig']->render('formulaires/login.html.twig', array(
                "error" => $this->erreur,
            ));
        }
        else
        {
            $resultat = new UserModelDAO($app['db']);
            $userVerifEmail = $resultat->verifEmailBdd($email);
            ($userVerifEmail) ? $user = $resultat->verifUserBdd($email) : array_push($this->erreur, 'Email ou mot de passe incorrect');
            ($user['password'] == $request->get(md5(password_hash($password, PASSWORD_DEFAULT)))) ? $_SESSION['membre'] = $user : array_push($this->erreur, 'Mdp ne correspond pas');
            var_dump($_SESSION);
            return $app['twig']->render('formulaires/login.html.twig', array(
                "error" => $this->erreur,
            ));
           
        }
    }
}