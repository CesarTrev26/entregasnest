<?php

namespace Controllers;

use MVC\Router;
use Model\ProjectCRUD;
use Model\Files;

class ProjectCRUDController {

    // Display all projects
    public static function index(Router $router) {
        $search = $_GET['search'] ?? '';
        $success = $_GET['success'] ?? ''; 
    
        if ($search) {
            $projects = ProjectCRUD::search($search);
        } else {
            $projects = ProjectCRUD::all();
        }
    
        // Pass the success message to the view
        $router->render('admin/projects/index', [
            'projects' => $projects,
            'search' => $search,
            'success' => $success
        ]);
    }

    // Create a new project
    public static function create(Router $router) {
        $errors = [];
        $project = new ProjectCRUD();
        $plans = Files::all(); // Fetch all plans/files
    
        // Check if the form has been submitted (POST request)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $project = new ProjectCRUD($_POST);
    
            // Validation logic
            if (empty($project->project_name)) {
                $errors[] = 'El nombre del proyecto es requerido.';
            }
    
            if (empty($project->location)) {
                $errors[] = 'La ubicación es requerida.';
            }
    
            // Handle selected plans/files
            $selectedPlans = isset($_POST['plans']) && is_array($_POST['plans']) 
                ? array_filter($_POST['plans'], fn($plan) => !empty($plan) && $plan != '0') 
                : [];
    
            // If there are no errors, attempt to create the project
            if (empty($errors)) {
                try {
                    if ($project->create()) {
                        // Save selected plans to the project_plan table if there are plans selected
                        if (!empty($selectedPlans)) {
                            ProjectCRUD::savePlansForProject($project->id, $selectedPlans); // Assuming savePlans handles the linking
                        }
    
                        header('Location: /admin/projects?success=Proyecto creado correctamente');
                        exit;
                    } else {
                        $errors[] = 'Error creando el proyecto.';
                    }
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }
    
        // Render the view, passing the project, plans, and errors
        $router->render('admin/projects/create', [
            'project' => $project,
            'errors' => $errors,
            'plans' => $plans // Include plans in the render
        ]);
    }
    
    public static function update(Router $router) {
        $id = $_GET['id'] ?? null; // Get the ID from the query parameters
        $errors = [];
    
        // Check if ID is valid
        if (!$id) {
            $errors[] = 'ID del proyecto no proporcionado.';
            $router->render('admin/projects/update', [
                'errors' => $errors,
                'project' => null,
                'plans' => Files::all(),
                'selectedPlans' => []
            ]);
            return;
        }
    
        // Fetch project data by ID
        $project = ProjectCRUD::find($id); // Now this returns a ProjectCRUD object
    
        if ($project) {
            // Fetch selected plans for the project
            $selectedPlansArray = ProjectCRUD::getPlansByProjectId($id);
            $selectedPlans = [];
            
            foreach ($selectedPlansArray as $plan) {
                $selectedPlans[$plan['file_id']] = $plan['file_name'];
            }
    
            // Check if the form has been submitted
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $project->synchronize($_POST); // This now works correctly
    
                // Validate required fields
                if (empty($project->project_name) || empty($project->location)) {
                    $errors[] = 'Todos los campos son requeridos.';
                } else {
                    // Attempt to update the project
                    if ($project->update()) {
                        // Handle selected plans
                        $plansToUpdate = isset($_POST['plans']) && is_array($_POST['plans']) 
                        ? array_filter($_POST['plans'], fn($plan) => is_numeric($plan) && !empty($plan))
                        : [];
                        ProjectCRUD::updatePlansForProject($id, $plansToUpdate);
    
                        header('Location: /admin/projects?success=Proyecto actualizado correctamente');
                        exit;
                    } else {
                        $errors[] = 'Error al actualizar el proyecto: ' . htmlspecialchars(self::$db->error);
                    }
                }
            }
    
            // Render the update form with the fetched data
            $router->render('admin/projects/update', [
                'project' => $project,
                'errors' => $errors,
                'plans' => Files::all(),
                'selectedPlans' => $selectedPlans
            ]);
        } else {
            // Handle project not found
            $errors[] = 'Proyecto no encontrado.';
            $router->render('admin/projects/update', [
                'errors' => $errors,
                'project' => null,
                'plans' => Files::all(),
                'selectedPlans' => []
            ]);
        }
    }
    

    // Delete a project
    public static function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $project = new ProjectCRUD(['id' => $id]);
            $result = $project->delete();

            if ($result) {
                header('Location: /admin/projects?success=Proyecto eliminado correctamente');
                exit;
            }
        }
    }
}
