<?php
namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Coolloc\Model\UpdateDetailsProfilModelDAO;

class DetailsProfilController extends Controller{

  public function detailsProfilAction(Application $app, Request $request){

    $handicap_access = strip_tags(trim($request->get('handicap_access')));
    $smoking = strip_tags(trim($request->get('smoking')));
    $animals = strip_tags(trim($request->get('animals')));
    $sex_roommates = strip_tags(trim($request->get('sex_roommates')));
    $furniture = strip_tags(trim($request->get('furniture')));
    $garden = strip_tags(trim($request->get('garden')));
    $balcony = strip_tags(trim($request->get('balcony')));
    $parking = strip_tags(trim($request->get('parking')));
    $date_dispo = strip_tags(trim($request->get('date_dispo')));

    if ($date_dispo == "")
      $date_dispo = date('Y-m-d');
    // var_dump($date_dispo);


    // ARRAY DES CHAMPS SELECT A MULTIPLES CHOIX

    $arrayDistrict = array('Proche de commerces', 'Proche d\'écoles', 'Proche de transports', 'Calme', 'Animé');
    $arrayEquipment = array('TV', 'Hifi', 'Wifi', 'Fibre optique', 'Salle de jeux', 'Machine à laver');
    $arrayMemberProfil = array('Timide', 'Bavard', 'Solitaire', 'Casanier', 'Discret', 'Convivial', 'Cool', 'Extraverti', 'Ordonné', 'Tolérant', 'Sportif', 'Fétard', 'Studieux', 'Curieux', 'Joyeux', 'Respectueux');
    $arrayHobbies = array('Ciné - TV - Série', 'Littérature', 'Musique', 'Jeux vidéo', 'Jeux plateau - Société', 'Mode', 'Shopping', 'Sport', 'Cuisine - Pâtisserie', 'Sorties culturelles', 'Voyages', 'Autres');

    // SECURITE DES VALEURS DES TABLEAUX
    // Je vérifie l'éxistance du champs 'district' dans mon POST et s'il existe je le traite
    ($request->request->has('district')) ? $district = $this->verifArrayAndFormat($request->get('district'), $arrayDistrict, 'Quartier', 'INSERT') : $district = "";

    // Je vérifie l'éxistance du champs 'equipment' dans mon POST et s'il existe je le traite
    ($request->request->has('equipment')) ? $equipment = $this->verifArrayAndFormat($request->get('equipment'), $arrayEquipment, 'Equipement', 'INSERT') : $equipment = "";

    // Je vérifie l'éxistance du champs 'member_profil' dans mon POST et s'il existe je le traite
    ($request->request->has('member_profil')) ? $member_profil = $this->verifArrayAndFormat($request->get('member_profil'), $arrayMemberProfil, 'Profil colocataires', 'INSERT') : $member_profil = "";

    // Je vérifie l'éxistance du champs 'hobbies' dans mon POST et s'il existe je le traite
    ($request->request->has('hobbies')) ? $hobbies = $this->verifArrayAndFormat($request->get('hobbies'), $arrayHobbies, "Centre d'intérêts", 'INSERT') : $hobbies = "";


    $arrayDetailsProfil = array(
      "date_dispo" => $date_dispo,
      "handicap_access" => $handicap_access,
      "smoking" => $smoking,
      "animals" => $animals,
      "sex_roommates" => $sex_roommates,
      "furniture" => $furniture,
      "garden" => $garden,
      "balcony" => $balcony,
      "parking" => $parking,
      "district" => $district,
      "equipment" => $equipment,
      "member_profil" => $member_profil,
      "hobbies" => $hobbies,
    );
    // var_dump($arrayDetailsProfil['hobbies']);
    // die();

    $idUser = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);
    $userId = $idUser['id_user'];




    $updateDetailsProfil = new UpdateDetailsProfilModelDAO($app['db']);
    $rowAffected = $updateDetailsProfil->UpdateDetailsProfil($arrayDetailsProfil, $userId);

    if ($rowAffected == true){
      $optionUser = Model::userOptionOnly($userId, $app);
      $annonceUser = Model::annonceByUser($userId, $app);
      return $app['twig']->render('connected/profil.html.twig', array("modified" => "Vos préférences ont bien été modifiées", "profilInfo" => $idUser, "userOption" => $optionUser, "annonceUser" => $annonceUser));
    }
    else{
      $optionUser = Model::userOptionOnly($userId, $app);
      return $app['twig']->render('connected/profil.html.twig', array("error" => "OooOops une erreur est survenue, merci de rééssayer", "profilInfo" => $idUser, "userOption" => $optionUser));
    }
  }

  //---------------Envoi des options utilisteur sur une page----------------//

  public function sendUserOption(){

    //appel de la globale $app
    global $app;

    //recupération des option user en fonction du token de la session en cours
    $idUser = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);
    $options = Model::userOptionOnly($idUser['id_user'], $app);

    if ($options['district'] != ""){
      $district = Controller::stringToArray($options['district']);
      $options['district'] = $district;
      if ($options['district'] == "proche d'écoles")
        $options['district'] == "proche_écoles";
    }

    if ($options['equipment'] != ""){
      $equipment = Controller::stringToArray($options['equipment']);
      $options['equipment'] = $equipment;
    }

    if ($options['hobbies'] != ""){
      $hobbies = Controller::stringToArray($options['hobbies']);
      $options['hobbies'] = $hobbies;
    }
    if($options['member_profil'] != ""){
      $member_profil = Controller::stringToArray($options['member_profil']);
      $options['member_profil'] = $member_profil;
    }

    // echo'<pre>';var_dump($options);echo'</pre>';
    // die();


    $isconnected = $this->ifConnected();

    if (!$isconnected) {
        return $app->redirect('/../Coolloc/public/login');
    } else {
        return $app['twig']->render('/connected/ajout-details-profil.html.twig', array("options" => $options));
    }
  }

  // Envoie des données de l'utilisateur par son id
  public function UserInfoById(Application $app, Request $request){
      $isconnected = Controller::ifConnected();
      $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();

      $idUser = strip_tags(trim($request->get("id_user"))); // ON RECUPERE L'ID UTILISATEUR DANS L'URL

      if(!filter_var($idUser, FILTER_VALIDATE_INT)){ // VERIFICATION DU BON FORMAT DE L'ID
          return $app->redirect('/Coolloc/public/connected/sabit/gerer-annonces');// SI L'ID EST AU MAUVAIS FORMAT
      }

      $detailsUser = new UserModelDAO($app['db']); // instanciation d'un objet pour recupérer les infos utilisateur
      $resultat = $detailsUser->selectUserFromId($idUser);

      if ($resultat != false){ // si la variable resultat n'est pas vide

          if ($isConnectedAndAdmin){
              return $app['twig']->render('profil-recherche-colocation.html.twig', array(
                  "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected,
                  "detailsUser" => $resultat['user'],
                  "district" => $resultat['district'],
                  "equipment" => $resultat['equipment'],
                  "member_profil" => $resultat['member_profil'],
                  "hobbie" => $resultat['hobbies'],

              ));
          }

          elseif ($isconnected) {
              return $app['twig']->render('profil-recherche-colocation.html.twig', array(
                  "connected" => $isconnected,
                  "detailsUser" => $resultat['user'],
                  "district" => $resultat['district'],
                  "equipment" => $resultat['equipment'],
                  "member_profil" => $resultat['member_profil'],
                  "hobbie" => $resultat['hobbies'],


              ));
          }

          else {
              return $app['twig']->render('profil-recherche-colocation.html.twig', array(
                  "detailsUser" => $resultat['user'],
                  "detailsUser" => $resultat['user'],
                  "district" => $resultat['district'],
                  "equipment" => $resultat['equipment'],
                  "member_profil" => $resultat['member_profil'],
                  "hobbie" => $resultat['hobbies'],


              ));

          }
      }else{
          return $app->abort(404, "Post $idUser n'existe pas.");
      }

  }


}
