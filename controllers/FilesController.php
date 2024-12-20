<?php

namespace Controllers;

use MVC\Router;
use Model\Files;
use Model\PlanTypesCRUD;

class FilesController {

    // Method to display the upload form
    public static function upload(Router $router, $errors = [], $success = null) {
        // Render the upload form with potential error/success messages
        $router->render('admin/files/upload', [
            'errors' => $errors,
            'success' => $success
        ]);
    }

    public function store(Router $router) {
        $errors = [];
        $responses = [];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if 'files' key exists in $_FILES
            if (isset($_FILES['files']) && is_array($_FILES['files']['name'])) {
                foreach ($_FILES['files']['name'] as $index => $originalName) {
                    $tmpName = $_FILES['files']['tmp_name'][$index];
                    $error = $_FILES['files']['error'][$index];
    
                    // Determine plan type based on file name
                    $planType = $this->determinePlanType($originalName);
                    if (!$planType) {
                        $errors[] = "No se pudo asignar el tipo de plan para '$originalName'.";
                        continue;
                    }
    
                    // Define the upload directory and file path
                    $uploadDirectory = 'build/uploaded/';
                    $newFilename = basename($originalName); // Keep the original name with versioning
                    $targetPath = $uploadDirectory . $newFilename;
    
                    // Check for upload errors
                    if ($error === UPLOAD_ERR_OK) {
                        // Check if the file exists on the server
                        if (file_exists($targetPath)) {
                            // If the file exists on the server, check if it exists in the database
                            if (Files::fileExists($newFilename)) {
                                // The file exists in the database and on the server
                                $responses[] = ["info" => "El archivo '$originalName' ya existe en la base de datos y en el servidor."];
                            } else {
                                // The file exists on the server but not in the database, add it to the database
                                $fileRecord = new Files([
                                    'plan_type' => $planType,
                                    'file_path' => "/" . $targetPath,
                                    'file_name' => $newFilename,
                                ]);
                                $fileRecord->save();
                                
                                $responses[] = ["success" => "El archivo '$originalName' ya estaba en el servidor y se agregó a la base de datos."];
                            }
                        } else {
                            // The file does not exist on the server, check if it's in the database
                            if (!Files::fileExists($newFilename)) {
                                // If the file doesn't exist in the database either, upload it
                                if (move_uploaded_file($tmpName, $targetPath)) {
                                    // Save file details in the database
                                    $fileRecord = new Files([
                                        'plan_type' => $planType,
                                        'file_path' => "/" . $targetPath,
                                        'file_name' => $newFilename,
                                    ]);
                                    $fileRecord->save();
    
                                    $responses[] = ["success" => "El archivo '$originalName' se subió correctamente y se agregó a la base de datos."];
                                } else {
                                    $errors[] = "El archivo '$originalName' no se pudo mover.";
                                }
                            } else {
                                // The file exists in the database but not on the server, handle re-upload
                                $responses[] = ["info" => "El archivo '$originalName' ya existe en la base de datos, pero no en el servidor."];
                                // Re-upload the file from the temporary location
                                if (move_uploaded_file($tmpName, $targetPath)) {
                                    $responses[] = ["success" => "El archivo '$originalName' se re-subió correctamente al servidor."];
                                } else {
                                    $errors[] = "No se pudo re-subir el archivo '$originalName' al servidor.";
                                }
                            }
                        }
                    } else {
                        $errors[] = "Error al cargar el archivo '$originalName'.";
                    }
                }
            } else {
                $errors[] = "No se seleccionaron archivos para cargar.";
            }
        }
    
        // Return JSON response with success or error messages for each file
        header('Content-Type: application/json');
        echo json_encode(["responses" => $responses, "errors" => $errors]);
    }
      
    
    public function delete(Router $router) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fileIds = $_POST['files'] ?? [];
            $errors = [];
            $success = '';
    
            $filesModel = new Files();
    
            // Debug log the received file IDs
            error_log("Files to delete: " . print_r($fileIds, true));
    
            foreach ($fileIds as $id) {
                try {
                    // Fetch the file details
                    $fileDetails = $filesModel->find($id);
    
                    if ($fileDetails) {
                        // Delete from the database
                        if ($filesModel->deleteFile($id)) {
                            // Delete the file from the filesystem
                            $filePath = $fileDetails['file_path']; // Assume it's a relative path
                            $filesModel->deleteFileFromFilesystem($filePath);
                            $success .= "Archivo '{$fileDetails['file_name']}' eliminado correctamente.<br>";
                        }
                    } else {
                        $errors[] = "El archivo con el ID $id no existe.";
                    }
                } catch (Exception $e) {
                    error_log("Error eliminando los archivos: " . $e->getMessage());
                    $errors[] = "Error eliminando el archivo con el ID $id: " . $e->getMessage();
                }
            }
    
            // Render the view again with messages
            $router->render('admin/files/delete', [
                'files' => Files::all(),
                'errors' => $errors,
                'success' => $success
            ]);
        } else {
            // Render the delete view if not a POST request
            $router->render('admin/files/delete', [
                'files' => Files::all(),
                'errors' => [],
                'success' => ''
            ]);
        }
    }
    
    private function determinePlanType($filename) {
        // Fetch plan types from the database
        $planTypesData = PlanTypesCRUD::all(); // Returns an array of PlanTypesCRUD objects
        $planTypes = [];
        
        // Transform the fetched objects into a key-value pair array
        foreach ($planTypesData as $planTypeObject) {
            $keyword = strtolower(trim($planTypeObject->keyword));
            $description = strtolower(trim($planTypeObject->description));
            $planTypes[$keyword] = $description;
        }
        
        // Sort plan types by the length of the keyword in descending order
        uksort($planTypes, function ($a, $b) {
            return strlen($b) - strlen($a);
        });
        
        // Normalize the filename (remove unwanted characters, spaces)
        $normalizedFilename = strtolower(trim($filename));
        // Remove extra characters like dashes, underscores, or spaces that could be present around keywords
        $normalizedFilename = preg_replace('/[\W_]+/', ' ', $normalizedFilename);
        
        // Determine the plan type
        foreach ($planTypes as $keyword => $planType) {
            // Use regular expression to check if the keyword exists in the filename with optional spaces or extra characters
            $pattern = '/\b' . preg_quote($keyword, '/') . '\b/i'; // \b for word boundaries, i for case-insensitive
            if (preg_match($pattern, $normalizedFilename)) {
                return ucfirst($planType); // Return the matched plan type
            }
        }
        
        return 'Desconocido'; // Return 'Desconocido' if no plan type matched
    }
    
}


