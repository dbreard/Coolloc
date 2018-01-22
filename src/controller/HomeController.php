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

class HomeController extends Controller{

    // SELECTION DES INFOS MEMBRE ET ANNONCE POUR LA HOME
    public function homeAction(Application $app){

        $membres = new UserModelDAO($app['db']);
        $annonce = new AnnonceModelDAO($app['db']);
        $temoignages = new CommentModelDAO($app['db']);

        $membresAnnonce = array();

        $membresAnnonce['membres'] = $membres->OrderUsersColocationSelected(); // stockage des resultat de selection des membres dans un index membres
        $membresAnnonce['annonces'] = $annonce->OrderAllAnnoncesSelected(); // stockage des resultat de selection des annonce dans un index annonces
        $membresAnnonce['stats_users'] = $membres->countAllUsers(); // stockage du nombre d'utilisateur en bdd
        $membresAnnonce['stats_annonces'] = $annonce->countAllAnnonces(); // stockage du nombres d'annonce en bdd
        $membresAnnonce['stats_city'] = $annonce->countAllCityFromAnnonce(); // stockage des resultat de selection des annonce dans un index annonces
        $membresAnnonce['temoignages'] = $temoignages->selectComment(); // Stockage des resultat de selection des temoignages dans un index temoignages

        return $membresAnnonce;

    }

}
