<?php

namespace Controllers;

use MVC\Router;
use Model\DepartmentCRUD;
use Model\ProjectCRUD;
use Model\Files;

class DepartmentCRUDController {

    // Display all departments
    public static function index(Router $router) {
        $search = $_GET['search'] ?? '';  // Get search term if provided
        $departments = [];
        $success = $_GET['success'] ?? '';
    
        if ($search) {
            $departments = DepartmentCRUD::search($search);  // Call the search method
        } else {
            $departments = DepartmentCRUD::all();  // Fetch all departments if no search
        }
    
        $router->render('admin/departments/index', [
            'departments' => $departments,
            'search' => $search,  // Pass search term back to the view for display
            'success' => htmlspecialchars($success)
        ]);
    }
    

    public function create(Router $router) {
        $errors = [];
        $success = '';
        $data = [];
        
        // Fetch projects and plans for dropdowns
        $projects = ProjectCRUD::all();
        $plans = Files::all(); // Assume Files model fetches files correctly
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
    
            // Basic validation for required fields
            foreach (['project_id', 'project_tower', 'department_name', 'department_basement'] as $field) {
                if (empty($data[$field])) {
                    $errors[] = "El campo $field es obligatorio.";
                }
            }
    
            // Verify selected project exists
            if (!empty($data['project_id'])) {
                $project = ProjectCRUD::find($data['project_id']);
                if ($project) {
                    // Updated to use object notation
                    $data['project_name'] = $project->project_name;
                } else {
                    $errors[] = "El proyecto seleccionado no existe.";
                }
            }
    
            $selectedPlans = isset($data['plans']) && is_array($data['plans']) 
                ? array_filter($data['plans'], fn($plan) => !empty($plan) && $plan != '0') 
                : [];
    
            if (empty($errors)) {
                // Create a new department
                $department = new DepartmentCRUD($data);
                $departmentId = $department->create();
                
                if ($departmentId) {
                    // Save selected plans to the department_plan table if there are plans selected
                    if (!empty($selectedPlans)) {
                        DepartmentCRUD::savePlans($departmentId, $selectedPlans);
                    }
                    
                    $success = 'Departamento creado con éxito.';
                    header('Location: /admin/departments?success=Departamento creado correctamente');
                    exit;
                    
                } else {
                    $errors[] = 'Error al crear el departamento.';
                }
            }
        }
    
        // Render the view with data
        $router->render('admin/departments/create', [
            'projects' => $projects,
            'plans' => $plans,
            'errors' => $errors,
            'success' => $success,
            'department' => $data
        ]);
    }    
    
    
    public static function update(Router $router) {
        $id = $_GET['id'] ?? null;
        $errors = [];
        
        // Check if ID is valid
        if (!$id) {
            $errors[] = 'ID del departamento no proporcionado.';
            $router->render('admin/departments/update', [
                'errors' => $errors,
                'department' => null,
                'projects' => ProjectCRUD::allDropdown(),
                'plans' => Files::all(),
                'selectedPlans' => []
            ]);
            return;
        }
    
        // Fetch department data by ID
        $department = DepartmentCRUD::find($id);
        
        if ($department) {
            // Fetch selected plans for the department
            $selectedPlansArray = DepartmentCRUD::getPlansByDepartmentId($id);

            $selectedPlans = [];
            foreach ($selectedPlansArray as $plan) {
                $selectedPlans[$plan['file_id']] = $plan['file_name']; // Store file_id as key and file_name as value (or just the ID)
            }
    
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Basic validation on $_POST data could be added here
                $department->synchronize($_POST);
    
                // Attempt to update the department
                if ($department->update()) {
                    // Handle selected plans
                    $plansToUpdate = isset($_POST['plans']) && is_array($_POST['plans']) 
                        ? array_filter($_POST['plans'], fn($plan) => is_numeric($plan) && !empty($plan))
                        : [];
                    DepartmentCRUD::updatePlansForDepartment($id, $plansToUpdate);
    
                    header('Location: /admin/departments?success=Departamento actualizado');
                    exit; // Make sure to exit after redirection
                } else {
                    $errors[] = 'Error al actualizar el departamento: ' . htmlspecialchars(self::$db->error);
                }
            }
    
            // Render the update form with the fetched data
            $router->render('admin/departments/update', [
                'department' => $department,
                'errors' => $errors,
                'projects' => ProjectCRUD::allDropdown(),
                'plans' => Files::all(),
                'selectedPlans' => $selectedPlans
            ]);
        } else {
            // Handle department not found
            $errors[] = 'Departamento no encontrado';
            $router->render('admin/departments/update', [
                'errors' => $errors,
                'department' => null,
                'projects' => ProjectCRUD::allDropdown(),
                'plans' => Files::all(),
                'selectedPlans' => []
            ]);
        }
    }
    
    // Delete a department
    public static function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $department = new DepartmentCRUD(['id' => $id]);
            $result = $department->delete();

            if ($result) {
                header('Location: /admin/departments?success=Departamento eliminado correctamente');
                exit;
            }
        }
    }
}
