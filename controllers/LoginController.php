<?php 

namespace Controllers;

use MVC\Router;
use Model\Admin;

class LoginController {
    public static function Index(Router $router) {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ajaxLogin();
        }

        $router->render('login', [
            'errores' => $errors
        ]);
    }

    public function ajaxLogin(Router $router = null) {
        $errors = [];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Admin($_POST);
    
            // Use error_log for debugging without interrupting JSON response
            //error_log(print_r($auth, true)); // Logs object details to PHP error log
    
            $errors = $auth->validate();
    
            if (empty($errors)) {
                $user = $auth->userExists();
    
                if (!$user) {
                    $errors = Admin::getErrors();
                } else {
                    $verified = $auth->checkPassword($user);
    
                    if ($verified) {
                        $auth->authenticate($user);
                        echo json_encode(['success' => true, 'redirectURL' => '$redirectURL']);
                    } else {
                        $errors[] = 'Contraseña incorrecta';
                    }
                }
            }
        }
    
        // Send JSON response for errors
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
        }
        exit;
    }    
    
    public static function Logout(Router $router) {
        session_start();
        $_SESSION = [];
        session_destroy();
        header('Location: /');
        exit; // Ensure no further code is executed after redirect
    }
}
