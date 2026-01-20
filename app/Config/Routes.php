<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'IndexController::index', ['filter' => ['login', 'components']]);

$routes->get('/logout', 'IndexController::logout', ['filter' => ['login', 'components']]);

$routes->get('/view/(:any)', 'AbsenceController::view/$1', ['filter' => ['staff', 'components']]);
$routes->get('/report_missing/(:any)', 'MissingController::reportMissing/$1', ['filter' => ['staff', 'components']]);
$routes->get('/revoke_missing/(:any)', 'MissingController::revokeMissing/$1', ['filter' => ['staff', 'components']]);

$routes->get('/print_absent/(:any)', 'AbsenceController::printAbsent/$1', ['filter' => ['staff', 'components']]);
$routes->get('/print_present/(:any)', 'AbsenceController::printPresent/$1', ['filter' => ['staff', 'components']]);
