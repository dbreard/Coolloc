<?php
namespace Coolloc\Model;

use Doctrine\DBAL\Connection;

class TokensDAO
{

    private $db;

    function __construct(Connection $connect){
        $this->db = $connect;
    }

    public function createToken(int $id_user,string $expired,string $token, string $type = 'email'): ? string{
        $this->getDb()->insert('tokens', array(
            'token' => $token,
            'user_id' => $id_user,
            'date_expire' => $expired,
            'type' => $type,
    ));
        return $token;
    }

    public function deleteToken(string $token){
        $resultat = $this->getDb()->delete("tokens", array("token" => $token));
        return $resultat;
    }

    //verification de la date de validité du token
    public function verifValidateToken(string $token){
        $sql = "SELECT * FROM tokens WHERE token = ? AND date_expire >= NOW()";
        $resultat = $this->getDb()->fetchAssoc( $sql, array((string) $token) );

        if(!empty($resultat)){
            return true;
        }else{
            return false;
        }

    }

    public function selectTokenFromIdUser(int $idUser){
        $sql = "SELECT * FROM tokens WHERE user_id = ?";
        $resultat = $this->getDb()->fetchAssoc( $sql, array((int) $idUser) );

        return $resultat;
    }

    //verification de l'éxistance du token
    public function verifExistTokenConnection(string $idUser) :bool{
        $sql = "SELECT * FROM tokens WHERE user_id = ? AND type = 'connexion'";
        $resultat = $this->getDb()->fetchAssoc( $sql, array((int) $idUser) );

        if(!empty($resultat)){
            return true;
        }else{
            return false;
        }

    }

    //verification de l'éxistance du token
    public function verifExistTokenEmail(string $idUser) :bool{
        $sql = "SELECT * FROM tokens WHERE user_id = ? AND type = 'email'";
        $resultat = $this->getDb()->fetchAssoc( $sql, array((int) $idUser) );

        if(!empty($resultat)){
            return true;
        }else{
            return false;
        }

    }


    //******* GETTER *********//

    public function getDb(){
        return $this->db;
    }

}
