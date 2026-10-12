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

service('auth')->routes($routes, ['only' => ['login', 'logout']]);
