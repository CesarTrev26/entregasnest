<?php

namespace Model;

class Projects extends ActiveRecord {
    protected static $table = 'projects';
    protected static $columnsDB = ['id', 'project_name', 'location'];

    public $id;
    public $project_name;
    public $location;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->project_name = $args['project_name'] ?? '';
        $this->location = $args['location'] ?? '';
    }

    public static function fetchProjectsAndDepartments($userId) {
        $userId = self::$db->real_escape_string($userId);
        $query = "SELECT p.id, p.project_name, p.location, COUNT(d.id) AS department_count 
                  FROM " . self::$table . " p 
                  LEFT JOIN departments d ON p.id = d.project_id 
                  INNER JOIN customer_projects cp ON p.id = cp.project_id 
                  WHERE cp.user_id = '$userId' 
                  GROUP BY p.id";

        // Execute query
        $result = self::$db->query($query);

        if(!$result) {
            echo "Error: " . self::$db->error;
            return false;
        }

        // Fetch all projects with their department count
        $projects_with_departments = [];
        while ($row = $result->fetch_assoc()) {
            $projects_with_departments[] = $row;
        }
        return $projects_with_departments;
    }

    public static function fetchUserDepartments($projectId, $userId) {
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
        if(!$result) {
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
        $userId = self::$db->real_escape_string($userId);
        $query = "SELECT * FROM users WHERE id = '$userId' LIMIT 1";

        $result = self::$db->query($query);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }
}
