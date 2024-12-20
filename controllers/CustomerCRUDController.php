<?php

namespace Controllers;

use MVC\Router;
use Model\CustomerRecordCRUD;
use Model\User;
use Model\ProjectCRUD;
use Model\DepartmentCRUD;

class CustomerCRUDController {
    public static function index(Router $router) {
        $search = $_GET['search'] ?? '';
        $customer_projects = CustomerRecordCRUD::search($search);
        $success = $_GET['success'] ?? '';
    
        $router->render('admin/customers/index', [
            'customer_projects' => $customer_projects,
            'search' => htmlspecialchars($search),
            'success' => htmlspecialchars($success)
        ]);
    } 

    public static function create(Router $router) {
        $errors = [];
        $users = User::allDropdown();
        $projects = ProjectCRUD::allDropdown();
        $departments = [];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Fetch the posted values
            $project_id = $_POST['project_id'] ?? null;
            $department_id = $_POST['department_id'] ?? null;
            $user_id = $_POST['user_id'] ?? null;
    
            // Validate required fields
            if (!$user_id) {
                $errors[] = 'El Usuario es obligatorio.';
            }
            if (!$project_id) {
                $errors[] = 'El Proyecto es obligatorio.';
            }
            if (!$department_id) {
                $errors[] = 'El Departamento es obligatorio.';
            }
    
            // If validation passes, proceed with creating the customer project record
            if (empty($errors)) {
                // Fetch the project name based on the project ID
                $project_name = '';
                if ($project_id) {
                    $project = ProjectCRUD::find($project_id);
                    $project_name = $project->project_name ?? '';
                }
    
                $_POST['project_name'] = $project_name;
    
                // Create a new customer project record
                $customer_project = new CustomerRecordCRUD($_POST);
    
                // Log the values for debugging (optional)
                var_dump($customer_project->user_id, $customer_project->project_id, $customer_project->project_name, $customer_project->department_id);
    
                // If creation succeeds, redirect with a success message
                if ($customer_project->create()) {
                    header('Location: /admin/customers?success=Departamento asignado correctamente');
                    exit;
                } else {
                    // Add an error message if creation fails
                    $errors[] = 'Error al asignar el departamento del cliente';
                }
            }
        }
    
        // Render the create page with errors and the dropdown data
        $router->render('admin/customers/create', [
            'errors' => $errors,
            'users' => $users,
            'projects' => $projects,
            'departments' => $departments
        ]);
    }
    
    
    public static function update(Router $router) {
        $id = $_GET['id'] ?? null;
        $errors = $errors ?? [];
        $users = User::allDropdown();
        $projects = ProjectCRUD::allDropdown();
        $departments = [];

        // Fetch data for the customer project record
        $data = CustomerRecordCRUD::find($id);

        if ($data) {
            // Initialize the customer project with existing data
            $customer_project = new CustomerRecordCRUD($data);
            $customer_project->id = $id;

            // Set current department ID
            $currentDepartmentId = $customer_project->department_id;

            // Fetch the tower name related to the current department
            $currentDepartment = DepartmentCRUD::find($currentDepartmentId);
            $currentTowerName = $currentDepartment->project_tower ?? null; 

            // Check if there's a project_id to fetch related departments
            if ($customer_project->project_id) {
                // Get unassigned departments for the selected project
                $departments = CustomerRecordCRUD::getUnassignedDepartments($customer_project->project_id);

                // Ensure the assigned department is included in the list
                if ($currentDepartment && !in_array($currentDepartment, $departments)) {
                    $departments[] = $currentDepartment;
                }
            }

            // Check for POST request to handle the form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Synchronize the submitted data with the customer project object

                $project_id = $_POST['project_id'] ?? null;
                $department_id = $_POST['department_id'] ?? null;
                $user_id = $_POST['user_id'] ?? null;
        
                // Validate required fields
                if (!$user_id) {
                    $errors[] = 'El Usuario es obligatorio.';
                }
                if (!$project_id) {
                    $errors[] = 'El Proyecto es obligatorio.';
                }
                if (!$department_id) {
                    $errors[] = 'El Departamento es obligatorio.';
                }
                $customer_project->synchronize($_POST);

                if (empty($errors)) {
                    // Attempt to update the database with the new data
                    if ($customer_project->update()) {
                        header('Location: /admin/customers?success=Asignación actualizada');
                        exit;
                    } else {
                        $errors[] = 'Error al actualizar la asignación del departamento';
                    }
                }
            }

            // Render the update view with all required data, including tower name for display
            $router->render('admin/customers/update', [
                'errors' => $errors,
                'users' => $users,
                'projects' => $projects,
                'departments' => $departments,
                'customer_project' => $customer_project,
                'currentDepartmentId' => $currentDepartmentId, // Pass to view for preselection
                'currentTowerName' => $currentTowerName       // Pass tower name to view
            ]);

        } else {
            // Handle case where the project is not found
            $errors[] = 'Proyecto no encontrado';
            $router->render('admin/customers/update', [
                'errors' => $errors,
                'users' => $users,
                'projects' => $projects,
                'departments' => $departments
            ]);
        }
    }    

    public static function delete(Router $router) {
        $id = $_POST['id'] ?? null;
        error_log("POST data: " . print_r($_POST, true));

    
        if (!$id || !is_numeric($id)) {
            // Redirect or display an error message
            header('Location: /admin/customers?error=Invalid ID');
            exit;
        }
    
        if (CustomerRecordCRUD::delete($id)) {
            header('Location: /admin/customers?success=Asignación eliminada');
        } else {
            header('Location: /admin/customers?error=Failed to delete');
        }
        exit;
    }
    

    public function getDepartments() {
        header('Content-Type: application/json');
    
        $towerName = $_GET['tower_name'] ?? null;
    
        if ($towerName) {
            try {
                $departmentCRUD = new DepartmentCRUD();
                $departments = $departmentCRUD->getDepartmentsByTowerName($towerName);
    
                if ($departments) {
                    echo json_encode(['departments' => $departments]);
                } else {
                    echo json_encode(['departments' => [], 'error' => 'No se encontraron departamentos']);
                }
            } catch (Exception $e) {
                echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['departments' => [], 'error' => 'No hay torre seleccionada']);
        }
    }
    
    public function getTowers() {
        header('Content-Type: application/json');

        $projectId = $_GET['project_id'] ?? null;

        if ($projectId) {
            $departmentCRUD = new DepartmentCRUD();
            $towers = $departmentCRUD->getTowersByProjectId($projectId);
            echo json_encode(['towers' => $towers]);
        } else {
            echo json_encode(['towers' => []]);
        }
    }
}
