<?php

namespace Coolloc\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
//use Webforce\Model\TokensDAO;



class LoginController
{

    //fonction d'analise des champs saisie
    public function loginAction(Application $app, Request $request)
    {
        //email : filter var et doit être égal à un email register
        //mdp : doit être égal au mdp register lié à l'email register

        
    }

}
