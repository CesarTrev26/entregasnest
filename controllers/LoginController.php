<?php 

namespace Controllers;
use MVC\Router;
use Model\Admin;


class LoginController {
    public static function Index(Router $router) {

        $errors = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $auth = new Admin($_POST);

            $errors = $auth->validate();

            if(empty($errors)) {
                //Verify if the user exists
                $result = $auth->userExists();

                if(!$result) {
                    $errors = Admin::getErrors();
                } else {
                    
                    //Verify the password
                    $verified = $auth->checkPassword($result);
                    //debug($verified);

                    if($verified) {
                        //Authenticate the user
                        $auth->authenticate();
                    } else {
                        // Incorrect password(Error message)
                        $errors = Admin::getErrors();
                    }
                }

                
            }
        }

        $router->render('login', [
            'errores' => $errors
        ]);
    }
    public static function Logout(Router $router) {
        session_start();

        $_SESSION = [];

        header('Location: /');
    }
} 