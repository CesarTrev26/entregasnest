<?php

namespace Model;

class DepartmentCRUD extends ActiveRecord {
    protected static $table = 'departments';
    protected static $columnsDB = ['project_id', 'project_name', 'project_tower', 'department_name', 'department_basement'];

    public $id;
    public $project_id;
    public $project_name;
    public $project_tower;
    public $department_name;
    public $department_basement;

    // Constructor to initialize properties
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    // Fetch all departments with project names
    public static function all() {
        $query = "SELECT d.*, p.project_name FROM departments d JOIN projects p ON d.project_id = p.id";
        $result = self::$db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    

    // Fetch departments for a dropdown, excluding those already assigned
    public static function allDropdown($projectId) {
        $query = "
            SELECT id, department_name 
            FROM " . static::$table . " 
            WHERE project_id = ? 
              AND id NOT IN (SELECT department_id FROM customer_projects)
        ";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Check for errors and return results
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            die('SQL Error: ' . self::$db->error);
        }
    }

    public function getTowersByProjectId($projectId)
    {
        $query = "SELECT DISTINCT project_tower AS tower_name, project_id AS id FROM departments WHERE project_id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param('i', $projectId);
        $stmt->execute();

        $result = $stmt->get_result();
        
        // Fetch all results
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        return $data;
    }

    public function getDepartmentsByTowerName($towerName)
    {
        $query = "
            SELECT d.id, d.department_name 
            FROM departments d
            LEFT JOIN customer_projects cp ON d.id = cp.department_id
            WHERE d.project_tower = ? AND cp.department_id IS NULL
        ";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param('s', $towerName); // Use 's' for string
        $stmt->execute();
    
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public static function search($search = '') {
        $query = "SELECT d.*, p.project_name
                  FROM " . static::$table . " d
                  LEFT JOIN projects p ON d.project_id = p.id";
        
        if ($search) {
            $query .= " WHERE p.project_name LIKE ? OR d.department_name LIKE ?";
        }
        
        $stmt = self::$db->prepare($query);
        if ($search) {
            $searchTerm = '%' . $search . '%';
            $stmt->bind_param('ss', $searchTerm, $searchTerm);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Find a department by ID
    public static function find($id) {
        // Use the static table name property
        $query = "SELECT * FROM " . static::$table . " WHERE id = ?";
        
        // Prepare the query
        $stmt = self::$db->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars(self::$db->error));
        }

        // Bind parameters
        $stmt->bind_param("i", $id);

        // Execute the query
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch the result as an associative array
        $data = $result->fetch_assoc();

        // Close the statement
        $stmt->close();

        if ($data) {
            return new self($data); // Ensure to return an instance of DepartmentCRUD
        }
        return null;
    }

    public function synchronize($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public static function savePlans($departmentId, $selectedPlans) {
        $query = "INSERT INTO department_plan (department_id, file_id) VALUES (?, ?)";
        $stmt = self::$db->prepare($query);
    
        foreach ($selectedPlans as $fileId) {
            // Verify if file exists in Files table before saving
            $fileExists = Files::find($fileId); // This assumes find() checks for file existence
            if (!$fileExists) {
                var_dump($fileId);
                throw new \Exception("El plan con ID " . ($fileId ?? 'desconocido') . " no existe en la tabla de archivos.");
            }
            
            
    
            $stmt->bind_param('ii', $departmentId, $fileId);
            if (!$stmt->execute()) {
                return false;
            }
        }
        return true;
    }    

    public static function getPlansByDepartmentId($departmentId) {
        $query = "SELECT dp.file_id, f.file_name 
                  FROM department_plan dp 
                  JOIN files f ON dp.file_id = f.id 
                  WHERE dp.department_id = ?";
        
        $stmt = self::$db->prepare($query);
        $stmt->bind_param('i', $departmentId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function updatePlansForDepartment($departmentId, $fileIds) {
        // Start a transaction
        self::$db->begin_transaction();
    
        try {
            // Delete existing plans for the department
            self::deletePlansByDepartmentId($departmentId);
    
            // Insert new plans
            self::savePlans($departmentId, $fileIds);
    
            // Commit the transaction
            self::$db->commit();
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            self::$db->rollback();
            throw $e; // Rethrow the exception for further handling
        }
    }
    
    public static function deletePlansByDepartmentId($departmentId) {
        $query = "DELETE FROM department_plan WHERE department_id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param('i', $departmentId);
        return $stmt->execute();
    }

    // Delete specific plan
    public static function deletePlan($departmentId, $fileId) {
        $query = "DELETE FROM department_plan WHERE department_id = ? AND file_id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param('ii', $departmentId, $fileId);
        return $stmt->execute();
    }

    // Create a new department
    public function create() {
        $columns = implode(', ', static::$columnsDB);
        $placeholders = implode(', ', array_fill(0, count(static::$columnsDB), '?'));
        $query = "INSERT INTO " . static::$table . " ($columns) VALUES ($placeholders)";
        $stmt = self::$db->prepare($query);
    
        // Prepare the types string and values array
        $types = str_repeat('s', count(static::$columnsDB)); // Assume all columns are strings for simplicity
        $values = [];
        foreach (static::$columnsDB as $column) {
            $values[] = $this->$column;
        }
    
        // Bind parameters
        $stmt->bind_param($types, ...$values);
    
        // Execute the statement and check for success
        if ($stmt->execute()) {
            // Return the ID of the newly inserted row
            return $stmt->insert_id;
        } else {
            // Return false if the insert failed
            return false;
        }
    }
    
    // Update an existing department
    public function update() {
        $setClause = implode(', ', array_map(fn($col) => "$col = ?", static::$columnsDB));
        $query = "UPDATE " . static::$table . " SET $setClause WHERE id = ?";
        $stmt = self::$db->prepare($query);

        // Prepare the types string and values array
        $types = str_repeat('s', count(static::$columnsDB)) . 'i'; // Assume all columns are strings except id
        $values = [];
        foreach (static::$columnsDB as $column) {
            $values[] = $this->$column;
        }
        $values[] = $this->id; // Add id to values array

        // Bind parameters
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    // Delete a department
    public function delete() {
        $query = "DELETE FROM " . static::$table . " WHERE id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param('i', $this->id);
        return $stmt->execute();
    }
}
