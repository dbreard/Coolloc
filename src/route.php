<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

//*****************************//
//*** ROUTE SANS CONNECTION ***//
//*****************************//

//*** ROUTES GET/POST ***//


//ROUTE HOME
$app->get('/', function () use ($app) {

    // si internaute non connecté rdv vers index-nc.html.twig autrement rdv vers index-c.html.twig
    return $app['twig']->render('index.html.twig', array());

})
->bind('accueil');
$app->post('/', function () use ($app) {
    //controleur
});

//RESULTAT RECHERCHE
$app->get('/resultat-recherche', function () use ($app) {
    return $app['twig']->render('details-annonce.html.twig', array());
})
    ->bind('resultat-recherche');
$app->post('/resultat-recherche', function () use ($app) {
    //controleur
});

//DETAILS ANNONCE NON CONNECTER
$app->get('/details-annonce-non-connecter', function () use ($app) {
    return $app['twig']->render('details-annonce.html.twig', array());
})
    ->bind('details-annonce');
$app->post('/details-annonce-non-connecter', function () use ($app) {
    //controleur
});

//LOGIN
$app->get('/login', function () use ($app) {
    return $app['twig']->render('formulaires/login.html.twig', array());
})
    ->bind('login');
$app->post('/login', function (Request $request) use ($app ) {
    array ("post" => $request);
});


//INSCRIPTION
$app->get('/inscription', function () use ($app) {
    return $app['twig']->render('formulaires/register.html.twig', array());
})
    ->bind('inscription');
$app->post('/inscription', "Coolloc\Controller\RegisterController::registerAction")->before($verifParamRegister);

//MDP OUBLIER
$app->get('/forgotten-password', function () use ($app) {
    return $app['twig']->render('forgotten-password.html.twig', array());
})
    ->bind('forgotten-password');
$app->post('/forgotten-password', function () use ($app) {
    //controleur
});

//FAQ
$app->get('/faq', function () use ($app) {
    return $app['twig']->render('faq.html.twig', array());
})
    ->bind('faq');
$app->post('/faq', function () use ($app) {
    //controleur
});

//CONTACT
$app->get('/contact', function () use ($app) {
    return $app['twig']->render('formulaires/contact.html.twig', array());
})
    ->bind('contact');
$app->post('/contact', "Coolloc\Controller\ContactController::contactAction")->before($verifContact);

//*** ROUTES GET ***//

//MENTIONS LEGALES
$app->get('/mentions-legales', function () use ($app) {
    return $app['twig']->render('mentions-legales.html.twig', array());
})
    ->bind('mentions-legales');

//CONDITIONS GENERALES DE VENTES
$app->get('/conditions-generales-de-vente', function () use ($app) {
    return $app['twig']->render('conditions-generales-de-vente.html.twig', array());
})
    ->bind('conditions-generales-de-vente');

//A PROPOS
$app->get('/a-propos', function () use ($app) {
    return $app['twig']->render('a-propos.html.twig', array());
})
    ->bind('a-propos');


//*****************************//
//*** ROUTE AVEC CONNECTION ***//
//*****************************//

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
    return $app['twig']->render('profil.html.twig', array());
})
    ->bind('profil');
$app->post('/connected/profil', function () use ($app) {
    //controleur
});

//AJOUT ANNONCE

$app->get('connected/ajout-annonce', function () use ($app) {

    return $app['twig']->render('connected/ajout-annonce.html.twig', array());
})
    ->bind('ajout-annonce');
$app->post('/connected/ajout-annonce', function () use ($app) {
    //controleur
});

//GERER ANNONCE
$app->get('/connected/gerer-annonce', function () use ($app) {
    return $app['twig']->render('gerer-annonce.html.twig', array());
})
    ->bind('gerer-annonce');
$app->post('/connected/gerer-annonce', function () use ($app) {
    //controleur
});

//*** ROUTES GET ***//

//GERER ANNONCE
$app->get('/connected/deconnexion', function () use ($app) {
    return $app['twig']->render('gerer-annonce.html.twig', array());
})
    ->bind('deconnexion');

//*************************************//
//*** ROUTE AVEC CONNECTION ET ADMIN***//
//*************************************//

//GERER USER
$app->get('/connected/admin/gerer-user', function () use ($app) {
    return $app['twig']->render('gerer-annonce-admin.html.twig', array());
})
    ->bind('gerer-user');
$app->post('/connected/admin/gerer-user', function () use ($app) {
    //controleur
});

//GERER ANNONCE ADMIN
$app->get('/connected/admin/gerer-annonce', function () use ($app) {
    return $app['twig']->render('gerer-annonce-admin.html.twig', array());
})
    ->bind('gerer-annonce');
$app->post('/connected/admin/gerer-annonce', function () use ($app) {
    //controleur
});

//GERER FAQ
$app->get('/connected/admin/gerer-faq', function () use ($app) {
    return $app['twig']->render('gerer-faq-admin.html.twig', array());
})
    ->bind('gerer-faq');
$app->post('/connected/admin/gerer-faq', function () use ($app) {
    //controleur
});

//GERER CONTENU
$app->get('/connected/admin/gerer-contenu', function () use ($app) {
    return $app['twig']->render('gerer-contenu-admin.html.twig', array());
})
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
