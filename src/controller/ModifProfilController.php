<?php

namespace Coolloc\Controller;


use Silex\Application;
use Coolloc\controller\Controller;
use Coolloc\Model\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ModifProfilController{

  public function sendUserProfilInfo(){

    $profilInfo = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);

    return $app['twig']->render('connected/profil-modif.html.twig', array("profilInfo" => $profilInfo));

  }

}
