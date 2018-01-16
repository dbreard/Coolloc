<?php

namespace Coolloc\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \DateTime;



    


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

    // FONCTION DE DATE LIMITE DU TOKEN
    public function expireToken(){
        $dateNow = new DateTime();
        $dateNow->modify("+ 1 day");
        return $dateNow->format("Y-m-d H:i:s");
    }

    // FONCTION D'ENVOIE DE MAIL DE TOKEN
    public function sendMailToken(array $user,array $message = array()) {
        global $app;
        try {
            //Server settings
            $mail = $app['mail'];
            $mail->SMTPDebug = 0;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp-mail.outlook.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'coollocstaff@outlook.fr';                 // SMTP username
            $mail->Password = 'azerty1234';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('coollocstaff@outlook.fr', 'Coolloc Staff');
            $mail->addAddress($user["adress"], $user["name"]);

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $message["subject"];
            $mail->Body    = $message["body"];

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }





    //********** GETTER ***********//

    public function getDate(){
        return date("Ymd");
    }

    //-----------------------ENVOI DE MAILS AU STAFF--------------------------//

    public function sendMailStaff(string $user, array $message): bool{
      try {
        global $app;
        $mail = $app['mail'];
        //Server settings
        $mail->SMTPDebug = 0;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'davidbreard@gmail.com';           // SMTP username
        $mail->Password = 'azerty123';                    // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('coollocstaff@outlook.fr', $user);
        $mail->addAddress('coollocstaff@outlook.fr', 'CoollocStaff');

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $message['subject'];
        $mail->Body    = $message['body'];


        $mail->send();
        return true;
      }
      catch (Exception $e) {
        return false;
      }
    }
    // --------------------- fin envoi mail ----------------------- //


}
