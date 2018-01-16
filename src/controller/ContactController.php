<?php

namespace Coolloc\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
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


      if (!$this->verifEmail($email)){
        array_push($this->erreur, 'veuillez préciser un email valide');
      }

      if (iconv_strlen($subject) < 3 || iconv_strlen($subject) > 50){
        array_push($this->erreur, 'veuillez préciser un sujet valide');
      }


      if (iconv_strlen($message) < 20){
        array_push($this->erreur, 'veuillez préciser un message valide');
      }

      if ($this->sendMailStaff($username, array("body" => "De: ".$username." -- < ".$email." ><hr>".$message, "subject" => $subject))){
        return $app['twig']->render('formulaires/contact.html.twig');
      }
      else{
        array_push($this->erreur, 'Erreur envoi email');
      }
    }
  }
