<?php

namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Coolloc\Model\TokensDAO;

class CommentController extends Controller
{

    //fonction d'analyse des champs saisie
    public function commentAction(Application $app, Request $request)
    {
        $comment = strip_tags(trim($request->get('comment')));

        if ((iconv_strlen($comment) < 3 || iconv_strlen($comment) > 100)){
            $this->erreur['comment'] = 'Votre commentaire doit contenir entre 3 et 100 caractères';
          }


        if (!empty($this->erreur))
        { // si il y a des erreurs lors de la connexion, on les affiche
            return $app['twig']->render('connected/temoigner.html.twig', array(
                "error" => $this->erreur,
            ));
        }
        else
        { // si les formats du message, 
        //    traitement de l'insertion - commentModelDAO
        }

        if (empty($this->erreur)){
            return $app->redirect('\Coolloc\public\connected\merci');
        }
        else
        {
            return $app['twig']->render('connected/temoigner.html.twig', array(
                "error" => $this->erreur,
            ));
        }
    }
}
