<?php 

namespace Model;

class ActiveRecord {

    //Database
    protected static $db;
    protected static $errors = [];

    // Define the Database connection
    public static function setDB($database) {
        self::$db = $database;
    }

    //Validation
    public static function getErrors() {
        return self::$errors;
    }

}