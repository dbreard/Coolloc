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

    }

}
