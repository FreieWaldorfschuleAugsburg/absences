<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'IndexController::index', ['filter' => ['login', 'components']]);
$routes->get('/logout', 'IndexController::logout', ['filter' => ['login', 'components']]);
$routes->get('/view/(:any)', 'AbsenceController::view/$1', ['filter' => ['staff', 'components']]);

$routes->get('/print_absent/(:any)', 'AbsenceController::printAbsent/$1', ['filter' => ['staff', 'components']]);
$routes->get('/print_present/(:any)', 'AbsenceController::printPresent/$1', ['filter' => ['staff', 'components']]);
$routes->post('/report', 'AbsenceController::reportAbsent', ['filter' => ['login', 'components']]);

$routes->get('/api/absence_events/(:any)', 'AbsenceController::apiAbsenceEvents/$1', ['filter' => ['login']]);
$routes->get('/api/entries/(:any)', 'AbsenceController::apiEntries/$1', ['filter' => ['staff', 'login']]);
$routes->get('/api/report_missing/(:any)', 'MissingController::apiReportMissing/$1', ['filter' => ['staff', 'components']]);
$routes->get('/api/revoke_missing/(:any)', 'MissingController::apiRevokeMissing/$1', ['filter' => ['staff', 'components']]);
$routes->get('/api/report_late/(:any)', 'AbsenceController::apiReportLate/$1', ['filter' => ['staff', 'components']]);
$routes->get('/api/report_leave/(:any)', 'AbsenceController::apiReportLeave/$1', ['filter' => ['staff', 'components']]);
$routes->get('/api/delete_absence/(:any)', 'AbsenceController::apiDeleteAbsence/$1', ['filter' => ['staff', 'components']]);

$routes->cli('/cron_reminder', 'MissingController::cronFollowUpReminder');