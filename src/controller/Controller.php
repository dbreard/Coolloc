<?php

namespace Coolloc\Controller;


class Controller {

    protected $erreur;


    public function verifEmail(string $email) :bool
    {
        $resultat = (filter_var($email, FILTER_VALIDATE_EMAIL)) ? true : false;
        return $resultat;

    }

    public function verifMdp(string $password) :bool
    {
       $resultat =  (iconv_strlen($password) < 6 || iconv_strlen($password) > 20) ? true : false;
       return $resultat;
    }

}