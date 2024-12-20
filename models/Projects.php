<?php

namespace Model;

class Projects extends ActiveRecord {
    protected static $table = 'projects';
    protected static $columnsDB = ['id', 'project_name', 'location'];

    public $id;
    public $project_name;
    public $location;
    public $name;     
    public $email;    
    public $department;
    public $tower;    
    public $project;  
    public $file;    
    public $message;  

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->project_name = $args['project_name'] ?? '';
        $this->location = $args['location'] ?? '';
        $this->name = $args['name'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->department = $args['department'] ?? '';
        $this->tower = $args['tower'] ?? '';
        $this->project = $args['project'] ?? '';
        $this->file = $args['file'] ?? '';
        $this->message = $args['message'] ?? '';
    }

    public static function fetchProjectsAndDepartments($userId) {
        $userId = (string) $userId;
        $userId = self::$db->real_escape_string($userId);
        
        // Subquery to get unique projects
        $subquery = "
            SELECT p.id, p.project_name, p.location 
            FROM " . self::$table . " p 
            INNER JOIN customer_projects cp ON p.id = cp.project_id 
            WHERE cp.user_id = '$userId'
            GROUP BY p.id
        ";
        
        $query = "
            SELECT p.id, p.project_name, p.location, 
                   COUNT(d.id) AS department_count,
                   GROUP_CONCAT(DISTINCT CASE WHEN f.plan_type = 'manual' THEN f.file_path END) AS manual_paths,
                   GROUP_CONCAT(DISTINCT CASE WHEN f.plan_type = 'videos' THEN f.file_path END) AS video_paths
            FROM ($subquery) AS p 
            LEFT JOIN departments d ON p.id = d.project_id 
            LEFT JOIN project_files pf ON pf.project_id = p.id 
            LEFT JOIN files f ON f.id = pf.file_id 
            GROUP BY p.id
        ";
        
        $result = self::$db->query($query);
        
        if (!$result) {
            echo "Error: " . self::$db->error;
            return false;
        }
        
        $projects_with_departments = [];
        while ($row = $result->fetch_assoc()) {
            $projects_with_departments[] = $row;
        }
        
        return $projects_with_departments;
    }
    

    public static function fetchProjects($userId) {
        // Escape the user ID to prevent SQL injection
        $userId = (string) $userId;
        $userId = self::$db->real_escape_string($userId);
    
        // Subquery to get the projects associated with the user
        $subquery = "
            SELECT p.id, p.project_name, p.location 
            FROM " . self::$table . " p 
            INNER JOIN customer_projects cp ON p.id = cp.project_id 
            WHERE cp.user_id = '$userId'
            GROUP BY p.id
        ";
    
        // Main query to get the projects with their departments and files categorized by assigned_button
        $query = "
            SELECT 
                p.id, 
                p.project_name, 
                p.location, 
                COUNT(DISTINCT d.id) AS department_count,
                GROUP_CONCAT(DISTINCT CASE WHEN pt.assigned_button = 'manual' THEN f.file_path END) AS manual_paths,
                GROUP_CONCAT(DISTINCT CASE WHEN pt.assigned_button = 'video' THEN f.file_path END) AS video_paths
            FROM ($subquery) AS p 
            LEFT JOIN departments d ON p.id = d.project_id 
            LEFT JOIN project_files pf ON pf.project_id = p.id 
            LEFT JOIN files f ON f.id = pf.file_id 
            LEFT JOIN plan_types pt ON f.plan_type = pt.description
            GROUP BY p.id
            HAVING department_count > 0  -- Only include projects that have at least one department
        ";
    
        // Execute the query
        $result = self::$db->query($query);
    
        // Check if query was successful
        if (!$result) {
            echo "Error: " . self::$db->error;
            return false;
        }
    
        // Store the projects with departments
        $projects_with_departments = [];
        while ($row = $result->fetch_assoc()) {
            $projects_with_departments[] = $row;
        }
    
        return $projects_with_departments;
    }    

    public static function getDepartmentsForTower($userId, $projectId, $towerId) {
        // Prepare the query to get both department_id and department_name
        $query = "
            SELECT d.id, d.department_name
            FROM departments d
            WHERE d.project_id = ? 
            AND d.project_tower = ? 
            AND d.id IN (
                SELECT department_id 
                FROM customer_projects 
                WHERE user_id = ?
            )
        ";
    
        // Prepare the statement
        $stmt = self::$db->prepare($query);
    
        // Bind the parameters to the query
        $stmt->bind_param("isi", $projectId, $towerId, $userId); // 'i' for integers, 's' for strings
    
        // Execute the query
        $stmt->execute();
    
        // Get the result
        $result = $stmt->get_result();
    
        // Fetch the result as an associative array
        $departments = [];
        while ($row = $result->fetch_assoc()) {
            // Add both id and department_name to the departments array
            $departments[] = [
                'id' => $row['id'], 
                'department_name' => $row['department_name']
            ];
        }
    
        // Return the departments as an array of objects with id and department_name
        return $departments;
    }    

    public static function fetchDepartmentPlans($departmentId) {
        $departmentId = self::$db->real_escape_string((string) $departmentId);
        
        // Modify the query to join the files table
        $query = "
            SELECT dp.*, f.plan_type, f.file_path, pt.assigned_button
            FROM department_plan AS dp
            LEFT JOIN files AS f ON dp.file_id = f.id
            LEFT JOIN plan_types AS pt ON f.plan_type = pt.description
            WHERE dp.department_id = '$departmentId'
        ";
        
        $result = self::$db->query($query);
        
        if (!$result) {
            echo "Error: " . self::$db->error;
            return false;
        }
        
        $plans = [];
        while ($row = $result->fetch_assoc()) {
            $plans[] = $row;
        }
        
        return $plans;
    }

    public static function fetchUserTowers($projectId, $userId) {
        $projectId = (string) $projectId;
        $userId = (string) $userId;
        $projectId = self::$db->real_escape_string($projectId);
        $userId = self::$db->real_escape_string($userId);
    
        // Fetch unique tower names based on project and departments assigned to the user
        $query = "SELECT DISTINCT d.project_tower
                  FROM departments d
                  WHERE d.project_id = '$projectId' 
                  AND d.id IN (
                      SELECT department_id 
                      FROM customer_projects 
                      WHERE user_id = '$userId'
                  )";
    
        $result = self::$db->query($query);
    
        if (!$result) {
            echo "Error: " . self::$db->error;
            return false;
        }
    
        $towers = [];
        while ($row = $result->fetch_assoc()) {
            $tower = $row['project_tower'];
            if ($tower) {  // Check if tower name is not null or empty
                $towers[] = $tower;
            }
        }
    
        return $towers;
    }    
    
    public static function fetchUserDepartments($projectId, $userId) {
        $projectId = (string) $projectId;
        $userId = (string) $userId;
        $projectId = self::$db->real_escape_string($projectId);
        $userId = self::$db->real_escape_string($userId);
    
        $query = "SELECT * FROM departments 
                  WHERE project_id = '$projectId' 
                  AND id IN (
                      SELECT department_id 
                      FROM customer_projects 
                      WHERE user_id = '$userId'
                  )";
    
        $result = self::$db->query($query);
    
        if (!$result) {
            echo "Error: " . self::$db->error;
            return false;
        }
    
        $departments = [];
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }
    
        return $departments;
    }
    

    public static function fetchUserData($userId) {
        // Ensure $userId is a string
        $userId = (string) $userId;
        $userId = self::$db->real_escape_string($userId);

        $query = "SELECT * FROM users WHERE id = '$userId' LIMIT 1";

        $result = self::$db->query($query);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    public function validate() {
        if (!$this->name) {
            self::$errors[] = 'El nombre es obligatorio';
        }
        if (!$this->email) {
            self::$errors[] = 'El email es obligatorio';
        }
        if (!$this->department) {
            self::$errors[] = 'El departamento es obligatorio';
        }
        if (!$this->tower) {
            self::$errors[] = 'La torre es obligatoria';
        }
        if (!$this->project) {
            self::$errors[] = 'El proyecto es obligatorio';
        }
        if (!$this->file) {
            self::$errors[] = 'Sube al menos 1 evidencia';
        }
        if (!$this->message) {
            self::$errors[] = 'Danos una explicación de tu situación';
        }

        return self::$errors;
    }
}
