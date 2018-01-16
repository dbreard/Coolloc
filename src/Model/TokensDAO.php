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

    //******* GETTER *********//

    public function getDb(){
        return $this->db;
    }

}
