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

        if ((iconv_strlen($comment) < 3 || iconv_strlen($comment) > 100)){
            $this->erreur['comment'] = 'Votre commentaire doit contenir entre 3 et 100 caractères';
          }

          if(!empty($erreur)){
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

    public function selectCommentAndAdminInfo(Application $app){
        // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
        $isconnectedAndAdmin = Controller::ifConnectedAndAdmin();

        // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
        if ($isconnectedAndAdmin) { // Si l'utilisateur est admin

            $comments = new CommentModelDAO($app['db']); // instanciation d'un objet pour récupérer les infos d'une annonce
            $resultat = $comments->selectComment();

            if(isset($resultat)){
                return $app['twig']->render('dashboard/comment-dashboard.html.twig', array(
                    "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
                    "comments" => $resultat,
                ));
            }else{
                return $app->redirect('/Coolloc/public/connected/sabit');
            }

        } else {// Si l'utilisateur n'est pas admin
            return $app->redirect('/Coolloc/public');
        }
    }

    public function deleteComment(Application $app, Request $request){
        $idComment = strip_tags(trim($request->get("id_comments"))); // ON RECUPERE L'ID DU COMMENTAIRE

        if(!filter_var($idComment, FILTER_VALIDATE_INT)){ // VERIFICATION DU BON FORMAT DE L'ID
            return $app->redirect('/Coolloc/public/connected/sabit/gerer-faq');// SI L'ID EST AU MAUVAIS FORMAT
        }

        $comment = new CommentModelDAO($app['db']);

        $comment->deleteComment($idComment);

        return $app->redirect('/Coolloc/public/connected/sabit/gerer-temoignage');
    }
}
