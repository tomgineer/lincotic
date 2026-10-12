<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('search', 'Home::search');
$routes->get('password-generator', 'Utilities::passwordGenerator');
$routes->get('date-time-calculator', 'Utilities::dateTimeCalculator');
$routes->get('hash-generator', 'Utilities::hashGenerator');
$routes->post('hash-generator/generate', 'Utilities::generateHashes');
$routes->get('work-time-tracker', 'Utilities::workTimeTracker');

$routes->group('edit', ['filter' => 'group:admin'], static function (RouteCollection $routes): void {
    $routes->post('users', 'Edit::createUser');
    $routes->post('users/(:num)', 'Edit::updateUser/$1');
    $routes->post('links', 'Edit::createLink');
    $routes->get('links/(:num)', 'Edit::link/$1');
    $routes->post('links/(:num)', 'Edit::updateLink/$1');
    $routes->delete('links/(:num)', 'Edit::deleteLink/$1');
    $routes->post('collections', 'Edit::createCollection');
    $routes->get('collections/(:num)', 'Edit::collection/$1');
    $routes->post('collections/(:num)', 'Edit::updateCollection/$1');
    $routes->delete('collections/(:num)', 'Edit::deleteCollection/$1');
});

service('auth')->routes($routes, ['only' => ['login', 'logout']]);
