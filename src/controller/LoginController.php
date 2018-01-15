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
        //email : filter var et doit être égal à un email register
        //mdp : doit être égal au mdp register lié à l'email register

        ($this->verifEmail($email)) ?  : $this->erreur .= 'Email invalide';
        // et email = email register
        // et mdp = mdp register

        function () {
            
        }

    }

    public function verifEmailAction(Application $app, Request $request){
        
        $token = strip_tags(trim($request->get("token")));
        
        
        $sql = "SELECT user_id FROM tokens WHERE token = ? AND type LIKE 'email'";
        $idUser = $app['db']->fetchAssoc($sql, array((string) $token));
        
        if(!$idUser)
            return $app->redirect("/Mike/POO/Silex%20-%20Project/public/register");

        $sql = "UPDATE `user` SET `statuts` = 'actif' WHERE `id` = ?";
        $rowAffected = $app['db']->executeUpdate( $sql, array( (int)$idUser["user_id"]));

        if($rowAffected == 1)
            $app['db']->delete("tokens", array( "token" => $token));


        return $app->redirect("/Mike/POO/Silex%20-%20Project/public/login");

    }

}