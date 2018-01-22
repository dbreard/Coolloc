<?php

namespace Coolloc\Model;

use Silex\Application;
use Doctrine\DBAL\Connection;
use Coolloc\controller\Controller;

class UserModelDAO {

    private $db;

    function __construct(Connection $connect){
        $this->db = $connect;
    }

    //******* GETTER *********//
    protected function getDb() {
        return $this->db;
    }

    // COMPARAISON DE L'EMAIL AVEC LA BDD
    function verifEmailBdd(string $email)
    {
            $sql = "SELECT * FROM user WHERE mail = ?";
            $resultat = $this->getDb()->fetchAssoc($sql, array((string) $email));
            return $resultat;
    }


    // CHANGEMENT DE MOT DE PASSE
    function changeMdpBdd(string $password)
    {
            $sql = "UPDATE user SET password = ?";
            $resultat = $this->getDb()->fetchAssoc($sql, array((string) $password));
            return $resultat;
    }



    public function insertUSer(string $first_name,string $last_name,string $birthdate,string $password,string $email,string $tel,string $sexe,string $activite,int     $condition){

        $this->getDb()->insert('options', array());


        $this->getDb()->insert('user', array(
            'options_id' => $this->db->lastInsertId(),
            'firstname' => $first_name,
            'lastname' => $last_name,
            'birthdate' => $birthdate,
            'password' => $password,
            'mail' => $email,
            'tel' => $tel,
            'activity' => $activite,
            'sex' => $sexe,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'conditions' => $condition,
        ));

        return $this->db->lastInsertId();
    }

    // SELECTION D'UN USER PAR SON TOKEN
    public function selectUserFromToken(string $token){

        $sql = "SELECT user_id FROM tokens WHERE token = ? AND type LIKE 'email'";
        $idUser = $this->getDb()->fetchAssoc($sql, array((string) $token));

        return $idUser;

    }

    // SELECTION D'UN TOKEN CONNEXION PAR SON USER_ID
    public function selectTokenConnectionFromUser(int $userId) {
        
        $sql = "SELECT token FROM tokens WHERE user_id = ? AND type LIKE 'connexion'";
        $token = $this->getDb()->fetchAssoc($sql, array((int) $userId));

        return $token;

    }

    // SELECTION D'UN USER PAR SON ID
    public function selectUserFromId(int $idUser){

        $sql = "SELECT * FROM user_options WHERE id_user = ?";
        $user = $this->getDb()->fetchAssoc( $sql, array((int) $idUser) );


        $response = array();

        $response['district'] = Controller::stringToArray($user['district']);
        $response['equipment'] = Controller::stringToArray($user['equipment']);
        $response['hobbies'] = Controller::stringToArray($user['hobbies']);
        $response['member_profil'] = Controller::stringToArray($user['member_profil']);
        $response['user'] = $user;

        return $response;
    }

    // CHANGE LE STATUT DE L'ADMIN EN ACTIF APRES RECEPTION DU TOKEN PAR MAIL
    public function updateUserFromToken(array $idUser) :int{

        $sql = "UPDATE user SET account = 'actif' WHERE id_user = ? ";
        $rowAffected = $this->getDb()->executeUpdate( $sql, array((int) $idUser["user_id"]) );

        return $rowAffected;

    }

    // MODIFICATION DU STATUS UTILISATEUR
    public function updateUserStatus(string $idUser, string $status): int{
      $sql = "UPDATE user SET status = ? WHERE id_user = ? ";
      $rowAffected = $this->getDb()->executeUpdate( $sql, array((string) $status, (int) $idUser));

      return $rowAffected;
     
    }

    // CHANGEMENT DE MOT DE PASSE
    public function modifyPasswordFromToken( string $password, int $idUser ) {
        
        $sql = "UPDATE user SET password = :password WHERE id_user = :id ";
        $rowAffected = $this->getDb()->executeUpdate( $sql, array('password' => $password, 'id' => $idUser ));

        return $rowAffected;


    }

    //SELECTION DE TOUT LES USERS EN BDD
    public function allUsersSelected(){

        $sql = "SELECT * FROM user_options";
        $users = $this->getDb()->fetchAll($sql, array());

        return $users;
    }

    //SELECTION DES UTILISATEURS CHERCHANT UN COLOCATION
    public function UsersColocationSelected(){

        $sql = "SELECT * FROM user_options WHERE status = 'cherche colocation'";
        $users = $this->getDb()->fetchAll($sql, array());

        return $users;

    }

    //SELECTION DES UTILISATEURS RECENT CHERCHANT UNE COLOCATION
    public function OrderUsersColocationSelected(){

        $sql = "SELECT *  FROM user_options WHERE status = 'cherche colocation' ORDER BY date_created DESC LIMIT 0,5";
        $users = $this->getDb()->fetchAll($sql, array());

        return $users;

    }

    //SELECTION DES UTILISATEURS CHERCHANT DES COLOCATAIRES
    public function UsersColocataireSelected(){

        $sql = "SELECT uo.* , aoc.id_user_post_annonce FROM user_options uo , annonce_options_city aoc WHERE aoc.user_id = uo.id_user AND status = 'cherche colocataire'";
        $users = $this->getDb()->fetchAll($sql, array());


        return $users;
    }


    public function modifyUserStatus( $idUser ){

        $sql = "UPDATE user SET account = 'inactif' WHERE id_user = ? ";
        $rowAffected = $this->getDb()->executeUpdate( $sql, array((int) $idUser) );

        return $rowAffected;


    }

}
