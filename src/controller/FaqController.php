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

            $infosFaqs = new FaqModelDAO($app['db']); // instanciation d'un objet pour récupérer les infos d'une annonce
            $resultat = $infosFaqs->allFaqSelected();

            if($resultat != false){
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

    // SELECTION DE TOUTE LES FAQ
    public function selectFaqs(Application $app){

        $infosFaqs = new FaqModelDAO($app['db']);
        $resultat = $infosFaqs->allFaqSelected();

            return $resultat;

    }


    public function faqAction(Application $app, Request $request){

        $question = strip_tags(trim($request->get("question")));
        $reponse = strip_tags(trim($request->get("reponse")));

        $insertionFaq = new FaqModelDAO($app['db']);
        $insertionFaq->insertFaq($question, $reponse);

            return $app->redirect('/Coolloc/public/connected/sabit/gerer-faq');

    }

    public function modifyDeleteFaq(Application $app, Request $request){
        $idFaq = strip_tags(trim($request->get("id_faq"))); // ON RECUPERE L'ID DE LA FAQ DANS L'URL
        $action = strip_tags(trim($request->get("action"))); // ON RECUPERE L'ACTION A EFFECTUER

        if(!filter_var($idFaq, FILTER_VALIDATE_INT)){ // VERIFICATION DU BON FORMAT DE L'ID
            return $app->redirect('/Coolloc/public/connected/sabit/gerer-faq');// SI L'ID EST AU MAUVAIS FORMAT
        }

        $faq = new FaqModelDAO($app['db']);

        if ($action == 'modify') // si l'action est modifier une faq
        {
            $infosAnnonce = new FaqModelDAO($app['db']); // instanciation d'un objet pour récupérer les infos d'une annonce
            $resultat = $infosAnnonce->allFaqSelected(); // recupère les infos de l'annonce

            $resultatFaq = $faq->selectFaq($idFaq);

            return $app['twig']->render('dashboard/faq-dashboard.html.twig', array(
                'userAdmin' => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
                "valueFaq" => $resultatFaq,
                "faqs" => $resultat,
            ));
        }
        elseif ($action == 'delete') // si l'action est supprimer une faq
        {
            $faq->deleteFaq($idFaq);
            return $app->redirect('/Coolloc/public/connected/sabit/gerer-faq');
        }
        else
        {
            return $app->redirect('/Coolloc/public/connected/sabit/gerer-faq');
        }
    }

    public function modifyFaq(Application $app, Request $request)
    {
        $question = strip_tags(trim($request->get("question")));
        $reponse = strip_tags(trim($request->get("reponse")));
        $idFaq = strip_tags(trim($request->get("id_faq")));

        $insertionFaq = new FaqModelDAO($app['db']);
        $insertionFaq->updateFaq($idFaq, $question, $reponse);


            return $app->redirect('/Coolloc/public/connected/sabit/gerer-faq');

    }

}
