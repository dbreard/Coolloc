<?php

namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Coolloc\Model\TokensDAO;

class ChangePassController extends Controller
{

    //fonction d'analyse des champs saisie
    public function changePassForgottenAction(Application $app, Request $request)
    {
        $token = strip_tags(trim($request->get("token")));
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
            $password = password_hash($password, PASSWORD_DEFAULT) . md5('bruh');
            $resultat = new UserModelDAO($app['db']);
            $idUser = $resultat->selectUserFromToken($token);


            if(!$idUser) // SI AUCUN UTILISATEUR NE CORRESPOND AU TOKEN
                return $app['twig']->render('formulaires/change-password.html.twig', array(
                    "error" => 'Erreur lors du changement de mot de passe veuillez réessayer',
                ));
            else
            {

                $updateUser = new UserModelDAO($app['db']); // MODIFICATION DU MDP UTILISATEUR
                $rowAffected = $updateUser->modifyPasswordFromToken($password, $idUser['user_id']);


                if($rowAffected == 1) { // SI LE MDP DE L'UTILISATEUR A BIEN ETE MODIFIER
                    $deleteToken = new TokensDAO($app['db']);
                    $rowAffectedDeleteToken = $deleteToken->deleteToken($token);
                }else{
                    return $app['twig']->render('formulaires/change-password.html.twig', array(
                        "error" => 'Erreur lors du changement de mot de passe veuillez réessayer',
                    ));
                }


                if($rowAffectedDeleteToken == 1) { // SI LE TOKEN A BIEN ETE SUPPRIMER
                    return $app->redirect('/Coolloc/public/login');
                }
            }
        }
    }

}
