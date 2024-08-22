<?php 

namespace Controllers;

use MVC\Router;
use Model\Projects;

class ProjectsController {
    public static function Proyectos(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        $userId = $_SESSION['id'];
        $projects_with_departments = Projects::fetchProjectsAndDepartments($userId);
    
        //var_dump($projects_with_departments);  // Check if data is fetched
    
        $router->render('proyectos', [
            'mensaje' => 'Desde la vista',
            'projects_with_departments' => $projects_with_departments
        ]);
    }

    public static function ProjectDetails(Router $router, $projectName) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $errors = [];
        $departments = [];

        // Check if project ID is provided in the URL
        if (isset($_GET['project_id'])) {
            $projectId = $_GET['project_id'];
            $userId = $_SESSION['id']; // Assuming user ID is stored in the session
            $auth = $_SESSION['login'];

            try {
                // Fetch User Data
                $userData = Projects::fetchUserData($userId);
                // Fetch departments for the specified project
                $departments = Projects::fetchUserDepartments($projectId, $userId);

                if (empty($departments)) {
                    $errors[] = 'No departments found for project ' . $projectName . '.';
                }
            } catch (\Exception $e) {
                $errors[] = 'An error occurred: ' . $e->getMessage();
            }
        }

        $router->render('proyectos/' . $projectName, [
            'departments' => $departments,
            'errores' => $errors,
            'userData' => $userData,
            'auth' => $auth
        ]);
    }

    public static function Anida(Router $router) {
        self::ProjectDetails($router, 'Anida');
    }

    public static function Rise(Router $router) {
        self::ProjectDetails($router, 'Rise');
    }

    public static function WE2T(Router $router) {
        self::ProjectDetails($router, 'WE2T');
    }
}
