<?php

namespace Coolloc\Model;

use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Doctrine\DBAL\Connection;
use Silex\Application;

class UpdateDetailsProfilModelDAO{

  private $db;

  function __construct(Connection $connect) {
      $this->db = $connect;
  }

  protected function getDB() {
      return $this->db;
  }

  public function getIdOptionByUser(string $idUser){
    $sql = "SELECT options_id FROM user WHERE id_user = ?";
    $idOption = $this->getDB()->fetchAssoc($sql, array((string) $idUser));
    return $idOption;
  }

  public function UpdateDetailsProfil(array $arrayDetailsProfil, string $idUser): bool{
      $idOption = $this->getIdOptionByUser($idUser);
      $sql = "UPDATE options SET handicap_access = ?, smoking = ?, animals = ?, equipment = ?, sex_roommates = ?, furniture = ?, garden = ?, balcony = ?, parking = ?, district = ?, date_dispo = ?, member_profil = ?  WHERE id_options = ? ";
      $rowAffected = $this->getDB()->executeUpdate( $sql, array(
        (string) $arrayDetailsProfil['handicap_access'],
        (string) $arrayDetailsProfil['smoking'],
        (string) $arrayDetailsProfil['animals'],
        (string) $arrayDetailsProfil['equipment'],
        (string) $arrayDetailsProfil['sex_roommates'],
        (string) $arrayDetailsProfil['furniture'],
        (string) $arrayDetailsProfil['garden'],
        (string) $arrayDetailsProfil['balcony'],
        (string) $arrayDetailsProfil['parking'],
        (string) $arrayDetailsProfil['district'],
        (string) $arrayDetailsProfil['date_dispo'],
        (string) $arrayDetailsProfil['member_profil'],
        (string) $idOption['options_id']));

      return true;


    }
  }
