<?php

namespace Model;

class Admin extends ActiveRecord {
    protected static $table = 'users';
    protected static $columnsDB = ['id', 'full_name', 'rol_id', 'email', 'password_hash', 'phone'];

    public $id;
    public $fullname;
    public $rol_id; // Role ID from the users table
    public $email;
    public $password;
    public $phone;
    
    public $rol; // Role name from the rol table

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->fullname = $args['full_name'] ?? '';
        $this->rol_id = $args['rol_id'] ?? ''; // Ensure rol_id is properly assigned
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->phone = $args['phone'] ?? '';
        $this->rol = $args['rol'] ?? ''; // Ensure rol is properly assigned
    }    

    public function validate() {
        if (!$this->email) {
            self::$errors[] = 'El email es obligatorio';
        }
        if (!$this->password) {
            self::$errors[] = 'La contraseña es obligatoria';
        }

        return self::$errors;
    }

    public function userExists() {
        $query = "
            SELECT users.*, rol.rol AS rol 
            FROM " . self::$table . " 
            LEFT JOIN rol ON users.rol_id = rol.id
            WHERE email = ? LIMIT 1";
        
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            self::$errors[] = 'Error en la consulta';
            return false;
        }
    
        if ($result->num_rows === 0) {
            self::$errors[] = 'El Usuario no existe';
            return false;
        }
    
        return $result->fetch_assoc(); // Fetch as associative array
    }
    
    

    public function checkPassword($user) {
        // Ensure $user is an array or object with 'password_hash'
        if (isset($user['password_hash']) && password_verify($this->password, $user['password_hash'])) {
            return true;
        } else {
            self::$errors[] = 'La contraseña es incorrecta';
            return false;
        }
    }
    
    public function authenticate($user) {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        // Set session variables
        $_SESSION['id'] = $user['id']; // or $user->id if object
        $_SESSION['email'] = $user['email']; // or $user->email if object
        $_SESSION['phone'] = $user['phone']; // or $user->phone if object
        $_SESSION['rol'] = $user['rol']; // or $user->rol if object
        $_SESSION['login'] = true;
    
        // Redirect based on role
        $redirectURL = $user['rol'] === 'Admin' ? '/admin' : '/proyectos';
    }    
}
