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
// Middleware pour inscription
$verifParamRegister = function (Request $request, Application $app)
                      {

                        $retour = verifParam($request->request, array("firstname","lastname","birthdate","password","password_repeat","mail","tel","activity","sex","status","conditions"));
                        if($retour["error"])
                        return $app->redirect("/Coolloc/public/inscription");
                      };

// Middleware pour connexion
$verifParamLogin = function (Request $request, Application $app)
                      {
                        $retour = verifParam($request->request, array("mail","password"));
                        if($retour["error"])
                        return $app->redirect("/Coolloc/public/login");
                      };

                      
$verifParamForgotPass = function (Request $request, Application $app)
                      {
                        $retour = verifParam($request->request, array("mail"));
                        if($retour["error"])
                        return $app->redirect("/Coolloc/public/forgotten-password");
                      };



$verifParamChangePass = function (Request $request, Application $app)

                      {
                        $retour = verifParam($request->request, array("password", "password_repeat"));
                        if($retour["error"])
                        return $app->redirect("/Coolloc/public/change-password");
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

                          return  $app->redirect("/Coolloc/public/contact");

                      };



// VERIFICATION PARAMETRES D'ANNONCE
// recéption des données et analyse pour savoir si elle sont existentes et remplis
$verifParamAnnonce = function (Request $request, Application $app)
                    {
                      $retour = verifParam($request->request, array("name_coloc", "rent", "description", "adress", "postal_code", "housing_type", "date_dispo", "nb_roommates", "conditions"));

                      // echo "<pre>";
                      //     var_dump($request->request);
                      // echo "</pre>";
                      // echo $retour['error'];
                      //die();
                      $value_form = $request->request->all();

                      if($retour["error"]){

                          $app["formulaire"] = array(
                              "verifParamAnnonce" => array(
                                  "error" => $retour["error"],
                                  "value_form" => $value_form,
                              )
                          );
                      }else{
                          $app["formulaire"] = array(
                              "verifParamAnnonce" => array(
                                  "error" => false,
                                  "value_form" => $value_form,
                              )
                          );
                      }
                    };


//-----------------Verification validation premiere connexion-----------------//
$verifStatus = function (Request $request, Application $app)
                      {
                        $retour = verifParam($request->request, array("statut"));

                        if($retour["error"])

                          return  $app->redirect("/Coolloc/public/profil");

                      };


// Middleware pour commentaires utilisateurs / témoignages du site home page

$verifParamComment = function (Request $request, Application $app)
                      {
                        // on vérifie que le contenu du message et que le user existe et différent de vide
                        $retour = verifParam($request->request, array("comment","user_id"));
                        if($retour["error"])
                        return $app->redirect("/Coolloc/public/connected/temoigner");
                      };

//Middleware pour la F.A.Q

$verifParamComment = function (Request $request, Application $app)
{
                    // on vérifie que le contenu du message et de la réponse et qu'ils ne soient pas vide
                    $retour = verifParam($request->request, array("question","reponse"));
                    if($retour["error"])
                        return $app->redirect("/Coolloc/public/connected/temoigner");
};

