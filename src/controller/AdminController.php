<?php
namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Coolloc\Model\AnnonceModelDAO;
use Coolloc\Model\CommentModelDAO;

class AdminController extends Controller {

    private $isconnectedAndAdmin; // stockage de la vérification si l'utilisateur est admin et connecter

        function __construct(){
            $this->isconnectedAndAdmin = Controller::ifConnectedAndAdmin();
        }

        // SELECTION DES INFOS DE L'ADMIN EN COUR
        public function selectedAdminInfos(Application $app){

            // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
            if ($this->isconnectedAndAdmin) { // Si l'utilisateur est admin

            $infosUser = new UserModelDAO($app['db']); // instanciation d'un objet pour récupérer les infos d'un user
            $infosAnnonce = new AnnonceModelDAO($app['db']); // instanciation d'un objet pour récupérer les infos d'une annonce
            $commentaires = new CommentModelDAO($app['db']);

            $resultatUserColocation = $infosUser->UsersColocationSelected();
            $resultatUserColocataires = $infosUser->UsersColocataireSelected();
            $resultatAnnonce = $infosAnnonce->allAnnoncesSelected();
            $resultatCommentaire = $commentaires->selectComment();


            if (!empty($resultatUserColocation) && !empty($resultatUserColocataires) && !empty($resultatAnnonce) && !empty($resultatCommentaire)){
                return $app['twig']->render('dashboard/index-dashboard.html.twig', array(
                    "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
                    "usersColocations" => $resultatUserColocation,
                    "usersColocataires" => $resultatUserColocataires,
                    "annonces" => $resultatAnnonce,
                    "commentaires" => $resultatCommentaire,
                ));
            }else{
                return $app->redirect('/Coolloc/public');
            }
            }else {
                return $app->redirect('/Coolloc/public');
            }
        }


        // SELECTION DE TOUTES LES INFOS DES UTILISATEUR CHERCHANT DES COLOCATION ET DES INFO DE L'ADMIN EN COUR
        public function selectedUsersColocationsAndAdminInfos(Application $app){

            // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN

            if ($this->isconnectedAndAdmin) { // Si l'utilisateur est admin
                $infosUser = new UserModelDAO($app['db']); // instanciation d'un objet pour récupérer les infos d'un user
                $resultat = $infosUser->UsersColocationSelected();

                if(!empty($resultat)){ // si la requette retourne un resultat
                    return $app['twig']->render('dashboard/user-dashboard-colocations.html.twig', array(
                        "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
                        "usersInfo" => $resultat,
                    ));
                }else{
                    return $app->redirect('/Coolloc/public/connected/sabit');
                }

            } else {// Si l'utilisateur n'est pas admin

                return $app->redirect('/Coolloc/public');

            }
        }

        // SELECTION DE TOUTES LES INFOS DES UTILISATEUR CHERCHANT DES COLOCATAIRES ET DES INFO DE L'ADMIN EN COUR
        public function selectedUsersColocatairesAndAdminInfos(Application $app){

            // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
            if ($this->isconnectedAndAdmin) { // Si l'utilisateur est admin

                $infosUser = new UserModelDAO($app['db']); // instanciation d'un objet pour récupérer les infos d'un user
                $resultat = $infosUser->UsersColocataireSelected();

                if(!empty($resultat)){
                    return $app['twig']->render('dashboard/user-dashboard-colocataires.html.twig', array(
                        "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app), // selection des info de l'admin pour affichage dans le dashboard
                        "usersInfo" => $resultat, // selection des infos utilisateur cherchant des colocataires
                    ));
                }else{
                    return $app->redirect('/Coolloc/public/connected/sabit');
                }

            } else {// Si l'utilisateur n'est pas admin
                return $app->redirect('/Coolloc/public');
            }
        }



        // SELECTION DE TOUTES LES ANNONCES
        public function selectedAnnoncesAndAdminInfos(Application $app){

            // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
            if ($this->isconnectedAndAdmin) { // Si l'utilisateur est admin

                $infosAnnonce = new AnnonceModelDAO($app['db']); // instanciation d'un objet pour récupérer les infos d'une annonce
                $resultat = $infosAnnonce->allAnnoncesSelected();

                if(!empty($resultat)){
                    return $app['twig']->render('dashboard/annonces-dashboard.html.twig', array(
                        "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
                        "annoncesInfo" => $resultat,
                    ));
                }else{
                    return $app->redirect('/Coolloc/public/connected/sabit');
                }

            } else {// Si l'utilisateur n'est pas admin
                return $app->redirect('/Coolloc/public');
            }
        }



        // SELECTIONNE TOUTE LES INFOS D'UN USER PAR SON ID
        public function detailsUser(Application $app, Request $request){

            // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
            if ($this->isconnectedAndAdmin) { // Si l'utilisateur est admin

                $idUser = strip_tags(trim($request->get("id_user"))); // ON RECUPERE L'ID UTILISATEUR DANS L'URL

                if(!filter_var($idUser, FILTER_VALIDATE_INT)){ // VERIFICATION DU BON FORMAT DE L'ID
                    return $app->redirect('/Coolloc/public/connected/sabit/gerer-annonces');// SI L'ID EST AU MAUVAIS FORMAT
                }

                $detailsUser = new UserModelDAO($app['db']); // instanciation d'un objet pour recupérer les infos utilisateur
                $resultat = $detailsUser->selectUserFromId($idUser);

                if(!empty($resultat)){ // si la requette retourne un resultat
                    return $app['twig']->render('dashboard/details-profil-dashboard.html.twig', array(
                        "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
                        "detailsUser" => $resultat,
                    ));
                }else{ // si la requette n'a pas fonctionner
                    return $app->redirect('/Coolloc/public/connected/sabit');
                }

            }else {// Si l'utilisateur n'est pas admin
                return $app->redirect('/Coolloc/public');
            }
        }


        // SELECTIONNE TOUTE LES INFOS D'UNE ANNONCE PAR SON ID
        public function detailsAnnonces(Application $app, Request $request){

            // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
            if ($this->isconnectedAndAdmin) { // Si l'utilisateur est admin

                $idAnnonce = strip_tags(trim($request->get("id_annonce"))); // ON RECUPERE L'ID DE L'ANNONCE DANS L'URL

                if(!filter_var($idAnnonce, FILTER_VALIDATE_INT)){ // VERIFICATION DU BON FORMAT DE L'ID
                    return $app->redirect('/Coolloc/public/connected/sabit/gerer-annonces');// SI L'ID EST AU MAUVAIS FORMAT
                }

                $detailsAnnonce = new AnnonceModelDAO($app['db']); // instanciation d'un objet pour recupérer les infos de l'annonce
                $resultat = $detailsAnnonce->selectAnnonceById($id_annonce);

                if (!empty($resultat)){ // si la requette retourne un resultat
                    return $app['twig']->render('dashboard/details-annonce-dashboard.html.twig', array(
                        "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
                        "detailsAnnonces" => $resultat['annonce'],
                        "photoAnnonces" => $resultat['photo'],
                        "videoAnnonces" => $resultat['video'],
                    ));
                }else{ // si la requette n'a pas fonctionner
                    return $app->redirect('/Coolloc/public/connected/sabit');
                }

            }else {// Si l'utilisateur n'est pas admin
                return $app->redirect('/Coolloc/public');
            }
        }



        // MODIFIE LE STATUS D'UN USER
        public function modifyUserStatus(Application $app, Request $request){

            // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
            if ($this->isconnectedAndAdmin) { // Si l'utilisateur est admin
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

