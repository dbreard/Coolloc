<?php

namespace Coolloc\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
//use Webforce\Model\TokensDAO;



class ContactController extends Controller
{
  public function sendMail(array $user, array $message): bool{
    try {

      global $app;
      $mail = $app['mail'];
      //Server settings
      $mail->SMTPDebug = 1;                                 // Enable verbose debug output
      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = 'coollocstaff@gmail.com';           // SMTP username
      $mail->Password = 'mike est noir';                    // SMTP password
      $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 465;                                    // TCP port to connect to

      //Recipients
      $mail->setFrom('coollocstaff@gmail.com', 'Coollocstaff');
      $mail->addAddress('regent.mickael.mai@gmail.com', 'michou');

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

    //fonction d'analise des champs saisie
    public function contactAction(Application $app, Request $request)
    {
      $email = strip_tags(trim($request->get("email")));
      $firstname = strip_tags(trim($request->get("firstname")));
      $lastname = strip_tags(trim($request->get("lastname")));
      $username = $firstname." ".$lastname;
      $subject = strip_tags(trim($request->get("subject")));
      $message = strip_tags(trim($request->get("message")));

      echo $email;
      echo $username;
      echo $subject;
      if (!$this->verifEmail($email)){
        return $app['twig']->render('formulaires/contact.html.twig');
      }

      if (iconv_strlen($subject) < 3 || iconv_strlen($subject) > 50)
        return $app['twig']->render('formulaires/contact.html.twig');

      if (iconv_strlen($message) < 20)
        return $app['twig']->render('formulaires/contact.html.twig');

      $this->sendMail(array("address" => "coollocstaff@gmail.com", "name" => $username), array("body" => $message, "subject" => $subject));
      }
  }
