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


                        return  $app->redirect("/public/inscription");
                      };

//----------------- MiddleWare du formulaire de CONTACT-----------------------//
$verifContact = function (Request $request, Application $app)
                      {
                        $retour = verifParam($request->request, array("firstname", "lastname", "email", "subject", "message"));
                        // var_dump($request->get("firstname"));
                        // var_dump($request->get("lastname"));
                        // var_dump($request->get("email"));
                        // var_dump($request->get("subject"));
                        // var_dump($request->get("message"));
                        // var_dump($retour);
                        // die();
                        if($retour["error"])
                          return  $app->redirect("/public/contact"); 
                      };

//------------------fin middleware form contact ------------------------------//


// VERIFICATION PARAMETRES D'ANNONCE
// recéption des données et analyse pour savoir si elle sont existentes et remplis
$verifParamAnnonce = function (Request $request, Application $app)
                    {
                      $retour = verifParam($request->request, array("name_coloc", "rent", "description", "city", "adress", "postal_code", "housing_type", "date_dispo", "nb_roommates", "conditions"));

                      echo "<pre>";
                          var_dump($request->request);
                      echo "</pre>";

                      //die();

                      if($retour["error"]){
                          $value_form = $request->request->all();

                          $app["formulaire"] = array(
                              "verifParamAnnonce" => array(
                                  "error" => $retour["error"],
                                  "value_form" => $value_form,
                              )
                          );
                      }else{

                          $app["formulaire"] = array(
                              "verifParamAnnonce" => array(
                                  "error" => false
                              )
                          );
                      }
                    };

