<?php
namespace Coolloc\Controller;


use Silex\Application;
use Coolloc\controller\Controller;
use Coolloc\Model\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ProfilAccessController extends Controller{

  public function profilAccessAction(Application $app){

    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();

    if ($isConnectedAndAdmin){
        $profilInfo = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);
        $optionUser = Model::userOptionOnly($profilInfo['id_user'], $app);
        $annonceUser = Model::annonceByUser($profilInfo['id_user'], $app);
        return $app['twig']->render('connected/profil.html.twig', array("profilInfo" => $profilInfo, "userOption" => $optionUser, "annonceUser" => $annonceUser,  "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected, ));
    }

    elseif ($isconnected) {
        $profilInfo = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);
        $optionUser = Model::userOptionOnly($profilInfo['id_user'], $app);
        $annonceUser = Model::annonceByUser($profilInfo['id_user'], $app);
        return $app['twig']->render('connected/profil.html.twig', array("profilInfo" => $profilInfo, "userOption" => $optionUser, "annonceUser" => $annonceUser, "connected" => $isconnected, ));

    }
    else {
        return $app->redirect('/Coolloc/public');

    }

  }
}
