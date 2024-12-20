<?php

namespace Model;

class CustomerRecordCRUD extends ActiveRecord {
    protected static $table = 'customer_projects';
    protected static $columns = ['id', 'user_id', 'full_name', 'project_id', 'project_name', 'department_id'];

    public $id;
    public $user_id;
    public $full_name;
    public $project_id;
    public $project_name;
    public $department_id;

    public function __construct($data) {
        $this->user_id = $data['user_id'] ?? null;
        $this->full_name = $data['full_name'] ?? null;
        $this->project_id = $data['project_id'] ?? null;
        $this->project_name = $data['project_name'] ?? null;
        $this->department_id = $data['department_id'] ?? null;
    }
    

    // Find all customer projects with joined data
    public static function all() {
        $query = "SELECT customer_projects.*, users.full_name, projects.project_name, departments.department_name
                  FROM " . static::$table . "
                  LEFT JOIN users ON customer_projects.user_id = users.id
                  LEFT JOIN projects ON customer_projects.project_id = projects.id
                  LEFT JOIN departments ON customer_projects.department_id = departments.id";
        $result = self::$db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Search customer projects by full name or project name with joined data
    public static function search($search = '') {
        $query = "SELECT customer_projects.*, users.full_name, projects.project_name, departments.department_name
                  FROM customer_projects
                  LEFT JOIN users ON customer_projects.user_id = users.id
                  LEFT JOIN projects ON customer_projects.project_id = projects.id
                  LEFT JOIN departments ON customer_projects.department_id = departments.id";
        
        if ($search) {
            $query .= " WHERE users.full_name LIKE ? OR projects.project_name LIKE ?";
        }
    
        $stmt = self::$db->prepare($query);
        if ($search) {
            $searchTerm = '%' . $search . '%';
            $stmt->bind_param('ss', $searchTerm, $searchTerm);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }    

    public static function getDepartmentsByProject($projectId) {
        $query = "SELECT * FROM departments WHERE project_id = ? AND id NOT IN (SELECT department_id FROM customer_projects)";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();
        $departments = $result->fetch_all(MYSQLI_ASSOC);
    
        // Debug output
        error_log('Departments fetched: ' . print_r($departments, true));
        
        return $departments;
    }

    public static function getUnassignedDepartments($project_id) {
        // Query to fetch departments that are not assigned to any customer
        $query = "SELECT * FROM departments WHERE project_id = ? AND id NOT IN (SELECT department_id FROM customer_projects)";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function getTowerNameByDepartmentId($departmentId) {
        $query = "SELECT department_name FROM departments WHERE id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $departmentId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
    
        return $result['department_name'] ?? '';
    }
    
    
    // Create a new customer project
    public function create() {
        $query = "INSERT INTO " . static::$table . " (user_id, full_name, project_id, project_name, department_id) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = self::$db->prepare($query);
        
        // Bind parameters and check if they are correctly set
        $stmt->bind_param("isiss", $this->user_id, $this->full_name, $this->project_id, $this->project_name, $this->department_id);
        
        // Debug SQL errors if any
        if (!$stmt->execute()) {
            var_dump(self::$db->error); // Print SQL error
            return false;
        }
        
        return true;
    }
    
    // Find a customer project by ID with joined data
    public static function find($id) {
        $query = "SELECT * FROM " . static::$table . " WHERE id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function synchronize($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
    
    public function update() {
        $query = "UPDATE customer_projects SET user_id = ?, project_id = ?, project_name = ?, department_id = ? WHERE id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param('iisii', $this->user_id, $this->project_id, $this->project_name, $this->department_id, $this->id);
        return $stmt->execute();
    }
    
    public static function delete($id) {
        $query = "DELETE FROM " . static::$table . " WHERE id = ?";
        $stmt = self::$db->prepare($query);
    
        if (!$stmt) {
            error_log("Prepare failed: " . self::$db->error);
            return false;
        }
    
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            return false;
        }
    
        return true;
    }
    
}
