<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Coolloc\Controller\Controller;
use Coolloc\Controller\HomeController;
use Coolloc\Controller\AProposController;
use Coolloc\Controller\FaqController;
use Coolloc\Controller\SearchController;
use Coolloc\Model\Model;


//Request::setTrustedProxies(array('127.0.0.1'));

//*****************************//
//*** ROUTE SANS CONNECTION ***//
//*****************************//

//*** ROUTES GET/POST ***//


//ROUTE HOME
$app->get('/', function () use ($app) {
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();
    $membresAnnoncesInfo = new HomeController;
    $donneesMembresAnnonces = $membresAnnoncesInfo->homeAction($app);
    $userSearchColocation = Controller::userSearchColocation($app);


    if ($isConnectedAndAdmin){
        return $app['twig']->render('index.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin,
            "connected" => $isconnected,
            "affichage" => $donneesMembresAnnonces,
            "userSearchColocation" => $userSearchColocation,
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('index.html.twig', array(
            "connected" => $isconnected,
            "affichage" => $donneesMembresAnnonces,
            "userSearchColocation" => $userSearchColocation,
    ));

    }


    else {
        return $app['twig']->render('index.html.twig', array(
            "affichage" => $donneesMembresAnnonces,
        )) ;
    }

    

    // si internaute non connecté rdv vers index-nc.html.twig autrement rdv vers index-c.html.twig
    // return $app['twig']->render('index.html.twig', array());

})
->bind('accueil');
$app->post('/', "Coolloc\Controller\SearchController::searchAction");


//AFFICHAGE DE TOUT LES PROFIL CHERCHANT DES COLOCATIONS
$app->get('/profils-public-colocataire/{page}', function (Request $request) use ($app) {

    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();
    $membresAnnoncesInfo = new SearchController;
    $donneesMembresAnnonces = $membresAnnoncesInfo->searchAllProfils($app, $request);
    $userSearchColocation = Controller::userSearchColocation($app);



    if ($isConnectedAndAdmin) {
        return $app['twig']->render('serp-profil.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected,
            "affichage" => $donneesMembresAnnonces,
            "userSearchColocation" => $userSearchColocation,
        ));
    } elseif ($isconnected) {
        return $app['twig']->render('serp-profil.html.twig', array(
            "connected" => $isconnected,
            "affichage" => $donneesMembresAnnonces,
            "userSearchColocation" => $userSearchColocation,
        ));

    } else {
        return $app['twig']->render('serp-profil.html.twig', array(
            "affichage" => $donneesMembresAnnonces,
        ));
    }

})->bind('profils-colocataires-recherchant-colocation');



//RESULTAT RECHERCHE
$app->get('/resultat-recherche/{page}', "Coolloc\Controller\SearchController::searchAllAnnonce")->bind('resultat-recherche');



//DETAILS ANNONCE
$app->get('/details-annonce/{id_annonce}', "Coolloc\Controller\AnnonceController::detailAnnonceAction")->bind('details-annonce');
$app->post('/details-annonce', function () use ($app) {
    //controleur
});



//LOGIN
$app->get('/login', function () use ($app) {
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();
    $userSearchColocation = Controller::userSearchColocation($app);


    if ($isConnectedAndAdmin || $isconnected)
        return $app->redirect('/Coolloc/public') ;

    return $app['twig']->render('formulaires/login.html.twig', array());

    })
    ->bind('login');
    $app->post('/login', "Coolloc\Controller\LoginController::loginAction")->before($verifParamLogin);

// LOGOUT
$app->get('/connected/deconnexion','Coolloc\Controller\Controller::sessionDestroy')->bind('deconnexion');


//INSCRIPTION
$app->get('/inscription', function () use ($app) {
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();
    $userSearchColocation = Controller::userSearchColocation($app);


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
$app->get('/change-password/{token}', function () use ($app) {
    return $app['twig']->render('basic/change-password.html.twig', array());
})
    ->bind('change-password');
$app->post('/change-password/{token}', "Coolloc\Controller\ChangePassController::changePassForgottenAction")->before($verifParamChangePass);



//FAQ
$app->get('/faq', function () use ($app) {
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();
    $faq = new FaqController();
    $resultatFaq = $faq->selectFaqs($app);
    $userSearchColocation = Controller::userSearchColocation($app);


    if ($isConnectedAndAdmin){
        return $app['twig']->render('faq.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin, "connected" => $isconnected,
            "infosFaqs" => $resultatFaq,
            "userSearchColocation" => $userSearchColocation,
        ));
    }
    elseif ($isconnected) {
        return $app['twig']->render('faq.html.twig', array(
            "connected" => $isconnected,
            "infosFaqs" => $resultatFaq,
            "userSearchColocation" => $userSearchColocation,
    ));
    }
    else {
        return $app['twig']->render('faq.html.twig', array(
            "infosFaqs" => $resultatFaq,
        ));
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
    $userSearchColocation = Controller::userSearchColocation($app);



    if ($isConnectedAndAdmin){
        return $app['twig']->render('contact.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin,
            "connected" => $isconnected,
            "userSearchColocation" => $userSearchColocation,
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('contact.html.twig', array(
             "connected" => $isconnected,
             "userSearchColocation" => $userSearchColocation,
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
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();
    $userSearchColocation = Controller::userSearchColocation($app);



    if ($isConnectedAndAdmin){
        return $app['twig']->render('connected/merci.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin,
            "connected" => $isconnected,
            "userSearchColocation" => $userSearchColocation,
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('connected/merci.html.twig', array(
             "connected" => $isconnected,
             "userSearchColocation" => $userSearchColocation,
    ));
    } else {
        return $app->redirect('/Coolloc/public');
    }

})
    ->bind('merci');


//MENTIONS LEGALES
$app->get('/mentions-legales', function () use ($app) {
    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();
    $userSearchColocation = Controller::userSearchColocation($app);



    if ($isConnectedAndAdmin){
        return $app['twig']->render('mentions-legales.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin,
            "connected" => $isconnected,
            "userSearchColocation" => $userSearchColocation,
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('mentions-legales.html.twig', array(
            "connected" => $isconnected,
            "userSearchColocation" => $userSearchColocation,
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
    $userSearchColocation = Controller::userSearchColocation($app);



    if ($isConnectedAndAdmin){
        return $app['twig']->render('conditions-generales-de-vente.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin,
            "connected" => $isconnected,
            "userSearchColocation" => $userSearchColocation,
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('conditions-generales-de-vente.html.twig', array(
            "connected" => $isconnected,
            "userSearchColocation" => $userSearchColocation,
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
    $membresAnnoncesInfo = new HomeController;
    $donneesMembresAnnonces = $membresAnnoncesInfo->homeAction($app);
    $userSearchColocation = Controller::userSearchColocation($app);




    if ($isConnectedAndAdmin){
        return $app['twig']->render('a-propos.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin,
            "connected" => $isconnected,
            "affichage" => $donneesMembresAnnonces,
            "userSearchColocation" => $userSearchColocation,
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('a-propos.html.twig', array(
            "connected" => $isconnected,
            "affichage" => $donneesMembresAnnonces,
            "userSearchColocation" => $userSearchColocation,
    ));
    } else {
        return $app['twig']->render('a-propos.html.twig', array(
            "affichage" => $donneesMembresAnnonces,
        ));

    }
})
    ->bind('a-propos');


    //AFFICHAGE PRESENTATION PROFIL

$app->get('/fiche-profil/{id_user}', 'Coolloc\Controller\DetailsProfilController::UserInfoById')->bind('fiche-profil');






//*****************************//
//*** ROUTE AVEC CONNECTION ***//
//*****************************//



// verification si user connecté

$app->get('/verif-connected/{pagename}/', 'Coolloc\Controller\Controller::verifConnected')->bind('verif-connected');



//*** ROUTES GET/POST ***//


//PROFIL
$app->get('/connected/profil', 'Coolloc\Controller\ProfilAccessController::profilAccessAction')
    ->bind('profil');
$app->post('/connected/profil', 'Coolloc\Controller\StatusController::changeStatusAction')->before($verifStatus);



//MODIFIER PROFIL
$app->get('/connected/profil-modif', 'Coolloc\Controller\ModifProfilController::sendUserProfilInfo')->bind('profil-modif');

$app->post('/connected/profil-modif', 'Coolloc\Controller\ModifProfilController::updateProfilAction');



//AJOUT ANNONCE
$app->get('/connected/ajout-annonce', function () use ($app) {

    $isconnected = Controller::ifConnected();
    $isConnectedAndAdmin = Controller::ifConnectedAndAdmin();
    $userSearchColocation = Controller::userSearchColocation($app);



    if ($isConnectedAndAdmin){
        return $app['twig']->render('/connected/ajout-annonce.html.twig', array(
            "isConnectedAndAmin" => $isConnectedAndAdmin,
            "connected" => $isconnected,
            "userSearchColocation" => $userSearchColocation,
        ));
    }

    elseif ($isconnected) {
        return $app['twig']->render('/connected/ajout-annonce.html.twig', array(
            "connected" => $isconnected,
            "userSearchColocation" => $userSearchColocation,
        ));
    }

    else {
        return $app->redirect('/Coolloc/public/login');
    }

})
    ->bind('ajout-annonce');

$app->post('/connected/ajout-annonce', 'Coolloc\Controller\AnnonceController::annonceAction')->before($verifParamAnnonce);


// GERER ANNONCE
$app->get('/connected/gerer-annonce/{id_annonce}', 'Coolloc\Controller\UpdateAnnonceController::selectAnnonceAction')
    ->bind('gerer-annonce');

$app->post('/connected/gerer-annonce/{id_annonce}', 'Coolloc\Controller\UpdateAnnonceController::updateAnnonceAction')->before($verifParamModifAnnonce);



//AJOUT DETAILS PROFIL

$app->get('/connected/ajout-details-profil', 'Coolloc\Controller\DetailsProfilController::sendUserOption')
    ->bind('ajout-details-profil');
$app->post('/connected/ajout-details-profil', 'Coolloc\Controller\DetailsProfilController::detailsProfilAction');



//ajout temoignage
$app->get('connected/temoigner', 'Coolloc\Controller\CommentController::sendCommentInfos')
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


//AFFICHER DETAILS PROFIL UTILISATEUR
$app->get('/connected/sabit/details-profil/{id_user}','Coolloc\Controller\AdminController::detailsUser')-> bind('details-profil');


//AFFICHER DETAILS ANNONCE
$app->get('/connected/sabit/details-annonce-admin/{id_annonce}','Coolloc\Controller\AdminController::detailsAnnonces')-> bind('details-annonce-admin');


//GERER ANNONCE ADMIN
$app->get('/connected/sabit/gerer-annonces','Coolloc\Controller\AdminController::selectedAnnoncesAndAdminInfos')->bind('gerer-annonces-admin');


//SUPPRIMER ANNONCE ADMIN
$app->get('/connected/sabit/gerer-annonces/{id_annonce}','Coolloc\Controller\AnnonceController::deleteAnnonceAction')->bind('gerer-annonces-admin-suppression');


//GERER FAQ
$app->get('/connected/sabit/gerer-faq', 'Coolloc\Controller\FaqController::selectedFaqAndAdminInfo') ->bind('gerer-faq');
$app->post('/connected/sabit/gerer-faq','Coolloc\Controller\FaqController::faqAction')->before($verifParamCommentFaq);


//MODIFIER - SUPPRIMER FAQ
$app->get('/connected/sabit/gerer-faq/{id_faq}/{action}', 'Coolloc\Controller\FaqController::modifyDeleteFaq') ->bind('gerer-faq-modifier-ou-supprimer');
$app->post('/connected/sabit/gerer-faq/{id_faq}/{action}','Coolloc\Controller\FaqController::modifyFaq')->before($verifParamCommentFaq);


//GERER COMMENTAIRES-TEMOIGNAGE
$app->get('/connected/sabit/gerer-temoignage', 'Coolloc\Controller\CommentController::selectCommentAndAdminInfo')->bind('gerer-temoignage');
$app->get('/connected/sabit/gerer-temoignage/{id_comments}','Coolloc\Controller\CommentController::deleteComment')->bind('supprimer-temoignage');



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
