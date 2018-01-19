<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Coolloc\Controller\Controller;
use Coolloc\Controller\AdminController;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;


//Request::setTrustedProxies(array('127.0.0.1'));

//*****************************//
//*** ROUTE SANS CONNECTION ***//
//*****************************//

//*** ROUTES GET/POST ***//


//ROUTE HOME
$app->get('/', function () use ($app) {
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();


    if ($isConnectedAndAdmin){
        return $app['twig']->render('index.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected, 
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('index.html.twig', array(
     "connected" => $isconnected, 
    ));
    
    } 

    
    else {
        return $app['twig']->render('index.html.twig', array()) ;
    }

    // si internaute non connecté rdv vers index-nc.html.twig autrement rdv vers index-c.html.twig
    // return $app['twig']->render('index.html.twig', array());

})
->bind('accueil');
$app->post('/', "Coolloc\Controller\SearchController::searchAction");


//RESULTAT RECHERCHE
$app->get('/resultat-recherche', function () use ($app) {

    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();


    if ($isConnectedAndAdmin){
        return $app['twig']->render('serp-annonce.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected, 
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('serp-annonce.html.twig', array(
     "connected" => $isconnected,
    ));
    } else {
        return $app['twig']->render('serp-annonce.html.twig', array());
    }

})
    ->bind('resultat-recherche');




//DETAILS ANNONCE NON CONNECTER
$app->get('/details-annonce/{id_annonce}', "Coolloc\Controller\AnnonceController::detailAnnonceAction")->bind('details-annonce');
$app->post('/details-annonce', function () use ($app) {
    //controleur
});



//LOGIN
$app->get('/login', function () use ($app) {
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();


    if ($isConnectedAndAdmin){
        return $app['twig']->render('formulaires/login.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected, 
        ));
    }

    elseif (!$isconnected) {
        return $app['twig']->render('formulaires/login.html.twig', array(
     "connected" => $isconnected,
    ));
    } else {
        return $app->redirect('/Coolloc/public/connected/profil') ;
    }


    })
    ->bind('login');
    $app->post('/login', "Coolloc\Controller\LoginController::loginAction")->before($verifParamLogin);

// LOGOUT
$app->get('/connected/deconnexion', function () use ($app) {
    
        $isdisconnected = Controller::sessionDestroy();
    
            return $app['twig']->render('index.html.twig', array(
         "disconnected" => $isdisconnected,
        ));
    })
        ->bind('deconnexion');
    $app->post('/connected/deconnexion', 'Coolloc\Controller\Controller::sessionDestroy');


//INSCRIPTION
$app->get('/inscription', function () use ($app) {
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();


    if ($isConnectedAndAdmin){
        return $app->redirect('/') ;
 
    }
    elseif ($isconnected) {
        return $app->redirect('/') ;

    } else {
        return $app['twig']->render('formulaires/register.html.twig', array());

    }

})
    ->bind('inscription');

$app->post('/inscription', "Coolloc\Controller\RegisterController::registerAction")->before($verifParamRegister);



//VERIFICATION DU TOKEN D'INSCRIPTION
$app->get('/verif/{token}/', 'Coolloc\Controller\RegisterController::verifEmailAction');


// confirmation mail inscription
$app->get('/confirmation', function () use ($app) {
    return $app['twig']->render('confirmation.html.twig', array());
})
    ->bind('confirmation');

//confirmation mail mdp oublié
$app->get('/confirmation-oublie', function () use ($app) {
    return $app['twig']->render('confirmation-oublie.html.twig', array());
})
    ->bind('confirmation-oublie');

//MDP OUBLIER
$app->get('/forgotten-password', function () use ($app) {
    return $app['twig']->render('basic/forgotten-password.html.twig', array());
})
    ->bind('forgotten-password');
$app->post('/forgotten-password', "Coolloc\Controller\ForgotPassController::forgotPassAction")->before($verifParamForgotPass);



//CHANGER MDP PAR LE PROFIL

$app->get('/change-password', function () use ($app) {
    return $app['twig']->render('basic/change-password.html.twig', array());
})
    ->bind('change-password');
$app->post('/change-password', "Coolloc\Controller\ChangePassController::changePassAction")->before($verifParamChangePass);


//CHANGER MDP OUBLIER PAR EMAIL
$app->get('/change-password/{token}', function ($token) use ($app) {
    return $app['twig']->render('basic/change-password.html.twig', array());
})
    ->bind('change-password');
$app->post('/change-password/{token}', "Coolloc\Controller\ChangePassController::changePassForgottenAction")->before($verifParamChangePass);



//FAQ
$app->get('/faq', function () use ($app) {
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();

    if ($isConnectedAndAdmin){
        return $app['twig']->render('faq.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected, 
        ));
    }
    elseif ($isconnected) {
        return $app['twig']->render('faq.html.twig', array(
     "connected" => $isconnected, 
    ));
    } 
    else {
        return $app['twig']->render('faq.html.twig', array());
    }
    
})
    ->bind('faq');

$app->post('/faq', function () use ($app) {
    //controleur
});


//CONTACT

$app->get('/contact', function () use ($app) {
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();


    if ($isConnectedAndAdmin){
        return $app['twig']->render('contact.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected, 
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('contact.html.twig', array(
     "connected" => $isconnected,
    ));
    } else {
        return $app['twig']->render('contact.html.twig', array()) ;
    }

})
    ->bind('contact');
$app->post('/contact', "Coolloc\Controller\ContactController::contactAction")->before($verifContact);



//*** ROUTES GET ***//


//MERCI
$app->get('connected/merci', function () use ($app) {
    return $app['twig']->render('connected/merci.html.twig', array());
})
    ->bind('merci');


//MENTIONS LEGALES
$app->get('/mentions-legales', function () use ($app) {
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();


    if ($isConnectedAndAdmin){
        return $app['twig']->render('mentions-legales.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected, 
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('mentions-legales.html.twig', array(
     "connected" => $isconnected,
    ));
    } else {
        return $app['twig']->render('mentions-legales.html.twig', array());
    }
})
    ->bind('mentions-legales');


//CONDITIONS GENERALES DE VENTES
$app->get('/conditions-generales-de-vente', function () use ($app) {
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();


    if ($isConnectedAndAdmin){
        return $app['twig']->render('conditions-generales-de-vente.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected, 
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('conditions-generales-de-vente.html.twig', array(
     "connected" => $isconnected,
    ));
    } else {
        return $app['twig']->render('conditions-generales-de-vente.html.twig', array());
    }
})
    ->bind('conditions-generales-de-vente');


//A PROPOS
$app->get('/a-propos', function () use ($app) {
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();


    if ($isConnectedAndAdmin){
        return $app['twig']->render('a-propos.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected, 
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('a-propos.html.twig', array(
     "connected" => $isconnected,
    ));
    } else {
        return $app['twig']->render('a-propos.html.twig', array());
    }

})
    ->bind('a-propos');




//*****************************//
//*** ROUTE AVEC CONNECTION ***//
//*****************************//



// verification si user connecté

$app->get('/verif-connected/{pagename}/', 'Coolloc\Controller\Controller::verifConnected')->bind('verif-connected');



//*** ROUTES GET/POST ***//

//DETAILS ANNONCE CONNECTER
$app->get('/connected/details-annonce-connecter', function () use ($app) {
    return $app['twig']->render('details-annonce-connecter.html.twig', array());
})
    ->bind('details-annonce-connecter');
$app->post('/connected/details-annonce-connecter', function () use ($app) {
    //controleur
});


//PROFIL
$app->get('/connected/profil', function () use ($app) {
    $profilInfo = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);
    $optionUser = Model::userOptionOnly($profilInfo['id_user'], $app);
    $annonceUser = Model::annonceByUser($profilInfo['id_user'], $app);
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();

    if ($isConnectedAndAdmin){
        return $app['twig']->render('connected/profil.html.twig', array("profilInfo" => $profilInfo, "userOption" => $optionUser, "annonceUser" => $annonceUser,  "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected, ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('connected/profil.html.twig', array("profilInfo" => $profilInfo, "userOption" => $optionUser, "annonceUser" => $annonceUser, "connected" => $isconnected, ));

    }
    else {
        return $app->redirect('/Coolloc/public');

    }

    
})
    ->bind('profil');
$app->post('/connected/profil', 'Coolloc\Controller\StatusController::changeStatusAction')->before($verifStatus);

//MODIFIER PROFIL
$app->get('/connected/profil-modif', function () use ($app) {
    $profilInfo = Model::userByTokenSession($_SESSION['membre']['zoubida'], $app);

    return $app['twig']->render('connected/profil-modif.html.twig', array("profilInfo" => $profilInfo));
})
    ->bind('profil-modif');
$app->post('/connected/profil', function () use ($app) {
    //controleur
});

//AJOUT ANNONCE
$app->get('/connected/ajout-annonce', function () use ($app) {

    return $app['twig']->render('/connected/ajout-annonce.html.twig', array());
})
    ->bind('ajout-annonce');

$app->post('/connected/ajout-annonce', 'Coolloc\Controller\AnnonceController::annonceAction')->before($verifParamAnnonce);


// GERER ANNONCE
$app->get('/connected/gerer-annonce', function () use ($app) {

    return $app['twig']->render('gerer-annonce.html.twig', array());

})
    ->bind('gerer-annonce');
$app->post('/connected/gerer-annonce', function () use ($app) {
    //controleur
});


//AJOUT DETAILS PROFIL
$app->get('/connected/ajout-details-profil', function () use ($app) {
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();


    if ($isConnectedAndAdmin){
        return $app['twig']->render('/connected/ajout-details-profil.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected, 
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('/connected/ajout-details-profil.html.twig', array());
    } 

    else {
        return $app->redirect('Coolloc/public/login');
    }
})
    ->bind('ajout-details-profil');
$app->post('/connected/ajout-details-profil', function () use ($app) {
    //controleur
});


//ajout temoignage
$app->get('connected/temoigner', function () use ($app) {

    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();


    if ($isConnectedAndAdmin){
        return $app['twig']->render('connected/temoigner.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected, 
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('connected/temoigner.html.twig', array(
     "connected" => $isconnected,
    ));
    }
    else
    {
        return $app->redirect('Coolloc/public/login') ;
    }

})
    ->bind('temoigner');
$app->post('/connected/temoigner', 'Coolloc\Controller\CommentController::commentAction')->before($verifParamComment);



//*** ROUTES GET ***//


//*************************************//
//*** ROUTE AVEC CONNECTION ET ADMIN***//
//*************************************//

//DASHBOARD
$app->get('/connected/sabit','Coolloc\Controller\AdminController::selectedAdminInfos')-> bind('dashboard');



//GERER USER CHERCHANT DES COLOCATIONS
$app->get('/connected/sabit/gerer-user-colocations','Coolloc\Controller\AdminController::selectedUsersColocationsAndAdminInfos')-> bind('gerer-user-colocations');


//GERER USER CHERCHANT DES COLOCATAIRES
$app->get('/connected/sabit/gerer-user-colocataires','Coolloc\Controller\AdminController::selectedUsersColocatairesAndAdminInfos')-> bind('gerer-user-colocataires');



//MODIFIER STATUT ACTIF/INACTIF D'UN USER
$app->get('/connected/sabit/gerer-user/{id_user}/{page_actuelle}','Coolloc\Controller\AdminController::modifyUserStatus')-> bind('modify-status-user');


//AFFICHER DETAILS STATUT UTILISATEUR
$app->get('/connected/sabit/details-profil/{id_user}','Coolloc\Controller\AdminController::detailsUser')-> bind('details-profil');




//GERER ANNONCE ADMIN
$app->get('/connected/sabit/gerer-annonces','Coolloc\Controller\AdminController::selectedAnnoncesAndAdminInfos')-> bind('gerer-annonces-admin');






//GERER FAQ
$app->get('/connected/sabit/gerer-faq', function () use ($app) {
    // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
    $isconnectedAndAdmin = Controller::ifConnectedAndAdmin();

    if ($isconnectedAndAdmin) { // Si l'utilisateur est admin
        return $app['twig']->render('dashboard/index-dashboard.html.twig', array(
            "userAdmin" => Model::userByTokenSession($_SESSION['membre']['zoubida'], $app),
        ));
    } else {// Si l'utilisateur n'est pas admin
        return $app->redirect('/Coolloc/public');
    }})
    ->bind('gerer-faq');
$app->post('/connected/admin/gerer-faq', function () use ($app) {
    //controleur
});



//GERER CONTENU
$app->get('/connected/sabit/gerer-contenu', function () use ($app) {
    // VERIFICATION SI L'UTILISATEUR EST CONNECTER ET ADMIN
    $isconnectedAndAdmin = Controller::ifConnectedAndAdmin();

    if ($isconnectedAndAdmin) { // Si l'utilisateur est admin
        $adminDonnes = new AdminController();
        return $app['twig']->render('dashboard/index-dashboard.html.twig', array(
            "userAdmin" => $adminDonnes,
        ));
    } else {// Si l'utilisateur n'est pas admin
        return $app->redirect('/Coolloc/public');
    }})
    ->bind('gerer-contenu');
$app->post('/connected/admin/gerer-contenu', function () use ($app) {
    //controleur
});



//*******************//
//*** ROUTE ERREUR***//
//*******************//

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }
    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
