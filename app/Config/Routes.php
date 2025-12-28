<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'IndexController::index', ['filter' => ['login', 'components']]);

$routes->get('/logout', 'IndexController::logout', ['filter' => ['login', 'components']]);

$routes->get('/view/(:any)', 'AbsenceController::view/$1', ['filter' => ['login', 'components']]);
$routes->get('/absent/(:any)', 'AbsenceController::absent/$1', ['filter' => ['login', 'components']]);

$routes->get('/print_absent/(:any)', 'AbsenceController::printAbsent/$1', ['filter' => ['login', 'components']]);
$routes->get('/print_present/(:any)', 'AbsenceController::printPresent/$1', ['filter' => ['login', 'components']]);
