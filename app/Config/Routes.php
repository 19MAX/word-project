<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rutas públicas
$routes->get('/', 'Auth\LoginController::index');
$routes->get('login', 'Auth\LoginController::index');
$routes->post('auth/login', 'Auth\LoginController::login');
$routes->get('logout', 'Auth\LoginController::logout');

// Rutas protegidas
$routes->get('dashboard', 'DashboardController::index');
// ADMIN
$routes->group('admin', static function ($routes) {
    $routes->get('/', 'Admin\AdminController::index');
    $routes->get('settings', 'Admin\AdminController::settings');

    $routes->group('users', static function ($users) {
        $users->get('/', 'Admin\AdminController::users');
        $users->post('create', 'Admin\UsersController::create');
        $users->post('update', 'Admin\UsersController::update');
        $users->post('delete', 'Admin\UsersController::delete');
        $users->post('reset-password', 'Admin\UsersController::resetPassword');
    });

    // Asistencias (Admin)
    $routes->get('attendances', 'Admin\AdminAttendanceController::index');
    $routes->post('attendances/getAttendances', 'Admin\AdminAttendanceController::getAttendances');
    $routes->post('attendances/validate', 'Admin\AdminAttendanceController::validateA');
});