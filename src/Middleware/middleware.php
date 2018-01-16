<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


function verifParam($request , $verifRequest = array()) :array
{
    $error = false ;
    $messageError = "";

foreach($verifRequest as $indice => $valeur)
{
    // ici on parcourt notre tableau de requete aray(email,username,password)
    if(!$request->has($valeur) || trim($request->get($valeur)) == "")
    // verification pour savoir si la valeur existe et verification si c'est vide
    {
        $error = true ;
        $messageError .= " $valeur, ";
    }
}
// retourne l'affichage des erreurs et du msg d'erreur
return array("error" => $error, "message" => $messageError) ;
}

// recéption des données et analyse pour savoir si elle sont existentes et remplis
$verifParamRegister = function (Request $request, Application $app)
                      {
                        $retour = verifParam($request->request, array("firstname","lastname","birthdate","password","repeat_password","mail","tel","activity","sex","status","conditions"));
                        if($retour["error"])
                        return $app->redirect("/Coolloc/public/inscription");
                      };
$verifParamLogin = function (Request $request, Application $app)
                      {
                        $retour = verifParam($request->request, array("mail","password"));
                        if($retour["error"])
                        return $app->redirect("/Coolloc/public/login");
                      };
$verifParamForgotPass = function (Request $request)
                      {
                        $retour = verifParam($request->request, array("mail"));
                        if($retour["error"])
                        return $app->redirect("/Coolloc/public/forgotten-password");
                      };
$verifParamChangePass = function (Request $request)
                      {
                        $retour = verifParam($request->request, array("password", "password_repeat"));
                        if($retour["error"])
                        return $app->redirect("/Coolloc/public/change-password");
};