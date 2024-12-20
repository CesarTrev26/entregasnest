<?php 

namespace Controllers;

use MVC\Router;
use Model\Projects;
use SendGrid;
use SendGrid\Mail\Mail;
use SendGrid\Mail\Attachment;

class ProjectsController {
    // In ProjectsController or relevant script
    public static function Proyectos(Router $router) {
        $errors[] = '';
        $userId = $_SESSION['id'];
        $auth = $_SESSION['login'];

        // Check if session variables are properly set
        if (!isset($_SESSION['id'])) {
            echo "User ID is not set in session.";
            return; // Exit if session is not properly set
        }
    

    
        if (empty($auth)) {
            echo "Authentication status is not set in session.";
            exit; // Stop execution if the authentication status is not set
        } 

        try {
            $userData = Projects::fetchUserData($userId);
        } catch (\Exception $e) {
            $errors[] = 'Ha ocurrido un error: ' . $e->getMessage();
        }

        $projects_with_departments = Projects::fetchProjectsAndDepartments($userId);
    
        $router->render('proyectos', [
            'mensaje' => 'Desde la vista',
            'errores' => $errors,
            'userData' => $userData,
            'auth' => $auth,
            'projects_with_departments' => $projects_with_departments
        ]);
    }
    

    
    public static function ProjectDetails(Router $router, $projectName) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $errors[] = '';
        $userId = $_SESSION['id'];
        $auth = $_SESSION['login'];
    
        // Debugging
        if (empty($userId)) {
            echo "User ID is not set in session.";
            exit; // Stop execution if the session is not set
        }
    
        if (empty($auth)) {
            echo "Authentication status is not set in session.";
            exit; // Stop execution if the authentication status is not set
        }
    
        if (isset($_GET['project_id'])) {
            $projectId = $_GET['project_id'];
    
            try {
                $userData = Projects::fetchUserData($userId);
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

    public static function Ayuda(Router $router) {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            submit();
        }

        $router->render('proyectos/ayuda', [
            'errores' => $errors
        ]);
    }

    public function getProjectsForUser($userId) {
        header('Content-Type: application/json');

        // Fetch projects and departments for the user
        $projects = Projects::fetchProjects($userId);

        // Return the result as a JSON response
        if ($projects) {
            echo json_encode(['projects' => $projects]);
        } else {
            echo json_encode(['projects' => []]);
        }
    }
    
    public function getTowersForProject($userId, $projectId) {
        header('Content-Type: application/json');
    
        // Fetch towers with assigned departments for the user and project
        $towers = Projects::fetchUserTowers($projectId, $userId);
    
        // Debugging: Check what data is being returned
        error_log("Towers data: " . print_r($towers, true));  // Log towers data
    
        if ($towers) {
            echo json_encode(['towers' => $towers]);
        } else {
            echo json_encode(['towers' => []]);
        }
    }

    public function getDepartmentsForTower($userId, $projectId, $towerId) {
        // Log the parameters to check if they are being received correctly
        error_log("Received parameters - User ID: $userId, Project ID: $projectId, Tower ID: $towerId");
        
        // Validate the inputs
        if (empty($userId) || empty($projectId) || empty($towerId)) {
            http_response_code(400);
            echo json_encode(['error' => 'All parameters are required']);
            return;
        }
        
        // Call the model to get the departments
        $departments = Projects::getDepartmentsForTower($userId, $projectId, $towerId);
        
        // Check if departments were found
        if ($departments) {
            http_response_code(200);
            echo json_encode(['departments' => $departments]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'No departments found']);
        }
    }      
    
    public static function submit(Router $router = null) {
        $errors = [];
    
        // Create a new Projects instance and populate it with form data
        $project = new \Model\Projects([
            'name' => $_POST['Name'],
            'email' => $_POST['Email'],
            'department' => $_POST['Department'],
            'tower' => $_POST['Tower'],
            'project' => $_POST['Project'],
            'file' => !empty($_FILES['file']['name']) ? $_FILES['file']['name'] : [], // assuming files is an array
            'message' => $_POST['Message']
        ]);
    
        // Validate using the model's validate method
        $validationErrors = $project->validate();
        if (!empty($validationErrors)) {
            $errors = array_merge($errors, $validationErrors);
        }
    
        // Prepare files for attachment without saving to disk
        $uploadedFiles = [];
        if (!empty($_FILES['file']['name'])) {
            $fileNames = is_array($_FILES['file']['name']) ? $_FILES['file']['name'] : [$_FILES['file']['name']];
            $tmpNames = is_array($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : [$_FILES['file']['tmp_name']];
            
            foreach ($tmpNames as $index => $tmpName) {
                $filename = basename($fileNames[$index]);
                $fileData = file_get_contents($tmpName); // Read file data directly
                $encodedData = base64_encode($fileData); // Encode for email attachment
    
                // Store file data in an array for email attachments
                $uploadedFiles[] = [
                    'data' => $encodedData,
                    'type' => mime_content_type($tmpName), // Get the MIME type of the file
                    'name' => $filename,
                ];
            }
        }
    
        header('Content-Type: application/json');
    
        // If no errors, send the email
        if (empty($errors)) {
            $email = new Mail();
            $email->setFrom("cesar.trevino@nest.com.mx", "Ayuda");
            $email->setSubject($_POST['projectName'] . " - Formulario de Ayuda");

            $recipients = [
                "cesar.trevino@nest.com.mx",
                "garantias@nest.com.mx"
            ];
        
            foreach ($recipients as $recipient) {
                $email->addTo($recipient);
            }
    
            // Email content
            $content = "
                <h2>{$_POST['projectName']} - Reporte de Ayuda</h2>
                <p><strong>Nombre:</strong> {$_POST['Name']}</p>
                <p><strong>Email:</strong> {$_POST['Email']}</p>
                <p><strong>Departamento:</strong> {$_POST['departmentName']}</p>
                <p><strong>Torre:</strong> {$_POST['Tower']}</p>
                <p><strong>Proyecto:</strong> {$_POST['projectName']}</p>
                <p><strong>Mensaje:</strong> {$_POST['Message']}</p>
                <p>
                    <a href='https://www.nest.com.mx/' target='_blank' style='width:100%;'>
                        <img src='https://nestspace.mx/build/img/Firma-Nest.webp' alt='Contact Image' style='width:100%; height:auto; border:0;'>
                    </a>
                </p>
            ";
            $email->addContent("text/html", $content);
    
            // Attach files directly
            foreach ($uploadedFiles as $file) {
                $email->addAttachment(
                    new Attachment(
                        $file['data'],
                        $file['type'],
                        $file['name'],
                        "attachment"
                    )
                );
            }
    
            // Send email
            $sendgrid = new SendGrid($_ENV['SENDGRID_API_KEY']);
            try {
                $response = $sendgrid->send($email);
                if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
                    // Send confirmation email to the user
                    $confirmationEmail = new Mail();
                    $confirmationEmail->setFrom("cesar.trevino@nest.com.mx", "Ayuda");
                    $confirmationEmail->setSubject("Confirmación de Recepción de Formulario");
                    $confirmationEmail->addTo($_POST['Email'], $_POST['Name']);
                    $confirmationContent = "
                        <h2>Estimado/a " . strtoupper($_POST['Name']) . ":</h2>
                        <p>Confirmamos recibida su solicitud correctamente.</p>
                        <p>En un plazo de 48 horas nuestro equipo de Garantías se pondrá en contacto con usted para proporcionarle más información.</p>
                        <br>
                        <p>Le compartimos la información que se nos proporcionó:</p>
                        <p><strong>Nombre:</strong> " . strtoupper($_POST['Name']) . "</p>
                        <p><strong>Email:</strong> {$_POST['Email']}</p>
                        <p><strong>Departamento:</strong> {$_POST['departmentName']}</p>
                        <p><strong>Torre:</strong> {$_POST['Tower']}</p>
                        <p><strong>Proyecto:</strong> {$_POST['projectName']}</p>
                        <p><strong>Mensaje:</strong> {$_POST['Message']}</p>
                        <p>Agradecemos su paciencia y quedamos a su disposición.</p>

                        <p>
                            <a href='https://www.nest.com.mx/' target='_blank' style='width:100%;'>
                                <img src='https://nestspace.mx/build/img/Firma-Nest.webp' alt='Contact Image' style='width:100%; height:auto; border:0;'>
                            </a>
                        </p>
                    ";
                    $confirmationEmail->addContent("text/html", $confirmationContent);
                    $sendgrid->send($confirmationEmail);
    
                    echo json_encode(['success' => true]);
                } else {
                    $errors[] = "Error al enviar el email: " . $response->body();
                    echo json_encode(['success' => false, 'errors' => $errors]);
                }
            } catch (Exception $e) {
                $errors[] = "Error al enviar el email: {$e->getMessage()}";
                echo json_encode(['success' => false, 'errors' => $errors]);
            }
        } else {
            echo json_encode(['success' => false, 'errors' => $errors]);
        }
        exit;
    }    
}
