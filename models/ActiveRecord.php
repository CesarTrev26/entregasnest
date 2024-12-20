<?php 

namespace Model;

class ActiveRecord {
    protected static $db;
    protected static $errors = [];

    // Define the Database connection
    public static function setDB($database) {
        self::$db = $database;
    }

    public static function getErrors() {
        return self::$errors ?? [];
    }

    // Method to execute a query and return results
    protected static function fetchResults($query) {
        $result = self::$db->query($query);
        if ($result === false) {
            die('Query failed: ' . self::$db->error);
        }
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        return $data;
    } 

    // Method to create an object from the database row
    protected static function createObject($row) {
        $object = new static(); // Create an instance of the calling class
        foreach ($row as $key => $value) {
            if (property_exists($object, $key)) {
                $object->$key = $value;
            }
        }
        return $object;
    }
}
