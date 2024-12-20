<?php

namespace Controllers;

use MVC\Router;
use Model\Customer_record;

class AdminController {
    // Method to display customer records for all users
    public static function Index(Router $router) {
        
        // Debug the data before rendering
        /* echo "<pre>";
        print_r($customer_records);
        echo "</pre>"; */
        
        $router->render('admin/index', [
            'title' => 'Panel Administrativo'
        ]);
    }
}
