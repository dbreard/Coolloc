<?php

namespace Coolloc\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;




class Controller {

    protected $erreur = array();

    // VERIFICATION DU FORMAT EMAIL
    public function verifEmail(string $email) :bool
    {
        $resultat = (filter_var($email, FILTER_VALIDATE_EMAIL)) ? true : false;
        return $resultat;

    }

    // VERIFIACTION DU FORMAT MDP
    public function verifMdp(string $password)
    {
       $resultat = (iconv_strlen($password) >= 6 && iconv_strlen($password) <= 20) ? true : false ;
           return $resultat;
    }

    // MODIFICATION DU FORMAT NUMERO DE TELEPHONE
    public function modifyTel(string $tel){

        if (iconv_strlen($tel) == 10){
            $tel = "+33" . (substr($tel, 1));
        }
        else if (iconv_strlen($tel) == 12){
            $tel = "+33" . (substr($tel, 3));
        }
        else if (iconv_strlen($tel) == 9){
            $tel = "+33" . (substr($tel, 0));
        }

        return $tel;
    }
    //VERIFICATION DU FORMAT DE NUMERO DE TELEPHONE
    public function verifTel(string $tel) :bool
    {
        $tel = str_replace(" ", "", $tel);
        $tel = str_replace("-", "", $tel);
        $tel = str_replace("/", "", $tel);

        if (iconv_strlen($tel) == 10){
            $resultat = (substr($tel, -10, 1) == 0) ? true : false;
        }
        if (iconv_strlen($tel) == 12){
            $resultat = (substr($tel, -12, 3) == "+33") ? true : false;
        }
        if (iconv_strlen($tel) == 9){
            $resultat = (substr($tel, -9, 1) == 0) ? true : false;
        }
        return $resultat;
    }

    //VERIFICATION DE LA CORRESPONDANCE DES MOT DE PASSE
    public function verifCorrespondanceMdp(string $password,string $password_repeat) : bool
    {
        $resultat = ($password == $password_repeat) ? true : false;
        return $resultat;
    }

    //CREATION DE TOKEN
    public function generateToken() {
        return substr( md5( uniqid().mt_rand() ), 0, 22 );
    }


    //********** GETTER ***********//

    public function getDate(){
        return date("d-m-Y ");
    }

}