<?php

namespace Coolloc\Model;

use Doctrine\DBAL\Connection;

class AnnonceModelDAO extends Model {

    private $db;

    function __construct(Connection $connect) {
        $this->db = $connect;
    }

    protected function getDB() {
        return $this->db;
    }

    public function createAnnonce(array $arrayAnnonce, array $arrayMedia): ?bool{

        $this->getDb()->insert('annonce', array(
            foreach ($arrayAnnonce as $key => $value) {
                $key => $value,
            }
        ));

        return true;

    }

}

?>
