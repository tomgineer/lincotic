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
    $routes->get('links/(:num)', 'Edit::link/$1');
    $routes->post('links/(:num)', 'Edit::updateLink/$1');
    $routes->delete('links/(:num)', 'Edit::deleteLink/$1');
});

service('auth')->routes($routes, ['only' => ['login', 'logout']]);
