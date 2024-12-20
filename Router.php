<?php

namespace MVC;

use Controllers\LoginController;
use Controllers\ProjectsController;
use Controllers\FilesController;

class Router {
    public $rutasGET = [];
    public $rutasPOST = [];

    public function get($url, $fn) {
        $this->rutasGET[trim($url, '/')] = $fn;
    }

    public function post($url, $fn) {
        $this->rutasPOST[trim($url, '/')] = $fn;
    }

    public function comprobarRutas() {
        $auth = $_SESSION['login'] ?? null;
        $rol = $_SESSION['rol'] ?? null;
    
        // Get the request URI and remove the query string
        $urlActual = $_SERVER['REQUEST_URI'] ?? '/';
        $urlActual = explode('?', $urlActual)[0];
        $urlActual = trim($urlActual, '/');
        
        $metodo = $_SERVER['REQUEST_METHOD'];
        error_log("Parsed URI: '$urlActual'");
        error_log("Request Method: '$metodo'");
    
        // Handle logout route
        if ($urlActual === 'cerrar-sesion') {
            $this->handleLogout();
            return; // Ensure no further routing occurs after logout
        }
    
        // Allow AJAX login to bypass authentication check
        if ($urlActual === 'login/ajax') {
            // Directly call the AJAX login method and return JSON without redirection
            $loginController = new LoginController();
            $loginController->ajaxLogin();
        }

        if ($urlActual === 'proyectos/ayuda') {
            // Directly call the AJAX login method and return JSON without redirection
            if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
                $ProjectsController = new ProjectsController();
                $ProjectsController->submit();
            }
        }
    
        // If not authenticated, redirect to login page
        if ($urlActual !== '' && !$auth) {
            header('Location: /');
            exit;
        }

        // Redirect admins to /admin if not already there
        if ($auth && $rol === 'Admin' && !preg_match('/^admin/', $urlActual)) {
            header('Location: /admin');
            exit;
        }

        // Redirect regular users to /proyectos if not already there
        if ($auth && $rol !== 'Admin' && !preg_match('/^proyectos/', $urlActual)) {
            header('Location: /proyectos');
            exit;
        }
    
        // Determine the route to handle based on the request method
        //$fn = $metodo === 'GET' ? ($this->rutasGET[$urlActual] ?? null) : ($this->rutasPOST[$urlActual] ?? null);
    
        // 1. Check Static Routes First
        if (isset($this->rutasGET[$urlActual]) || isset($this->rutasPOST[$urlActual])) {
            $fn = $metodo === 'GET' ? ($this->rutasGET[$urlActual] ?? null) : ($this->rutasPOST[$urlActual] ?? null);

            if ($fn) {
                if (is_callable($fn)) {
                    call_user_func($fn, $this); // Handle static route handler
                } elseif (is_array($fn)) {
                    $controller = new $fn[0];  // Instantiate the controller
                    $method = $fn[1];         // Get the method name
                    $controller->$method($this); // Call the method of the controller
                }
            } else {
                echo "Página No Encontrada";
            }
            return; // Exit after handling static route
        }

        // 2. Handle Dynamic Routes
        foreach ($this->rutasGET as $route => $handler) {
            $pattern = preg_replace('/{(\w+)}/', '([^/]+)', $route); 
            if (preg_match("#^$pattern$#", $urlActual, $matches)) {
                array_shift($matches); // Remove full match
                $params = array_values($matches); 
                $params = array_map('urldecode', $params); // Decode URL parameters
        
                if (is_callable($handler)) {
                    // Directly call the handler with extracted parameters
                    call_user_func_array($handler, $params);
                } elseif (is_array($handler)) {
                    // Create the controller and call the method
                    $controller = new $handler[0];
                    $method = $handler[1];
                    call_user_func_array([$controller, $method], $params);
                }
                return; 
            }
        }
              

        // 3. Handle other cases (e.g., route not found)
        echo "Página No Encontrada";
    }
    

    // Handle logout logic
    private function handleLogout() {
        session_start();
        $_SESSION = [];
        session_destroy();
        header('Location: /');
        exit;
    }

    // Show views
    public function render($view, $datos = []) {
        foreach($datos as $key => $value ) {
            $$key = $value;
        }

        ob_start();
        include __DIR__ . "/views/$view.php";
        
        $contenido = ob_get_clean();

        include __DIR__ . "/views/layout.php";
    }
}
