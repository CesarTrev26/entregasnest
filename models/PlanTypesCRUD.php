<?php

namespace Model;

class PlanTypesCRUD extends ActiveRecord {
    protected static $table = 'plan_types'; // Database table name
    protected static $columnsDB = ['id', 'keyword', 'description', 'assigned_button'];

    public $id;
    public $keyword;
    public $description;
    public $assigned_button;

    // Constructor with default values
    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->keyword = $args['keyword'] ?? '';
        $this->description = $args['description'] ?? '';
        $this->assigned_button = $args['assigned_button'] ?? '';
    }

    // Fetch all records and return as an array of PlanTypesCRUD objects
    public static function all() {
        $query = "SELECT * FROM " . static::$table;
        $result = self::$db->query($query);
        if (!$result) {
            throw new Exception("Error fetching plan types: " . self::$db->error);
        }

        $planTypes = [];
        while ($row = $result->fetch_assoc()) {
            $planTypes[] = new self($row); // Create an instance of PlanTypesCRUD for each record
        }
        return $planTypes;
    }

    // Find a specific record by ID and return a PlanTypesCRUD instance
    public static function find($id) {
        $stmt = self::$db->prepare("SELECT * FROM " . static::$table . " WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . self::$db->error);
        }

        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error executing query: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // Return an instance of PlanTypesCRUD, or null if not found
        return $row ? new self($row) : null;
    }

    public function validate() {
        $errors = [];
    
        // Validate required fields
        if (!$this->keyword) {
            $errors[] = "La palabra clave es obligatoria.";
        }
        if (!$this->description) {
            $errors[] = "La descripción es obligatoria.";
        }
        if (!$this->assigned_button) {
            $errors[] = "Debe asignar el plano a un botón.";
        }
    
        // Validate uniqueness of keyword and description
        if ($this->isDuplicate('keyword', $this->keyword)) {
            $errors[] = "La palabra clave ya existe en la base de datos.";
        }
        if ($this->isDuplicate('description', $this->description)) {
            $errors[] = "La descripción ya existe en la base de datos.";
        }
    
        return $errors;
    }

    private function isDuplicate($field, $value) {
        $field = self::$db->real_escape_string($field);
        $value = self::$db->real_escape_string($value);
    
        $query = "SELECT COUNT(*) AS count FROM " . self::$table . " WHERE $field = '$value'";
        $result = self::$db->query($query);
    
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['count'] > 0;
        }
    
        return false;
    }

    public function validateUpdate() {
        $errors = [];
    
        // Validate required fields
        if (!$this->keyword) {
            $errors[] = "La palabra clave es obligatoria.";
        }
        if (!$this->description) {
            $errors[] = "La descripción es obligatoria.";
        }
        if (!$this->assigned_button) {
            $errors[] = "Debe asignar el plano a un botón.";
        }
    
        return $errors;
    }
    

    // Synchronize the object with POST data
    public function synchronize($data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = htmlspecialchars(trim($value));
            }
        }
    }

    // Create a new record in the database
    public function create() {
        $stmt = self::$db->prepare("INSERT INTO " . static::$table . " (keyword, description, assigned_button) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new \Exception("Error preparing statement: " . self::$db->error);
        }
        $stmt->bind_param("sss", $this->keyword, $this->description, $this->assigned_button);
        $result = $stmt->execute();
        if (!$result) {
            throw new \Exception("Error creating plan type: " . $stmt->error);
        }
        return $result;
    }

    // Update an existing record in the database
    public function update() {
        $stmt = self::$db->prepare("UPDATE " . static::$table . " SET keyword = ?, description = ?, assigned_button = ? WHERE id = ?");
        if (!$stmt) {
            throw new \Exception("Error preparing statement: " . self::$db->error);
        }
        $stmt->bind_param("sssi", $this->keyword, $this->description, $this->assigned_button, $this->id);
        $result = $stmt->execute();
        if (!$result) {
            throw new \Exception("Error updating plan type: " . $stmt->error);
        }
        return $result;
    }

    // Delete a record from the database
    public static function delete($id) {
        $stmt = self::$db->prepare("DELETE FROM " . static::$table . " WHERE id = ?");
        if (!$stmt) {
            throw new \Exception("Error preparing statement: " . self::$db->error);
        }
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        if (!$result) {
            throw new \Exception("Error deleting plan type: " . $stmt->error);
        }
        return $result;
    }
}
