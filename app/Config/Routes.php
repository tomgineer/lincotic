<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Hub::index');
$routes->get('search', 'Hub::search');
$routes->get('@(:segment)/search', 'Hub::searchUser/$1');
$routes->get('@(:segment)', 'Hub::show/$1');
$routes->get('go/(:num)', 'Links::follow/$1');
$routes->get('password-generator', 'Utilities::passwordGenerator');
$routes->get('date-time-calculator', 'Utilities::dateTimeCalculator');
$routes->get('hash-generator', 'Utilities::hashGenerator');
$routes->post('hash-generator/generate', 'Utilities::generateHashes');
$routes->get('work-time-tracker', 'Utilities::workTimeTracker');

$routes->group('edit', ['filter' => 'group:admin'], static function (RouteCollection $routes): void {
    $routes->post('users', 'Editing\Users::create');
    $routes->post('users/(:num)', 'Editing\Users::update/$1');
});

$routes->group('edit', ['filter' => 'session'], static function (RouteCollection $routes): void {
    $routes->post('links', 'Editing\Links::create');
    $routes->get('links/(:num)', 'Editing\Links::show/$1');
    $routes->post('links/(:num)', 'Editing\Links::update/$1');
    $routes->delete('links/(:num)', 'Editing\Links::delete/$1');
    $routes->post('collections', 'Editing\Collections::create');
    $routes->post('collections/order', 'Editing\Collections::reorder');
    $routes->get('collections/(:num)', 'Editing\Collections::show/$1');
    $routes->post('collections/(:num)', 'Editing\Collections::update/$1');
    $routes->delete('collections/(:num)', 'Editing\Collections::delete/$1');
});

service('auth')->routes($routes, ['only' => ['login', 'logout']]);
