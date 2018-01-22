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
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();


    if ($isConnectedAndAdmin){
        return $app['twig']->render('connected/profil-modif.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected, "profilInfo" => $profilInfo,
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('connected/profil-modif.html.twig', array(
            "connected" => $isconnected, "profilInfo" => $profilInfo
        ));
    }

    else {
        return $app->redirect('Coolloc/public/login');
    }

  }

  public function updateProfilAction(Application $app, Request $request){

    //récupération des données venant du formulaire
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

    if(empty($tel) || !$this->verifTel($tel)){
      $this->erreur['tel'] = 'Ce numéro de téléphone n\'est pas valide';
    }
    else{ //traitment du numero de telephone pour le mettre au format +33
      $tel = $this->modifyTel($tel);
    }

    if(empty($status))
      $this->erreur['status'] = 'Ce champs ne peut pas être vide';

    if(empty($activity))
      $this->erreur['activity'] = 'Ce champs ne peut pas être vide';



    $username= $firstname."_".$lastname;

    if (!empty($_FILES["profil_picture"]['name'])) {
        // Je créer un préfixe pour mon image
        $photo_name_profil = str_replace(' ', '_', $username);
        // Je créer le nom de la photo
        $nom_photo = $photo_name_profil . '-' . $_FILES["profil_picture"]['name'];
        // Je créer l'URL a insérer dans la BDD
        $photo_bdd = $this->URL . "photos/$nom_photo";
        // Je créer le chemin d'accés pour aller copier la photo dans le dossier "photos"
        $photo_dossier = __DIR__ . "/../../public/photos/$nom_photo";
        // Si elle à un nom
        if (!empty($_FILES["profil_picture"]['tmp_name'])) {
            // Je copie la photo dans le dossier
            copy($_FILES["profil_picture"]['tmp_name'], $photo_dossier);
        }else { // sinon erreur
            $this->erreur['photo'] = "Il manque un paramètre important sur la photo de profil, veuillez choisir une autre photo";
        }
    }

// On verifie que l'utilisateur a ajouté une photo
    if(!empty($_FILES['profil_picture']['tmp_name']) && !isset($this->erreur['photo'])){
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

      // var_dump($arrayUpdateProfil);
      // die();

      if(empty($this->erreur)){//si le traitement des données du formulaire ne renvoi aucune erreurs

        $updateProfil = new UpdateProfilModelDAO($app['db']);// On crée une nouvelle instance du model Update user profil
        $rowAffected = $updateProfil->updateProfilUser($arrayUpdateProfil, $app); // on apelle la fonction updateProfilUser du modele

        $idUser = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app); // on récupère les informations utilisateur
        $userId = $idUser['id_user']; // On stocke son id dans une variable

        if ($rowAffected == true){ // si la fonction updateProfilUser a retourné un resultat positif
          $optionUser = Model::userOptionOnly($userId, $app);// on récupère les options liées au compte utilisteur
          $annonceUser = Model::annonceByUser($userId, $app);// On récupère toutes les infos de toutes les annonces postées par l'utilisateur
          return $app['twig']->render('connected/profil.html.twig', array("updated" => "votre profil a bien été modifié", "profilInfo" => $idUser, "userOption" => $optionUser, "annonceUser" => $annonceUser));
          // on envoie le tout sur la page profil pour pouvoir afficher les informations voulues
        }
        else{ // Si la fonction userUpdateProfil a retourné un resultat négatif
          return $app['twig']->render('connected/profil-modif.html.twig', array("error" => "OooOops une erreur est survenue, merci de rééssayer", "profilInfo" => $idUser));
          // on renvoi sur la page de modification profil avec une erreur et les informations du profils pour les réafficher dans les champs du formulaire.
        }
      }
      else{// Si le traitement des données du formulaire renvoi une ou plusieures erreurs
        return $app['twig']->render('connected/profil-modif.html.twig', array("errors" => $this->erreur, "profilInfo" => $idUser));
        // on le renvoi sur la page modification du profil avec ses info profil pour pouvoir les réafficher dans les champs.
      }
    }
  }
