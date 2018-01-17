<?php

namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Coolloc\Model\TokensDAO;

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
        

        if (!empty($this->erreur))
        { // si il y a des erreurs lors de la connexion, on les affiche
            return $app['twig']->render('formulaires/login.html.twig', array(
                "error" => $this->erreur,
            ));
        }
        else
        { // si les formats d'email et mdp sont bons, l'user se connecte et crée un token
            $resultat = new UserModelDAO($app['db']);
            $userVerifEmail = $resultat->verifEmailBdd($email);
                ($userVerifEmail) ? $user = $resultat->verifEmailBdd($email) : array_push($this->erreur, 'Email ou mot de passe incorrect');
                // Vérifie si l'email de connexion correspond en BDD
                if (!empty($user)){
                    if ($user['password'] == password_verify($password , substr($user['password'], 0, -32 ))) 
                    { // si les mot de passe cryptés correspondent
                        $tokenUser = new TokensDAO($app['db']);
                        $resultatToken = $tokenUser->createToken($user['id_user'], $this->expireToken(), $this->generateToken(), 'connexion');
                        // générer le token en fonction de id_user
                        if (!empty ($resultatToken))
                        { // si le token existe, on récupère sa valeur dans la session
                            $_SESSION['membre'] = array(
                                'zoubida' => $resultatToken, 
                                'status' => $user['status'],                         
                            );
                        }
                        else
                        {
                            array_push($this->erreur, 'Erreur lors de la connexion');
                        }                 
                    }
                    else
                    {
                        array_push($this->erreur, 'Email ou mot de passe incorrect');
                    }

                }        
        }

        if (empty($this->erreur)){
            return $app->redirect('\Coolloc\public\connected\profil');
        }
        else
        {
            return $app['twig']->render('formulaires/login.html.twig', array(
                "error" => $this->erreur,
            ));
        }
    }
}
