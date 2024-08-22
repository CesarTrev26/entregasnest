<?php

namespace Model;

class Admin extends ActiveRecord {
    protected static $table = 'users';
    protected static $columnsDB = ['id', 'username', 'password_hash', 'full_name', 'phone', 'email'];

    public $id;
    public $username;
    public $password;
    public $fullname;
    public $phone;
    public $email;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->username = $args['username'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->fullname = $args['full_name'] ?? '';
        $this->phone = $args['phone'] ?? '';
        $this->email = $args['email'] ?? '';
    }

    public function validate() {
        if(!$this->email) {
            self::$errors[] = 'El email es obligatorio';
        }
        if(!$this->password) {
            self::$errors[] = 'La contraseña es obligatoria';
        }

        return self::$errors;
    }

    public function userExists() {
        $query = "SELECT * FROM " . self::$table . " WHERE email = '" . $this->email . "' LIMIT 1";
        $result = self::$db->query($query);

        if(!$result->num_rows) {
            self::$errors[] = 'El Usuario no existe';
            return false;
        }
        return $result;
    }

    public function checkPassword($result) {
        $user = $result->fetch_object();

        if($auth = ($this->password === $user->password_hash)) {
            $auth = true;
            return $auth;
        } else {
            self::$errors[] = 'La contraseña es incorrecta';
        }
        //debug($auth);
    }

    public function authenticate() {
        //Fill the SESSION's array
        session_start();
        $_SESSION['id'] = $user['id'];
        $_SESSION['email'] = $user['email']; 
        $_SESSION['phone'] = $user['phone'];
        $_SESSION['login'] = true;

        header('Location: /proyectos');
    }
}