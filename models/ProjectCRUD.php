<?php

namespace Model;

class ProjectCRUD extends ActiveRecord {
    // Define the table and columns
    protected static $table = 'projects';
    protected static $columnsDB = ['project_name', 'location'];

    public $id;
    public $project_name;
    public $location;

    // Constructor to initialize properties
    public function __construct($args = []) {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    // Fetch all projects
    public static function all() {
        $query = "SELECT * FROM " . static::$table;
        $result = self::$db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function allDropdown() {
        $query = "SELECT id, project_name FROM projects";
        $result = self::$db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function savePlansForProject($projectId, $selectedPlans) {
        $query = "INSERT INTO project_files (project_id, file_id) VALUES (?, ?)";
        $stmt = self::$db->prepare($query);
    
        foreach ($selectedPlans as $fileId) {
            // Verify if file exists in Files table before saving
            $fileExists = Files::find($fileId); // This assumes find() checks for file existence
            if (!$fileExists) {
                throw new \Exception("El plan con ID " . ($fileId ?? 'desconocido') . " no existe en la tabla de archivos.");
            }
    
            $stmt->bind_param('ii', $projectId, $fileId);
            if (!$stmt->execute()) {
                return false;
            }
        }
        return true;
    }
    

    public static function updatePlansForProject($projectId, $fileIds) {
        // Start a transaction
        self::$db->begin_transaction();
    
        try {
            // Delete existing plans for the project
            self::deletePlansByProjectId($projectId);
    
            // Insert new plans
            self::savePlansForProject($projectId, $fileIds);
    
            // Commit the transaction
            self::$db->commit();
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            self::$db->rollback();
            throw $e; // Rethrow the exception for further handling
        }
    }
    

    public static function getPlansByProjectId($projectId) {
        $db = self::$db;
        // Adjust the query to correctly join the project_files table
        $query = "SELECT pf.file_id, f.file_name, f.plan_type 
                  FROM project_files pf 
                  JOIN files f ON pf.file_id = f.id 
                  WHERE pf.project_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('i', $projectId);
        $stmt->execute();
    
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    

    public static function deletePlansByProjectId($projectId) {
        $query = "DELETE FROM project_files WHERE project_id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param('i', $projectId);
        return $stmt->execute();
    }
    

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
        
        if ($stmt->execute()) {
            // Set the newly created ID to $this->id
            $this->id = self::$db->insert_id;
            return true;
        }
        return false;
    }    

    // Update an existing project
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

    // Delete a project
    public function delete() {
        $query = "DELETE FROM " . static::$table . " WHERE id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param('i', $this->id);
        return $stmt->execute();
    }

    public static function find($id) {
        $query = "SELECT * FROM " . static::$table . " WHERE id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = $result->fetch_assoc();

        // Return a new ProjectCRUD object if found, otherwise null
        return $data ? new self($data) : null;
    }

    public function synchronize($data) {
        $this->id = $data['id'] ?? null;
        $this->project_name = $data['project_name'] ?? '';
        $this->location = $data['location'] ?? '';
    }

    // Search for projects by name
    public static function search($search) {
        $search = "%{$search}%";
        $query = "SELECT * FROM " . static::$table . " WHERE project_name LIKE ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
