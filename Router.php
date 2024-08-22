<?php

namespace MVC;

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
        session_start();

        $auth = $_SESSION['login'] ?? null;
        //debug($auth);

        // Private routes 
        $protected_routes = ['proyectos'];


        // Get the request URI and remove the query string
        $urlActual = $_SERVER['REQUEST_URI'] ?? '/';
        $urlActual = explode('?', $urlActual)[0];
        
        // Trim leading and trailing slashes
        $urlActual = trim($urlActual, '/');
        //echo "$urlActual";

        // Normalize the root URL
        /*if ($urlActual === '') {
            $urlActual = '/';
        }*/

        // Log the current URL and method for debugging
        $metodo = $_SERVER['REQUEST_METHOD'];
        error_log("Parsed URI: '$urlActual'");
        error_log("Request Method: '$metodo'");

        // Protect the routes

        if($urlActual !== '' && !$auth) {
            header('Location: /');
        }

        // Determine which routes to check based on the request method
        if ($metodo === 'GET') {
            $fn = $this->rutasGET[$urlActual] ?? null;
        } else {
            $fn = $this->rutasPOST[$urlActual] ?? null;
        }

        if ($fn) {
            error_log("Route found: " . print_r($fn, true));
            if (is_callable($fn)) {
                call_user_func($fn, $this);
            } elseif (is_array($fn)) {
                $controller = new $fn[0];
                $method = $fn[1];
                $controller->$method();
            }
        } else {
            //echo "Ruta no encontrada: '$urlActual'";
            echo "Página No Encontrada";
        }
    }

    //Show views
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
