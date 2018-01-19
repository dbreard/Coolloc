<?php
namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\FaqModelDAO;
use Coolloc\Model\Model;

class FaqController extends Controller
{

    public function selectedFaqAndAdminInfo(Application $app){
            // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
        $isconnectedAndAdmin = Controller::ifConnectedAndAdmin();

        // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
        if ($isconnectedAndAdmin) { // Si l'utilisateur est admin

            $infosAnnonce = new FaqModelDAO($app['db']); // instanciation d'un objet pour récupérer les infos d'une annonce
            $resultat = $infosAnnonce->allFaqSelected();

            if(!empty($resultat)){
                return $app['twig']->render('dashboard/faq-dashboard.html.twig', array(
                    "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
                    "faqs" => $resultat,
                ));
            }else{
                return $app->redirect('/Coolloc/public/connected/sabit');
            }

        } else {// Si l'utilisateur n'est pas admin
            return $app->redirect('/Coolloc/public');
        }
    }

    public function faqAction(Application $app, Request $request){

        $question = strip_tags(trim($request->get("question")));
        $reponse = strip_tags(trim($request->get("reponse")));

        $insertionFaq = new FaqModelDAO($app['db']);
        $rowAffected = $insertionFaq->insertFaq($question, $reponse);

        if($rowAffected == 1){
            return $app->redirect('/Coolloc/public/connected/sabit/gerer-faq');
        }else{
            return $app->render('/Coolloc/public/connected/sabit/gerer-faq');
        }
    }

}
