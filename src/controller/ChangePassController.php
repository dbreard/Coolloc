<?php

namespace Coolloc\Controller;

use Coolloc\Controller\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
//use Webforce\Model\TokensDAO;



class ChangePassController extends Controller
{

    //fonction d'analise des champs saisie
    public function changePassAction(Application $app, Request $request)
    {
        //mdp : entre 6 et 20 caractère et mdp 1 = mdp 2

        // et password = repeat_password

        function ChangePass() {
            UPDATE user
            SET password = '$_POST[password]'
            WHERE 
            
        }
    }

}
