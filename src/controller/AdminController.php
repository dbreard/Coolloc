<?php
namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Coolloc\Model\TokensDAO;

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
                    "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
                    "usersInfo" => $infosUser->allUsersSelected(),
                ));
            } else {// Si l'utilisateur n'est pas admin
                return $app->redirect('/Coolloc/public');
            }
        }

}

