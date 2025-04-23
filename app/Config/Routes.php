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
$routes->get('materias/generar-word/(:num)', 'MateriasController::generarWord/$1');
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

// Materias CRUD
$routes->get('materias', 'MateriasController::index');
$routes->post('materias/listar', 'MateriasController::listar');
$routes->get('materias/nueva', 'MateriasController::nueva');
$routes->post('materias/guardar', 'MateriasController::guardar');
$routes->get('materias/editar/(:num)', 'MateriasController::editar/$1');
$routes->post('materias/actualizar/(:num)', 'MateriasController::actualizar/$1');
$routes->get('materias/eliminar/(:num)', 'MateriasController::eliminar/$1');
$routes->get('materias/(:num)', 'MateriasController::ver/$1');

// Secciones
$routes->group('materias', function ($routes) {
    // Objetivos
    $routes->get('objetivos/(:num)', 'MateriasController::objetivos/$1');
    $routes->get('nuevo-objetivo/(:num)', 'MateriasController::nuevoObjetivo/$1');
    $routes->post('guardar-objetivo/(:num)', 'MateriasController::guardarObjetivo/$1');
    $routes->get('editar-objetivo/(:num)/(:num)', 'MateriasController::editarObjetivo/$1/$2');
    $routes->post('actualizar-objetivo/(:num)/(:num)', 'MateriasController::actualizarObjetivo/$1/$2');
    $routes->get('eliminar-objetivo/(:num)/(:num)', 'MateriasController::eliminarObjetivo/$1/$2');

    // Unidades y Temas
    $routes->get('unidades/(:num)', 'MateriasController::unidades/$1');
    $routes->get('nueva-unidad/(:num)', 'MateriasController::nuevaUnidad/$1');
    $routes->post('guardar-unidad/(:num)', 'MateriasController::guardarUnidad/$1');
    // ... rutas similares para editar/actualizar/eliminar unidad

    $routes->get('nuevo-tema/(:num)', 'MateriasController::nuevoTema/$1');
    $routes->post('guardar-tema/(:num)', 'MateriasController::guardarTema/$1');
    // ... rutas similares para editar/actualizar/eliminar tema

    // Bibliografía
    $routes->get('bibliografia/(:num)', 'MateriasController::bibliografia/$1');
    $routes->get('nueva-bibliografia/(:num)', 'MateriasController::nuevaBibliografia/$1');
    $routes->post('guardar-bibliografia/(:num)', 'MateriasController::guardarBibliografia/$1');
    // ... rutas similares para editar/actualizar/eliminar bibliografia

});