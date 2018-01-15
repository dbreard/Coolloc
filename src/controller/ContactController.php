<?php

namespace Coolloc\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
//use Webforce\Model\TokensDAO;



class ContactController
{

    //fonction d'analise des champs saisie
    public function contactAction(Application $app, Request $request)
    {
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        return false;
        return $app['twig']->render('formulaires/contact.html.twig');
      }

      if (iconv_strlen($username) < 5 || iconv_strlen($username) > 50)
        return $app['twig']->render('formulaires/contact.html.twig');

      if (iconv_strlen($password) < 5 || iconv_strlen($password) > 20)
        return $app['twig']->render('formulaires/contact.html.twig');

      return $app['twig']->render('formulaires/contact.html.twig', array(
            "post" => $request->request));


      public function sendMail(array $user, array $message): bool{
        try {

          global $app;
          $mail = $app['mail'];
          //Server settings
          $mail->SMTPDebug = 0;                                 // Enable verbose debug output
          $mail->isSMTP();                                      // Set mailer to use SMTP
          $mail->Host = 'provolone.o2switch.net';  // Specify main and backup SMTP servers
          $mail->SMTPAuth = true;                               // Enable SMTP authentication
          $mail->Username = 'eleve@lyknowledge.fr';                 // SMTP username
          $mail->Password = 'zoubida22?';                           // SMTP password
          $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
          $mail->Port = 465;                                    // TCP port to connect to

                //Recipients
          $mail->setFrom('eleve@lyknowledge.fr', 'Webforce3');
          $mail->addAddress($user["address"], $user['name']);

          //Content
          $mail->isHTML(true);                                  // Set email format to HTML
          $mail->Subject = $message['subject'];
          $mail->Body    = $message['body'];

          $mail->send();
          return true;
      } catch (Exception $e) {
          return false;
      }
    }

    }
}
