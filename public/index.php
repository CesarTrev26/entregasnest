<?php

require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\ProjectsController;
use Controllers\LoginController;
use Controllers\AdminController;
use Controllers\UserController;
use Controllers\ProjectCRUDController;
use Controllers\DepartmentCRUDController;
use Controllers\CustomerCRUDController;
use Controllers\FilesController;
use Controllers\PlanTypesController;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$router = new Router();

$router->get('/', [LoginController::class, 'Index']);
$router->post('/login/ajax', [LoginController::class, 'ajaxLogin']);

$router->get('cerrar-sesion', [LoginController::class, 'Logout']);
$router->get('proyectos', [ProjectsController::class, 'Proyectos']);
$router->get('proyectos/Anida', [ProjectsController::class, 'Anida']);
$router->get('proyectos/WE2T', [ProjectsController::class, 'WE2T']);
$router->get('proyectos/Rise', [ProjectsController::class, 'Rise']);
$router->get('proyectos/ayuda', [ProjectsController::class, 'ayuda']);
$router->post('proyectos/ayuda', [ProjectsController::class, 'submit']);

//Admin Panel Index
$router->get('admin', [AdminController::class, 'Index']);

//User CRUD MVC
$router->get('/admin/users', [UserController::class, 'index']);
$router->get('/admin/users/create', [UserController::class, 'create']);
$router->post('/admin/users/create', [UserController::class, 'create']);
$router->get('/admin/users/update', [UserController::class, 'update']);
$router->post('/admin/users/update', [UserController::class, 'update']);
$router->post('/admin/users/delete', [UserController::class, 'delete']);

//Projects CRUD MVC
$router->get('/admin/projects', [ProjectCRUDController::class, 'index']);
$router->get('/admin/projects/create', [ProjectCRUDController::class, 'create']);
$router->post('/admin/projects/create', [ProjectCRUDController::class, 'create']);
$router->get('/admin/projects/update', [ProjectCRUDController::class, 'update']);
$router->post('/admin/projects/update', [ProjectCRUDController::class, 'update']);
$router->post('/admin/projects/delete', [ProjectCRUDController::class, 'delete']);

//Departments CRUD MVC
$router->get('/admin/departments', [DepartmentCRUDController::class, 'index']);
$router->get('/admin/departments/create', [DepartmentCRUDController::class, 'create']);
$router->post('/admin/departments/create', [DepartmentCRUDController::class, 'create']);
$router->get('/admin/departments/update', [DepartmentCRUDController::class, 'update']);
$router->post('/admin/departments/update', [DepartmentCRUDController::class, 'update']);
$router->post('/admin/departments/delete', [DepartmentCRUDController::class, 'delete']);


//Customer CRUD MVC
$router->get('admin/customers', [CustomerCRUDController::class, 'Index']); // Show create form
$router->get('admin/customers/create', [CustomerCRUDController::class, 'create']); // Show create form
$router->post('admin/customers/create', [CustomerCRUDController::class, 'create']); // Handle creation
$router->get('admin/customers/getDepartments', [CustomerCRUDController::class, 'getDepartments']);
$router->get('/admin/customers/getTowers', [CustomerCRUDController::class, 'getTowers']);
$router->get('admin/customers/update', [CustomerCRUDController::class, 'update']); // Show update form
$router->post('admin/customers/update', [CustomerCRUDController::class, 'update']); // Handle update
$router->post('admin/customers/delete', [CustomerCRUDController::class, 'delete']); // Handle deletion

//Upload Files
$router->get('admin/files/upload', [FilesController::class, 'upload']); // Show update form
$router->post('admin/files/upload', [FilesController::class, 'store']); // Handle update
$router->get('admin/files/delete', [FilesController::class, 'delete']); // Show update form
$router->post('admin/files/delete', [FilesController::class, 'delete']); // Handle update

//Plan Types
$router->get('admin/files/plantypes', [PlanTypesController::class, 'index']);
$router->get('admin/files/plantypes/create', [PlanTypesController::class, 'create']);
$router->post('admin/files/plantypes/create', [PlanTypesController::class, 'create']);
$router->get('admin/files/plantypes/update/{id}', [PlanTypesController::class, 'update']);
$router->post('admin/files/plantypes/update/{id}', [PlanTypesController::class, 'update']);
$router->post('admin/files/plantypes/delete', [PlanTypesController::class, 'delete']);


//API routes
$router->get('/proyectos/api/getProjectsForUser/{userId}', [ProjectsController::class, 'getProjectsForUser']);
$router->get('/proyectos/api/getTowersForProject/{userId}/{projectId}', [ProjectsController::class, 'getTowersForProject']);
$router->get('/proyectos/api/getDepartmentsForTower/{userId}/{projectId}/{towerId}', [ProjectsController::class, 'getDepartmentsForTower']);

$router->comprobarRutas();

