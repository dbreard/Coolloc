<?php

namespace Coolloc\Controller;


class Controller {

    protected $erreur;

    // VERIFICATION DU FORMAT EMAIL
    public function verifEmail(string $email) :bool
    {
        $resultat = (filter_var($email, FILTER_VALIDATE_EMAIL)) ? true : false;
        return $resultat;
    }

    // VERIFICATION DU FORMAT MDP
    public function verifMdp(string $password)
    {
       $resultat = (iconv_strlen($password) >= 6 && iconv_strlen($password) <= 20) ? true : false ;
           return $resultat;
    }

    //VERIFICATION DE LA CORRESPONDANCE DES MOT DE PASSE
    public function verifCorrespondanceMdp(string $password,string $password_repeat) : bool
    {
        $resultat = ($password == $password_repeat) ? true : false;
        return $resultat;
    }
}