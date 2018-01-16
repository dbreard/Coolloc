<?php

namespace Coolloc\Model;

use Silex\Application;
use Doctrine\DBAL\Connection;

class Model {

    private $db;

    function __construct(Connection $connect){
        $this->db = $connect;
    }


}