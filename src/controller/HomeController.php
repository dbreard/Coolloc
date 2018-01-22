<?php
namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Coolloc\Model\AnnonceModelDAO;

class HomeController extends Controller{

    // SELECTION DES INFOS MEMBRE ET ANNONCE POUR LA HOME
    public function homeAction(Application $app){

        $membres = new UserModelDAO($app['db']);
        $annonce = new AnnonceModelDAO($app['db']);

        $membresAnnonce = array();

        $membresAnnonce['membres'] = $membres->OrderUsersColocationSelected(); // stockage des resultat de selection des membres dans un index membres
        $membresAnnonce['annonces'] = $annonce->OrderAllAnnoncesSelected(); // stockage des resultat de selection des annonce dans un index annonces

        return $membresAnnonce;

    }

}
