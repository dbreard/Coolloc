<?php
namespace Coolloc\Model;

use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Doctrine\DBAL\Connection;
use Silex\Application;

class UpdateProfilModelDAO{

  private $db;

  function __construct(Connection $connect) {
      $this->db = $connect;
  }

  protected function getDB() {
      return $this->db;
  }

  public function updateProfilUser($arrayUpdateProfil, Application $app){

    //recupération de l'id utilisateur pour la session en cours
    $user = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);
    $idUser = $user['id_user'];

    // fonction permettant de controler combien de champs remplis du formulaire sont identiques a ceux deja présents dans la base de données.
    $sql = "SELECT * FROM user WHERE id_user = ?";// on selectionne les infos enregistrées de l'utilisateur
    $resultat = $this->getDb()->fetchAssoc($sql, array((string) $idUser));
    $count = 0; // on créé une variable compteur definie a 0 par defaut
    foreach ($resultat as $key => $value){ // on parcour les valeur des champs saisis par l'utilisateur
      if(isset($arrayUpdateProfil[$key]) && $arrayUpdateProfil[$key] == $value) // on compare ses valeurs de champs saisis à ceux de la BDD
        $count++; // si la valeur d'un champs saisi est identique a la valeur du champs deja present dans la BDD, on incrémente le compteur de 1.
    }


    if (isset($arrayUpdateProfil['profil_picture'])){// Si le tableau envoyé par le ModifProfilController contient l'index profil_picture
      $sql = "UPDATE user SET firstname = ?, lastname = ?, birthdate = ?, mail = ?, tel = ?, activity = ?, sex = ?, profil_picture = ?, status = ? WHERE id_user = ?";
      $rowAffected = $this->getDB()->executeUpdate( $sql, array( // on insère tous les champs de ce tableau en bdd
        (string) $arrayUpdateProfil['firstname'],
        (string) $arrayUpdateProfil['lastname'],
        (string) $arrayUpdateProfil['birthdate'],
        (string) $arrayUpdateProfil['mail'],
        (string) $arrayUpdateProfil['tel'],
        (string) $arrayUpdateProfil['activity'],
        (string) $arrayUpdateProfil['sex'],
        (string) $arrayUpdateProfil['profil_picture'],
        (string) $arrayUpdateProfil['status'],
        (string) $idUser,));

        if ($count == 9)//dans les cas ou tous les champs restent inchangés (et donc que aucune insertion ne s'est faite dans la bdd) on fait appel a notre compteur pour s'assurer que tous les champs du tableau envoyés, etaient identiques a ceux present dans la bdd
          $rowAffected = 1; // si le nombre de nos champs saisie (9 dans ce cas est egal a la valeur du compteur alors on passe rowAffected a 1)

    }
    else{ // Si le tableau envoyé par ModifProfilController ne contient pas le champs profil_picture
      $sql = "UPDATE user SET firstname = ?, lastname = ?, birthdate = ?, mail = ?, tel = ?, activity = ?, sex = ?, status = ? WHERE id_user = ?";
      $rowAffected = $this->getDB()->executeUpdate( $sql, array(//on insère tous les champs du tableau dans la bdd
        (string) $arrayUpdateProfil['firstname'],
        (string) $arrayUpdateProfil['lastname'],
        (string) $arrayUpdateProfil['birthdate'],
        (string) $arrayUpdateProfil['mail'],
        (string) $arrayUpdateProfil['tel'],
        (string) $arrayUpdateProfil['activity'],
        (string) $arrayUpdateProfil['sex'],
        (string) $arrayUpdateProfil['status'],
        (string) $idUser,));

        if($count == 8){//dans les cas ou tous les champs restent inchangés (et donc que aucune insertion ne s'est faite dans la bdd) on fait appel a notre compteur pour s'assurer que tous les champs du tableau envoyés, etaient identiques a ceux present dans la bdd
          $rowAffected = 1;// si le nombre de nos champs saisie (8 dans ce cas est egal a la valeur du compteur alors on passe rowAffected a 1)
        }
    }



    if ($rowAffected == 1){ // si l'insertion s'est bien passé (donc que rowaffected est a 1)
      return true;// on retourne true pour le controller
    }
    else{
      return false; // sinon on retourne false
    }



  }
}
