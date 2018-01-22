<?php

namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Coolloc\Model\CommentModelDAO;
use Coolloc\Model\TokensDAO;


class CommentController extends Controller
{

    //fonction d'analyse des champs saisie
    public function commentAction(Application $app, Request $request)
    {
        $comment = strip_tags(trim($request->get('comment')));
        

        // si le message contient moins de 5 caractères ou si le message contient plus de 101 caractères 
        if ((iconv_strlen($comment) < 5 && iconv_strlen($comment) > 100)){
            $this->erreur['comment'] = 'Votre commentaire doit contenir entre 5 et 100 caractères';
          }

          if(!empty($this->erreur)){
            return $app['twig']->render('connected/temoigner.html.twig', array(
                "error" => $this->erreur,
            ));
          }

          else {
            // on fait appel à la fonction statique dans model - qui permet de récupérer les infos du profil utilisateur connecté
             $profilInfo = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);


             if(isset($profilInfo)){
                $commentaire = new CommentModelDAO($app['db']);
                $rowaffected = $commentaire->createComment($profilInfo['id_user'], $comment);

                if($rowaffected == 1){
                    return $app->redirect('/Coolloc/public/connected/merci');
                }
             }
             else
             {
                $this->erreur['comment'] = 'Ce message ne peut être envoyé';
             }

            }


        if (!empty($this->erreur))
        { // si il y a des erreurs lors de la connexion, on les affiche
            return $app['twig']->render('connected/temoigner.html.twig', array(
                "error" => $this->erreur,
            ));
        }
        else
        { // si les formats du message,

            $comment = new CommentModelDAO($app['db']);
            return $app['twig']->redirect('Coolloc/public/connected/merci');

        }


    }

    
}
