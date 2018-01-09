<?php

    // ini_set('display_errors', 0); // Message d'erreur n'apparait pas

    require_once __DIR__.'/../vendor/autoload.php';

    use Silex\Application;
    $app = new Application();
    require __DIR__.'/../src/register.php';
    require __DIR__.'/../src/route.php';
    $app->run();
