<?php

/**
 * The starting point for the continuous deployment test application
 *
 * Verify the request should not be served as a static file.
 * Load composer's autoloader.
 * Prepare twig and the dependency injector.
 * Then create an instance of the application and render the page.
 *
 * @author Sean Wallis <sean.wallis2@networkrail.co.uk>
 */

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

// Load composer's autoloader
require 'vendor/autoload.php';

// Setup Twig Template Engine from the app/views directory
$loader = new \Twig_Loader_Filesystem('app/views');
$twig = new \Twig_Environment($loader);

// Prepare the Guzzle Client for the service
$guzzleClient = new \GuzzleHttp\Client(
    ['base_uri' => 'https://api.github.com']
);

// Prepare dependency injector
$injector = new \Auryn\Injector;
$injector->share($twig);
$injector->share($guzzleClient);

// Create an instance of the application and render the page.
$app = $injector->make('CD\App');
$appInstance = $app->getInstance();
echo $appInstance->render();
