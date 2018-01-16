<?php

namespace Coolloc\Model;

use Doctrine\DBAL\Connection;

class AnnonceModelDAO {

    private $db;

    function __construct(Connection $connect) {
        $this->db = $connect;
    }

    protected function getDB() {
        return $this->db;
    }

    public function createAnnonce(array $arrayAnnonce): ?string{

        $token = substr( md5( uniqid().mt_rand() ), 0, 22 );

        $this->getDb()->insert('tokens', array(
            'token' => $token,
            'type' => $type,
            'dateEnd' => $dateExpire,
            'user_id' => $id,
        ));

        return $token;

    }

}

?>
