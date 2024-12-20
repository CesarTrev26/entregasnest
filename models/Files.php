<?php

namespace Model;

class Files extends ActiveRecord {
    protected static $table = 'files'; // Database table name
    protected static $columnsDB = ['id', 'plan_type', 'file_path', 'file_name'];

    public $id;
    public $plan_type;
    public $file_path;
    public $file_name;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->plan_type = $args['plan_type'] ?? '';
        $this->file_path = $args['file_path'] ?? '';
        $this->file_name = $args['file_name'] ?? '';
    }

    public static function all() {
        $query = "SELECT * FROM " . static::$table;
        $result = self::$db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public static function normalizeFileName($fileName) {
        // Remove any versioning suffix like (2), (3) etc. for normalization purposes
        return preg_replace('/\s*\(\d+\)\./', '.', $fileName); // Removes (2), (3), etc., before the extension
    }
    
    public static function fileExists($fileName) {
        // Do not normalize the file name here, as we want to check the exact versioned name
        $query = "SELECT * FROM " . static::$table . " WHERE file_name = '" . self::escapeString($fileName) . "' LIMIT 1";
        $result = self::$db->query($query);
        return $result->num_rows > 0;
    }
    
    public function save() {
        // Sanitize the attributes
        $attributes = $this->sanitizeAttributes();
    
        // Check if the file name with versioning exists in the database
        if (self::fileExists($this->file_name)) {
            // Handle duplicate file error if the exact file name already exists
            throw new Exception("El archivo '$this->file_name' ya existe en la base de datos.");
        }
    
        // Proceed with the usual save operation
        if (!isset($this->id)) {
            // INSERT new record
            $query = "INSERT INTO " . static::$table . " (" . join(', ', array_keys($attributes)) . ") ";
            $query .= "VALUES ('" . join("', '", array_values($attributes)) . "')";
        } else {
            // UPDATE existing record (optional, if you want update functionality)
            $query = "UPDATE " . static::$table . " SET ";
            $query .= join(', ', array_map(function($key) use ($attributes) {
                return "{$key}='{$attributes[$key]}'";
            }, array_keys($attributes)));
            $query .= " WHERE id = " . $this->id;
        }
    
        // Execute the query
        $result = self::$db->query($query);
    
        // Return true if the query was successful, false otherwise
        if ($result) {
            if (!isset($this->id)) {
                $this->id = self::$db->insert_id;
            }
            return true;
        }
    
        return false;
    }
        
    public static function escapeString($string) {
        return self::$db->real_escape_string($string);
    }

    public static function find($id) {
        $stmt = self::$db->prepare("SELECT * FROM files WHERE id = ?");
        $stmt->bind_param("i", $id); // Bind the ID as an integer
    
        // Execute the statement
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_assoc(); // Fetch the file details
        } else {
            throw new Exception("Error fetching file with ID: $id");
        }
    }
    

    public function sanitizeAttributes() {
        $attributes = [];
        foreach (static::$columnsDB as $column) {
            $value = $this->$column;
            // Only escape if the value is not null
            $attributes[$column] = isset($value) ? self::$db->escape_string($value) : 'NULL';
        }
        return $attributes;
    }

    public function deleteFile($id) {
        // Prepare the SQL statement
        $stmt = self::$db->prepare("DELETE FROM files WHERE id = ?");
        $stmt->bind_param("i", $id); // Bind the ID parameter
        
        // Execute the statement
        if ($stmt->execute()) {
            return true; // Deletion successful
        } else {
            // Log any database errors and throw an exception
            error_log("Error deleting file with ID: $id. MySQL Error: " . $stmt->error);
            throw new Exception("Error deleting file with ID: $id. MySQL Error: " . $stmt->error);
        }
    }
    

    public function deleteFileFromFilesystem($filePath) {
        // Get the full path to the file, prepending the document root to the relative path
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $filePath;
        
        // Check if the file exists before attempting to delete
        if (file_exists($fullPath)) {
            // Attempt to delete the file
            if (unlink($fullPath)) {
                return true; // File deleted successfully
            } else {
                // Log the error if unlink fails
                error_log("Failed to delete file: $fullPath");
                throw new Exception("Could not delete the file: $fullPath");
            }
        } else {
            // Log an error if the file doesn't exist
            error_log("File not found: $fullPath");
            throw new Exception("File does not exist: $fullPath");
        }
    }
    
}
