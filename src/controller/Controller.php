<?php

namespace Coolloc\Controller;


class Controller {

    protected $erreur = array();


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

    //-----------------------ENVOI DE MAILS AU STAFF--------------------------//

    public function sendMailStaff(string $user, array $message): bool{
      try {
        global $app;
        $mail = $app['mail'];
        //Server settings
        $mail->SMTPDebug = 1;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp-mail.outlook.com';                       // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'coollocstaff@outlook.fr';           // SMTP username
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
