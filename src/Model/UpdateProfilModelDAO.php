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

    $sql = "SELECT * FROM user WHERE id_user = ?";
    $resultat = $this->getDb()->fetchAssoc($sql, array((string) $idUser));



    $count = 0;
    foreach ($resultat as $key => $value){
      if(isset($arrayUpdateProfil[$key]) && $arrayUpdateProfil[$key] == $value)
        $count++;
    }


    if (isset($arrayUpdateProfil['profil_picture'])){
      $sql = "UPDATE user SET firstname = ?, lastname = ?, birthdate = ?, mail = ?, tel = ?, activity = ?, sex = ?, profil_picture = ?, status = ? WHERE id_user = ?";
      $rowAffected = $this->getDB()->executeUpdate( $sql, array(
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

        if ($count == 9)
          $rowAffected = 1;

    }
    else{
      $sql = "UPDATE user SET firstname = ?, lastname = ?, birthdate = ?, mail = ?, tel = ?, activity = ?, sex = ?, status = ? WHERE id_user = ?";
      $rowAffected = $this->getDB()->executeUpdate( $sql, array(
        (string) $arrayUpdateProfil['firstname'],
        (string) $arrayUpdateProfil['lastname'],
        (string) $arrayUpdateProfil['birthdate'],
        (string) $arrayUpdateProfil['mail'],
        (string) $arrayUpdateProfil['tel'],
        (string) $arrayUpdateProfil['activity'],
        (string) $arrayUpdateProfil['sex'],
        (string) $arrayUpdateProfil['status'],
        (string) $idUser,));

        if($count == 8){
          $rowAffected = 1;
        }
    }



    if ($rowAffected == 1){
      return true;
    }
    else{
      return false;
    }



  }
}
