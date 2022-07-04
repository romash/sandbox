<?php

namespace Game\Router;

// This file must only configure autoloader and library paths, making code further executable, no game mechanic there,
require_once 'include/bootstrap.php';

use Application\App;
use Application\Exception\HttpNotFoundException;
use Application\Routing\RouteCollectorProxy as Group;

// Custom response builder and custom context for the app
$response = new Response();
$context = \Game\Context::getInstance();

// The app
$app = new App($response, $context);
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// Configure error handling
$errorMiddleware = $app->addErrorMiddleware(false, true, true);
// what to generate in case of page not found
$errorMiddleware->setErrorHandler(HttpNotFoundException::class, Controller\Error\NotFound::class);

$app->group('', function (Group $group) {

        /**
         * Game pages
         */
        $group->group('', function (Group $group) {
            $group->get('/marketplace/buy', Controller\Page\Marketplace::class . ':buy');
            $group->get('/marketplace/offer', Controller\Page\Marketplace::class . ':offer');
        })
            ->add(Middleware\Authorize::class);

        /**
         * REST API
         */

        $group->group('/api/v1', function (Group $group) {

            /**
             * Game requests
             */
            $group->group('', function (Group $group) {
                //Marketplace
                $group->group('/marketplace', function (Group $group) {
                    $group->get('/offers/all', Controller\Api\Marketplace::class . ':getAllOffers');
                    $group->get('/offers/{id:[0-9]+}', Controller\Api\Marketplace::class . ':getOffer');
                    $group->post('/offers/create', Controller\Api\Marketplace::class . ':createOffer');
                    $group->post('/offers/{id:[0-9]+}/edit', Controller\Api\Marketplace::class . ':editOffer');
                    $group->post('/offers/{id:[0-9]+}/delete', Controller\Api\Marketplace::class . ':removeOffer');
                    $group->post('/offers/{id:[0-9]+}/accept', Controller\Api\Marketplace::class . ':acceptOffer');
                });
            })
                ->add(Middleware\AuthorizeAjaxToken::class)
                ->add(Middleware\Authorize::class);
        })
            ->add(Middleware\RESTify::class);

    })
    // Bootstrap the request, initialize DB, session, etc.
    ->add(Middleware\Bootstrap::class);

$app->run();
