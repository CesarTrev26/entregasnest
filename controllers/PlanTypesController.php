<?php 

namespace Controllers;

use MVC\Router;
use Model\PlanTypesCRUD;

class PlanTypesController {
    
    public static function index(Router $router) {
        $errors = [];

        if (isset($_GET['errors'])) {
            $queryErrors = $_GET['errors'];
            
            // Decode if it's a serialized array (like JSON or http_build_query)
            if (is_string($queryErrors)) {
                $queryErrors = json_decode($queryErrors, true) ?? [$queryErrors];
            }
            
            // Ensure it's an array and merge with $errors
            $errors = array_merge($errors, (array) $queryErrors);
        }    
        
        $success = $_GET['success'] ?? '';
        // Fetch all plan types from the database using the model
        $planTypes = PlanTypesCRUD::all();

        // Pass the data to the view
        $router->render('admin/files/types/index', [
            'planTypes' => $planTypes,
            'errors' => $errors,
            'success' => htmlspecialchars($success)
        ]);
    }

    public static function create(Router $router) {
        $errors = [];
        $planType = new PlanTypesCRUD();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $planType->synchronize($_POST); // Populate the object with form data
            $errors = $planType->validate();

            if (empty($errors)) {
                $result = $planType->create();
                if ($result) {
                    header('Location: /admin/files/plantypes?success=Tipo de plano agregado correctamente'); // Redirect after successful creation
                }
            }
        }

        $router->render('admin/files/types/create', [
            'errors' => $errors,
            'planType' => $planType
        ]);
    }

    public static function update(Router $router, $id) {
        if (!$id) {
            header('Location: /admin/files/plantypes?errors=Id de tipo de plano no encontrado');
            exit;
        }
    
        // Fetch the plan type
        $planType = PlanTypesCRUD::find($id);
        $planType = (object) $planType;
        if (!$planType) {
            header('Location: /admin/files/plantypes?errors=No hay tipos de planos');
            exit;
        }
    
        $errors = [];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Synchronize data from the form
            $planType->synchronize($_POST);
            $errors = $planType->validateUpdate();
    
            if (empty($errors)) {
                if ($planType->update($planType->id, $planType->keyword, $planType->description, $planType->assigned_button)) {
                    header('Location: /admin/files/plantypes?success=Tipo de plano actualizado correctamente');
                    exit;
                }
            }
        }
    
        // Pass the planType object to the view
        $router->render('admin/files/types/update', [
            'planType' => $planType,
            'errors' => $errors,
        ]);
    }    

    public static function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
    
            if (!$id) {
                $errors = ['No se proporcionó un ID'];
                $query = http_build_query(['errors' => $errors]);
                header('Location: /admin/files/plantypes?' . $query);
                exit;
            }
    
            $planType = PlanTypesCRUD::find($id);
            if (!$planType) {
                $errors = ['No se encontró el tipo de plano'];
                $query = http_build_query(['errors' => $errors]);
                header('Location: /admin/files/plantypes?' . $query);
                exit;
            }
    
            // Proceed with deletion
            if (PlanTypesCRUD::delete($id)) {
                header('Location: /admin/files/plantypes?success=Tipo de plano eliminado correctamente');
                exit;
            } else {
                $errors = ['Error al borrar el tipo de plano'];
                $query = http_build_query(['errors' => $errors]);
                header('Location: /admin/files/plantypes?' . $query);
                exit;
            }
        }
    }        
}
