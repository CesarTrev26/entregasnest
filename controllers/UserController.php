<?php

namespace Controllers;

use MVC\Router;
use Model\User;

class UserController {
    
    // Display all users
    public static function index(Router $router) {
        $search = $_GET['search'] ?? '';
        $success = $_GET['success'] ?? '';

        if ($search) {
            $users = User::search($search);
        } else {
            $users = User::all();
        }

        // Fetch roles for displaying in the view (if needed for dropdowns)
        $roles = User::getRoles();

        // Render the view and pass the users, search query, success message, and roles
        $router->render('admin/users/index', [
            'users' => $users,
            'search' => $search,
            'success' => $success,
            'roles' => $roles // Pass roles to the view
        ]);
    }


    // Create a new user
    public static function create(Router $router) {
        $errors = [];
        $roles = User::getRoles(); // Fetch roles for the dropdown

        $user = new User($_POST);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle role ID
            $user->rol_id = $_POST['rol_id'] ?? null;

            // Check for unique fields
        if (User::emailExists($user->email)) {
            $errors[] = 'El correo ya existe.';
        }
        
        if (User::phoneExists($user->phone)) {
            $errors[] = 'El teléfono ya existe.';
        }
        
        if (User::fullNameExists($user->full_name)) {
            $errors[] = 'El nombre ya existe.';
        }

        // Hash the password
        if (!empty($_POST['password'])) {
            $user->password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        } else {
            $errors[] = 'La contraseña es requerida.';
        }

        if (empty($errors)) {
            try {
                if ($user->create()) {
                    header('Location: /admin/users?success=Usuario creado correctamente');
                    exit;
                } else {
                    $errors = User::getErrors();
                }
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
            // Ensure roles are passed to the view
            $router->render('admin/users/create', [
                'errors' => $errors,
                'roles' => $roles // Pass roles to the view
            ]);
        }  else {
            $router->render('admin/users/create', [
                'errors' => $errors,
                'roles' => $roles // Pass roles to the view
            ]);
        }
    }

    public static function update(Router $router) {
        $errors = [];
        $roles = User::getRoles(); // Fetch roles for the dropdown
    
        $user = null;
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $full_name = $_POST['full_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $password = $_POST['password'] ?? '';
            $rol_id = $_POST['rol_id'] ?? null; // Capture role ID from form
    
            if (!$id || empty($full_name) || empty($email) || empty($phone)) {
                $errors[] = "Todos los campos son requeridos.";
            } else {
                $user = User::find($id);
                if ($user) {
                    $user->full_name = $full_name;
                    $user->email = $email;
                    $user->phone = $phone;
                    $user->rol_id = $rol_id; // Update role ID
    
                    if (!empty($password)) {
                        $user->password_hash = password_hash($password, PASSWORD_BCRYPT);
                    }
    
                    error_log("Before update call");
                    try {
                        if ($user->update()) {
                            error_log("Update successful, redirecting...");
                            header('Location: /admin/users?success=Usuario editado correctamente');
                            exit; // Ensure no further code is executed after redirect
                        } else {
                            error_log("Update failed");
                            $errors[] = "Error editando el usuario.";
                        }
                    } catch (Exception $e) {
                        error_log("Exception: " . $e->getMessage());
                        $errors[] = $e->getMessage(); // Display exception message
                    }
                } else {
                    $errors[] = "Usuario no encontrado.";
                }
            }
        } else {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $user = User::find($id);
                if (!$user) {
                    $errors[] = "Usuario no encontrado.";
                }
            } else {
                $errors[] = "ID de usuario requerido.";
            }
        }
    
        // Render the view and pass the user, errors, and roles
        $router->render('admin/users/update', [
            'user' => $user,
            'errors' => $errors,
            'roles' => $roles // Pass roles to the view
        ]);
    }
    
    
    // Delete a user
    public static function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $user = User::find($id);
                if ($user && $user->delete()) {
                    header('Location: /admin/users?success=Usuario eliminado correctamente');
                    exit;
                } else {
                    header('Location: /admin/users?error=El usuario no se encontró o no se logró eliminar');
                    exit;
                }
            } else {
                header('Location: /admin/users?error=ID de usuario requerido');
                exit;
            }
        }
    }
}
