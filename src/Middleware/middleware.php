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

                        return  $app->redirect("/Coolloc/public/inscription");
                      };

//----------------- MiddleWare du formulaire de CONTACT-----------------------//
$verifContact = function (Request $request, Application $app)
                      {
                        $retour = verifParam($request->request, array("email","subject","message"));
                        if($retour["error"])
                          return  $app->redirect("/contact");
                      };
