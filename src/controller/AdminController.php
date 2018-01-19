<?php
namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Coolloc\Model\AnnonceModelDAO;

class AdminController extends Controller {

        // SELECTION DES INFOS DE L'ADMIN EN COUR
        public function selectedAdminInfos(Application $app){

            // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
            $isconnectedAndAdmin = Controller::ifConnectedAndAdmin();

            if ($isconnectedAndAdmin) { // Si l'utilisateur est admin
                return $app['twig']->render('dashboard/index-dashboard.html.twig', array(
                    "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
                ));
            }else {
            return $app->redirect('/Coolloc/public');
            }
        }


        // SELECTION DE TOUTES LES INFOS DES UTILISATEUR ET DES INFO DE L'ADMIN EN COUR
        public function selectedUsersAndAdminInfos(Application $app){

            // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
            $isconnectedAndAdmin = Controller::ifConnectedAndAdmin();

            if ($isconnectedAndAdmin) { // Si l'utilisateur est admin
                $infosUser = new UserModelDAO($app['db']); // instanciation d'un objet pour récupérer les infos d'un user

                return $app['twig']->render('dashboard/user-dashboard.html.twig', array(
                    "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
                    "usersInfo" => $infosUser->allUsersSelected(),
                ));

            } else {// Si l'utilisateur n'est pas admin

                return $app->redirect('/Coolloc/public');

            }
        }


        // SELECTION DE TOUTES LES INFOS DES UTILISATEUR CHERCHANT DES COLOCATION ET DES INFO DE L'ADMIN EN COUR
        public function selectedUsersColocationsAndAdminInfos(Application $app){

            // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
            $isconnectedAndAdmin = Controller::ifConnectedAndAdmin();

            if ($isconnectedAndAdmin) { // Si l'utilisateur est admin
                $infosUser = new UserModelDAO($app['db']); // instanciation d'un objet pour récupérer les infos d'un user

                return $app['twig']->render('dashboard/user-dashboard-colocations.html.twig', array(
                    "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
                    "usersInfo" => $infosUser->allUsersSelected(),
                ));

            } else {// Si l'utilisateur n'est pas admin

                return $app->redirect('/Coolloc/public');

            }
        }

        // SELECTION DE TOUTES LES INFOS DES UTILISATEUR CHERCHANT DES COLOCATAIRES ET DES INFO DE L'ADMIN EN COUR
        public function selectedUsersColocatairesAndAdminInfos(Application $app){

            // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
            $isconnectedAndAdmin = Controller::ifConnectedAndAdmin();

            if ($isconnectedAndAdmin) { // Si l'utilisateur est admin
                $infosUser = new UserModelDAO($app['db']); // instanciation d'un objet pour récupérer les infos d'un user
                return $app['twig']->render('dashboard/user-dashboard-colocataires.html.twig', array(
                    "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app), // selection des info de l'admin pour affichage dans le dashboard
                    "usersInfo" => $infosUser->allUsersSelected(), // selection des infos utilisateur
                ));
            } else {// Si l'utilisateur n'est pas admin
                return $app->redirect('/Coolloc/public');
            }
        }

    // SELECTION DE TOUTES LES INFOS DES UTILISATEUR CHERCHANT DES COLOCATAIRES ET DES INFO DE L'ADMIN EN COUR
    public function selectedAnnoncesAndAdminInfos(Application $app){

        // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
        $isconnectedAndAdmin = Controller::ifConnectedAndAdmin();

        if ($isconnectedAndAdmin) { // Si l'utilisateur est admin
            $infosAnnonce = new AnnonceModelDAO($app['db']); // instanciation d'un objet pour récupérer les infos d'une annonce
            return $app['twig']->render('dashboard/annonces-dashboard.html.twig', array(
                "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
                "annoncesInfo" => $infosAnnonce->allAnnoncesSelected(),
            ));
        } else {// Si l'utilisateur n'est pas admin
            return $app->redirect('/Coolloc/public');
        }
    }

    // SELECTIONNE TOUTE LES INFOS D'UN USER PAR SON ID
    public function detailsUser(Application $app, Request $request){

        // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
        $isconnectedAndAdmin = Controller::ifConnectedAndAdmin();

        if ($isconnectedAndAdmin) { // Si l'utilisateur est admin
            $idUser = strip_tags(trim($request->get("id_user"))); // ON RECUPERE L'ID UTILISATEUR DANS L'URL
            $detailsUser = new UserModelDAO($app['db']); // instanciation d'un objet pour recupérer les infos utilisateur

            return $app['twig']->render('dashboard/details-profil-dashboard.html.twig', array(
                "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
                "detailsUser" => $detailsUser->selectUserFromId($idUser),
        ));
        }else {// Si l'utilisateur n'est pas admin
            return $app->redirect('/Coolloc/public');
        }
    }

    // MODIFIE LE STATUS D'UN USER
    public function modifyUserStatus(Application $app, Request $request){

        // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
        $isconnectedAndAdmin = Controller::ifConnectedAndAdmin();

        if ($isconnectedAndAdmin) { // Si l'utilisateur est admin
            $idUser = strip_tags(trim($request->get("id_user"))); // ON RECUPERE L'ID UTILISATEUR DANS L'URL
            $pageActuel = strip_tags(trim($request->get("page_actuelle"))); // ON RECUPERE LA PAGE AFIN DE LA REAFICHER APRES TRAITEMENT
            $modifyUser = new UserModelDAO($app['db']); // instanciation d'un objet pour modifier un utilisateur

            $rowAffected = $modifyUser->modifyUserStatus( $idUser );

            if ($rowAffected == 1){ //  SI L'UTILISATEUR A BIEN ETE MODIFIER
                return $app->redirect('/Coolloc/public/connected/sabit/gerer-user-' .$pageActuel);
            }else{//  SI LA REQUETTE A RENCONTRER UNE ERREUR
                return $app['twig']->render('dashboard/user-dashboard-' .$pageActuel. '.html.twig', array(
                    "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
                    "error" => "Erreur lors de la modification du status utilisateur"
                ));
            }

        } else {// Si l'utilisateur n'est pas admin
            return $app->redirect('/Coolloc/public');
        }

    }



}

