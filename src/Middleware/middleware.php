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
$verifParamRegister = function (Request $request)
                      {
                        $retour = verifParam($request->request, array("firstname","lastname","birthdate","password","repeat_password","mail","tel","activity","sex","status","conditions"));
                        if($retour["error"])
                        return  $app->redirect("/login");
                      };


// VERIFICATION PARAMETRES D'ANNONCE
// recéption des données et analyse pour savoir si elle sont existentes et remplis
$verifParamAnnonce = function (Request $request)
                    {
                      $retour = verifParam($request->request, array("name_coloc", "rent", "tel_annonce", "mail_annonce", "description", "city", "adress", "adress_details", "postal_code", "housing_type", "surface", "nb_room", "date_dispo", "nb_roommates", "handicap_access", "smoking", "animals", "sex_roommates", "furniture", "garden", "balcony", "parking", "district", "equipments", "member_profil", "hobbies", "conditions"));
                      if($retour["error"])
                      return  $app->redirect("/connected/ajout_annonce");
                    };
