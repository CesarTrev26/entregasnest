<?php

require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\ProjectsController;
use Controllers\LoginController;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$router = new Router();

$router->get('/', [LoginController::class, 'Index']);
$router->post('/', [LoginController::class, 'Index']);
$router->get('cerrar-sesion', [LoginController::class, 'Logout']);
$router->get('proyectos', [ProjectsController::class, 'Proyectos']);
$router->get('proyectos/Anida', [ProjectsController::class, 'Anida']);
$router->get('proyectos/WE2T', [ProjectsController::class, 'WE2T']);
$router->get('proyectos/Rise', [ProjectsController::class, 'Rise']);

$router->comprobarRutas();

