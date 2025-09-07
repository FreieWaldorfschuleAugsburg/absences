<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'IndexController::index');

$routes->get('/view/(:any)', 'AbsenceController::view/$1');
$routes->get('/absent/(:any)', 'AbsenceController::absent/$1');

$routes->get('/print_absent/(:any)', 'AbsenceController::printAbsent/$1');
$routes->get('/print_present/(:any)', 'AbsenceController::printPresent/$1');
