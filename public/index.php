<?php

    session_start();
    // ini_set('display_errors', 0); // Message d'erreur n'apparait pas

    require_once __DIR__.'/../vendor/autoload.php';

    use Silex\Application;


    $app = new Application();

    //----------------------------------------------------------------------------------------------------------------------
    $app['debug'] = true; // a supprimer en mode prod
    //----------------------------------------------------------------------------------------------------------------------

    require __DIR__.'/../src/register.php';

    require __DIR__.'/../src/Middleware/middleware.php';

    require __DIR__.'/../src/controller/Controller.php';
    require __DIR__.'/../src/controller/RegisterController.php';

    require __DIR__.'/../src/controller/SearchController.php';
    require __DIR__.'/../src/controller/LoginController.php';
    require __DIR__.'/../src/controller/ChangePassController.php';
    require __DIR__.'/../src/controller/ForgotPassController.php';
    require __DIR__.'/../src/controller/ContactController.php';
    require __DIR__.'/../src/controller/AnnonceController.php';
    require __DIR__.'/../src/controller/AdminController.php';
    require __DIR__.'/../src/controller/StatusController.php';
    require __DIR__.'/../src/controller/CommentController.php';
    require __DIR__.'/../src/controller/DetailsProfilController.php';
    require __DIR__.'/../src/controller/UpdateAnnonceController.php';


    require __DIR__.'/../src/Model/Model.php';
    require __DIR__.'/../src/Model/UserModelDAO.php';
    require __DIR__.'/../src/Model/AnnonceModelDAO.php';
    require __DIR__.'/../src/Model/TokensDAO.php';
    require __DIR__.'/../src/Model/SearchAnnonceModelDAO.php';
    require __DIR__.'/../src/Model/CommentModelDAO.php';
    require __DIR__.'/../src/Model/UpdateDetailsProfilModelDAO.php';
    require __DIR__.'/../src/Model/UpdateAnnonceModelDAO.php';


    require __DIR__.'/../src/route.php';



    $app->run();
