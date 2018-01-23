<?php

namespace Coolloc\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Coolloc\Controller\Controller;
//use Webforce\Model\TokensDAO;



class ContactController extends Controller
{
    //fonction d'analise des champs saisie
    public function contactAction(Application $app, Request $request)
    {
      $email = strip_tags(trim($request->get("email")));
      $firstname = strip_tags(trim($request->get("firstname")));
      $lastname = strip_tags(trim($request->get("lastname")));
      $username = $firstname." ".$lastname;
      $subject = strip_tags(trim($request->get("subject")));
      $message = strip_tags(trim($request->get("message")));


      if ((iconv_strlen($firstname) < 3 || iconv_strlen($firstname) > 50)){
        $this->erreur['firstname'] = 'Le prénom n\'est pas valide';
      }
      if ((iconv_strlen($lastname) < 3 || iconv_strlen($lastname) > 50)){
        $this->erreur['lastname'] = 'Le nom doit être compris entre 3 et 50 caractères';
      }
      if (!$this->verifEmail($email)){
        $this->erreur['email'] = 'Votre format email n\'est pas valide';
      }
      if (iconv_strlen($subject) < 3 || iconv_strlen($subject) > 50){
        $this->erreur['subject'] = 'Le sujet doit etre compris entre 3 et 50 caractère';
      }
      if (iconv_strlen($message) < 20){
        $this->erreur['message'] = 'Votre message doit faire au minimum 20 caractères';
      }

      $isconnected = Controller::ifConnected();
      $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();
      $userSearchColocation = Controller::userSearchColocation($app);

      // SI IL Y A DES ERREURS
      if (!empty($this->erreur)) {

        if ($isConnectedAndAdmin){
          return $app['twig']->render('contact.html.twig', array(
              "error" => $this->erreur,
              "firstname" => $firstname,
              "lastname" => $lastname,
              "email" => $email,
              "subject" => $subject,
              "message" => $message,
              "isConnectedAndAmin" => $isConnectedAndAdmin,
              "connected" => $isconnected,
              "userSearchColocation" => $userSearchColocation,
          ));
        }elseif ($isconnected) {
            return $app['twig']->render('contact.html.twig', array(
                "error" => $this->erreur,
                "firstname" => $firstname,
                "lastname" => $lastname,
                "email" => $email,
                "subject" => $subject,
                "message" => $message,
                "connected" => $isconnected,
                "userSearchColocation" => $userSearchColocation,
            ));
        }else {
          return $app['twig']->render('contact.html.twig', array(
              "error" => $this->erreur,
              "firstname" => $firstname,
              "lastname" => $lastname,
              "email" => $email,
              "subject" => $subject,
              "message" => $message,
          ));
      }

    }else { // SI IL N'Y A PAS D'ERREUR
        $message = utf8_encode($message);
        if ($this->sendMailStaff($username, array("body" => "De: ".$username." -- < ".$email." ><hr>".$message, "subject" => $subject))){

          if ($isConnectedAndAdmin){
            return $app['twig']->render('contact.html.twig', array(
                "success" => "Votre message a bien été envoyé, merci pour votre participation :-)",
                "isConnectedAndAmin" => $isConnectedAndAdmin,
                "connected" => $isconnected,
                "userSearchColocation" => $userSearchColocation,
            ));
          }elseif ($isconnected) {
            return $app['twig']->render('contact.html.twig', array(
                "success" => "Votre message a bien été envoyé, merci pour votre participation :-)",
                "connected" => $isconnected,
                "userSearchColocation" => $userSearchColocation,
            ));
          }else {
            return $app['twig']->render('contact.html.twig', array(
                "success" => "Votre message a bien été envoyé, merci pour votre participation :-)",
            ));
          }
        }else{
          array_push($this->erreur, 'Erreur envoi email');

          if ($isConnectedAndAdmin){
            return $app['twig']->render('contact.html.twig', array(
                "userSearchColocation" => $userSearchColocation,
                "error" => $this->erreur,
                "isConnectedAndAmin" => $isConnectedAndAdmin,
                "connected" => $isconnected,
            ));
          }elseif ($isconnected) {
            return $app['twig']->render('contact.html.twig', array(
                "userSearchColocation" => $userSearchColocation,
                "error" => $this->erreur,
                "connected" => $isconnected,
            ));
          }else {
            return $app['twig']->render('contact.html.twig', array(
                "error" => $this->erreur,
            ));
          }
        }
      }
    }
  }
