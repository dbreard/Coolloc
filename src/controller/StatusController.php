<?php
namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Coolloc\Model\TokensDAO;

class StatusController extends Controller{

  function changeStatusAction(Application $app, Request $request){
    $status = strip_tags(trim($request->get("statut")));
    $profilInfo = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);

      $isconnected = Controller::ifConnected();
      $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();
      $userSearchColocation = Controller::userSearchColocation($app);


    if ($status != "cherche_colocation" && $status != "cherche_colocataire"){
      //Si les champs envoyé au formulaire ne correspondent pas
      $this->erreur['incorrect_status'] = 'Votre demande a échoué';

        if ($isConnectedAndAdmin){
            return $app['twig']->render('connected/profil.html.twig', array(
                "isConnectedAndAmin" => $isConnectedAndAdmin,
                "connected" => $isconnected,
                "userSearchColocation" => $userSearchColocation,
                "error" => $this->erreur,
                "profilInfo" => $profilInfo
            ));
        }

        elseif ($isconnected) {
            return $app['twig']->render('connected/profil.html.twig', array(
                "connected" => $isconnected,
                "userSearchColocation" => $userSearchColocation,
                "error" => $this->erreur,
                "profilInfo" => $profilInfo
            ));
        }
    }
    elseif ($status == "cherche_colocataire"){
      //si le champ du forulaire correspond a la recherche de colocataires
      return $app->redirect('\Coolloc\public\connected\ajout-annonce');
    }
    elseif ($status == "cherche_colocation"){
      //si le champ correspond a la recherche de colocation
      $userSearchColocation = Controller::userSearchColocation($app);
      $id = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);
      $updateStatus = new UserModelDAO($app['db']);
      $rowAffected = $updateStatus->updateUserStatus($id['id_user'], "cherche colocation");

      if ($rowAffected == 1){
        //si le modele retourne
        $profilInfo = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);

          if ($isConnectedAndAdmin){
              return $app['twig']->render('connected/profil.html.twig', array(
                  "isConnectedAndAmin" => $isConnectedAndAdmin,
                  "connected" => $isconnected,
                  'profilInfo' => $profilInfo,
                  "success" => "",
                  "userSearchColocation" => $userSearchColocation,
              ));
          }

          elseif ($isconnected) {
              return $app['twig']->render('connected/profil.html.twig', array(
                  "connected" => $isconnected,
                  'profilInfo' => $profilInfo,
                  "success" => "",
                  "userSearchColocation" => $userSearchColocation,
              ));
          }
      }
      else{
        //
        $this->erreur['failed_update'] = 'Le changement du statut a échoué';

          if ($isConnectedAndAdmin){
              return $app['twig']->render('connected/profil.html.twig', array(
                  "isConnectedAndAmin" => $isConnectedAndAdmin,
                  "connected" => $isconnected,
                  "error" => $this->erreur,
                  "profilInfo" => $profilInfo,
                  "userSearchColocation" => $userSearchColocation,
              ));
          }

          elseif ($isconnected) {
              return $app['twig']->render('connected/profil.html.twig', array(
                  "connected" => $isconnected,
                  "error" => $this->erreur,
                  "profilInfo" => $profilInfo,
                  "userSearchColocation" => $userSearchColocation,
              ));
          }
      }
    }
    else{
      $this->erreur['wrong_status'] = 'OooOops, nous avons rencontré une erreur. Veuillez rééssayer SVP';
     
        if ($isConnectedAndAdmin){
            return $app['twig']->render('connected/profil.html.twig', array(
                "isConnectedAndAmin" => $isConnectedAndAdmin,
                "connected" => $isconnected,
                "error" => $this->erreur,
                "profilInfo" => $profilInfo,
                "userSearchColocation" => $userSearchColocation,
            ));
        }

        elseif ($isconnected) {
            return $app['twig']->render('connected/profil.html.twig', array(
                "connected" => $isconnected,
                "error" => $this->erreur,
                "profilInfo" => $profilInfo,
                "userSearchColocation" => $userSearchColocation,
            ));
        }
    }
  }

}
