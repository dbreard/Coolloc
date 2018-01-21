<?php

namespace Coolloc\Controller;


use Silex\Application;
use Coolloc\controller\Controller;
use Coolloc\Model\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\UpdateProfilModelDAO;


class ModifProfilController extends Controller{

  public function sendUserProfilInfo(Application $app){

    $profilInfo = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);

    return $app['twig']->render('connected/profil-modif.html.twig', array("profilInfo" => $profilInfo));

  }

  public function updateProfilAction(Application $app, Request $request){

    $firstname = strip_tags(trim($request->get('firstname')));
    $lastname = strip_tags(trim($request->get('lastname')));
    $birthdate = strip_tags(trim($request->get('birthdate')));
    $mail = strip_tags(trim($request->get('mail')));
    $sex = strip_tags(trim($request->get('sex')));
    $tel = strip_tags(trim($request->get('tel')));
    $status = strip_tags(trim($request->get('status')));
    $activity = strip_tags(trim($request->get('activity')));


    //controle des champs du formulaire
    if(empty($firstname))
      $this->erreur['firstname'] = 'Ce champs ne peut pas être vide';

    if(empty($lastname))
      $this->erreur['lastname'] = 'Ce champs ne peut pas être vide';

    if(empty($birthdate))
      $this->erreur['birthdate'] = 'Ce champs ne peut pas être vide';

    if(empty($mail) || !$this->verifEmail($mail))
      $this->erreur['mail'] = 'Cet email n\'est pas valide';

    if(empty($sex))
      $this->erreur['sex'] = 'Ce champs ne peut pas être vide';

    if(empty($tel))
      $this->erreur['tel'] = 'Ce champs ne peut pas être vide';

    if(empty($status))
      $this->erreur['status'] = 'Ce champs ne peut pas être vide';

    if(empty($activity))
      $this->erreur['activity'] = 'Ce champs ne peut pas être vide';



    $username= $firstname."_".$lastname;


    if (!empty($_FILES["profil_picture"]['tmp_name'])) {
        // Je créer un préfixe pour mon image
        $photo_name_profil = str_replace(' ', '_', $username);
        // Je créer le nom de la photo
        $nom_photo = $photo_name_profil . '-' . $_FILES["profil_picture"]['name'];
        // Je créer l'URL a insérer dans la BDD
        $photo_bdd = $this->URL . "photos/$nom_photo";
        // Je créer le chemin d'accés pour aller copier la photo dans le dossier "photos"
        $photo_dossier = __DIR__ . "/../../public/photos/$nom_photo";
        // Si elle à un nom
        if (!empty($_FILES["profil_picture"]['name'])) {
            // Je copie la photo dans le dossier
            copy($_FILES["profil_picture"]['tmp_name'], $photo_dossier);
        }else { // sinon erreur
            $this->erreur['photo'] = "Il manque un paramètre important sur la photo de profil, veuillez choisir une autre photo";
        }
    }

// On verifie que l'utilisateur a ajouté une photo
    if(!empty($_FILES['profile_picture']['name'])){
      $arrayUpdateProfil = array(
        "firstname" => $firstname,
        "lastname" => $lastname,
        "birthdate" => $birthdate,
        "mail" => $mail,
        "sex" => $sex,
        "tel" => $tel,
        "status" => $status,
        "activity" => $activity,
        "profil_picture" => $photo_bdd,);
    }
    else{ // si pas de photo on n'envoie pas le champs dans le tableau
      $arrayUpdateProfil = array(
        "firstname" => $firstname,
        "lastname" => $lastname,
        "birthdate" => $birthdate,
        "mail" => $mail,
        "sex" => $sex,
        "tel" => $tel,
        "status" => $status,
        "activity" => $activity,);
    }


      //
      // var_dump($arrayUpdateProfil);
      // die();


      $updateProfil = new UpdateProfilModelDAO($app['db']);
      $rowAffected = $updateProfil->updateProfilUser($arrayUpdateProfil, $app);

      $idUser = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);
      $userId = $idUser['id_user'];

      if(empty($this->erreur)){
        if ($rowAffected == true){
          $optionUser = Model::userOptionOnly($userId, $app);
          $annonceUser = Model::annonceByUser($userId, $app);
          return $app['twig']->render('connected/profil.html.twig', array("updated" => "votre profil a bien été modifié", "profilInfo" => $idUser, "userOption" => $optionUser, "annonceUser" => $annonceUser));
        }
        else{
          return $app['twig']->render('connected/profil-modif.html.twig', array("error" => "OooOops une erreur est survenue, merci de rééssayer", "profilInfo" => $idUser));
        }
      }
      else{
        return $app['twig']->render('connected/profil-modif.html.twig', array("errors" => $this->erreur, "profilInfo" => $idUser));
      }
    }
  }
