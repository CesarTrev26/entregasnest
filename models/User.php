<?php 

namespace Model;

class User extends ActiveRecord {
    protected static $table = 'users';
    protected static $columnsDB = ['id', 'full_name', 'rol_id', 'email', 'password_hash', 'phone' ];

    public $id;
    public $full_name;
    public $rol_id;
    public $email;
    public $password_hash;
    public $phone;
    
    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->full_name = $args['full_name'] ?? null;
        $this->rol_id = $args['rol_id'] ?? null;
        $this->email = $args['email'] ?? null;
        $this->password_hash = $args['password_hash'] ?? null;
        $this->phone = $args['phone'] ?? null;
    }

    // Create a new user
    public function create() {
        if (self::emailExists($this->email)) {
            throw new \Exception('Email already exists');
        }

        $sql = "INSERT INTO users (full_name, email, phone, password_hash, rol_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = self::$db->prepare($sql);
        $stmt->bind_param('ssssi', $this->full_name, $this->email, $this->phone, $this->password_hash, $this->rol_id);

        return $stmt->execute();
    }

    public function update() {
        // Ensure you have a valid rol_id before proceeding
        $validRolIds = self::getValidRolIds();
        if (!in_array($this->rol_id, $validRolIds)) {
            throw new Exception("Invalid rol_id: {$this->rol_id}");
        }
    
        // Update SQL query with correct number of placeholders
        $query = "UPDATE " . self::$table . " SET 
            full_name = ?,
            rol_id = ?,
            email = ?,
            password_hash = ?,
            phone = ?
            WHERE id = ?";
    
        $stmt = self::$db->prepare($query);
    
        // Ensure the parameter types match the SQL query
        $stmt->bind_param('sisssi', $this->full_name, $this->rol_id, $this->email, $this->password_hash, $this->phone, $this->id);
    
        // Debugging and error logging
        error_log("Updating user ID {$this->id} with role ID {$this->rol_id}");
    
        if (!$stmt->execute()) {
            error_log("SQL Error: " . $stmt->error);
            throw new mysqli_sql_exception($stmt->error);
        }
    
        return true; // Ensure to return true on successful update
    }
    
    
    public static function getValidRolIds() {
        $query = "SELECT id FROM rol";
        $result = self::$db->query($query);
        $validRolIds = [];
    
        while ($row = $result->fetch_assoc()) {
            $validRolIds[] = $row['id'];
        }
    
        return $validRolIds;
    }
    
    

    // Delete a user
    public function delete() {
        $query = "DELETE FROM " . static::$table . " WHERE id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param('i', $this->id);
        return $stmt->execute();
    }

   // Fetch a single user with rol details
   public static function find($id) {
        $query = "SELECT u.*, r.rol
                FROM " . static::$table . " u
                LEFT JOIN rol r ON u.rol_id = r.id 
                WHERE u.id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param('i', $id); 
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? new self($result) : null;
    }

    public static function getRoles() {
        $sql = "SELECT id, rol FROM rol";
        $stmt = self::$db->prepare($sql); // Use the database connection
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC); // Fetch all results as associative array
    }

    public static function search($query) {
        $query = '%' . $query . '%'; 
        $sql = "SELECT * FROM " . static::$table . " WHERE full_name LIKE ? OR email LIKE ?";
        $stmt = self::$db->prepare($sql);
        $stmt->bind_param('ss', $query, $query); // Bind parameters
        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = new self($row);
        }
        return $users;
    }

    // Fetch users with their roles (JOIN with the rol table)
    public static function all() {
        $query = "SELECT u.*, r.rol 
                  FROM " . static::$table . " u
                  LEFT JOIN rol r ON u.rol_id = r.id"; // Join the rol table to fetch role details
        $result = self::$db->query($query);
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = new self($row);
        }
        return $users;
    }

    public static function allDropdown() {
        $query = "SELECT id, full_name FROM users";
        $result = self::$db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function emailExists($email) {
        $db = connectDB();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Check if the phone is unique
    public static function phoneExists($phone) {
        $db = connectDB();
        $stmt = $db->prepare("SELECT id FROM users WHERE phone = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Check if the full name is unique
    public static function fullNameExists($full_name) {
        $db = connectDB();
        $stmt = $db->prepare("SELECT id FROM users WHERE full_name = ?");
        $stmt->bind_param("s", $full_name);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
}
