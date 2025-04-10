<?php
// models/User.php
class User {
    // Database connection and table name
    private $conn;
    private $table_name = "users";
    
    // Object properties
    public $id;
    public $username;
    public $password;
    public $full_name;
    public $created_at;
    
    // Constructor with database connection
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Login user
    public function login() {
        // Sanitize inputs
        $this->username = $this->sanitize($this->username);
        $this->password = $this->sanitize($this->password);
        
        // Query for user
        $query = "SELECT id, username, password, full_name FROM " . $this->table_name . " 
                 WHERE username = '{$this->username}' LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $encrypt = md5($this->password);
            
            // Verify password (MD5 for simplicity - not recommended for production)
            if (md5($this->password) == $row['password']) {
                // Set properties
                $this->id = $row['id'];
                $this->full_name = $row['full_name'];
                return true;
            }
        }
        
        return false;
    }
    
    // Create user
    public function create() {
        // Sanitize inputs
        $this->username = $this->sanitize($this->username);
        $this->password = md5($this->sanitize($this->password)); // Hash password
        $this->full_name = $this->sanitize($this->full_name);
        
        // Check if username already exists
        $check_query = "SELECT id FROM " . $this->table_name . " WHERE username = '{$this->username}' LIMIT 1";
        $check_result = $this->conn->query($check_query);
        
        if ($check_result->num_rows > 0) {
            return false; // Username already exists
        }
        
        // Create query
        $query = "INSERT INTO " . $this->table_name . " (username, password, full_name) 
                  VALUES ('{$this->username}', '{$this->password}', '{$this->full_name}')";
        
        // Execute query
        if ($this->conn->query($query)) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        
        return false;
    }
    
    // Read all users
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id ASC";
        return $this->conn->query($query);
    }
    
    // Read one user
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = " . $this->id;
        $result = $this->conn->query($query);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->username = $row['username'];
            $this->full_name = $row['full_name'];
            $this->created_at = $row['created_at'];
            return true;
        }
        
        return false;
    }
    
    // Update user
    public function update() {
        // Sanitize inputs
        $this->id = intval($this->id);
        $this->username = $this->sanitize($this->username);
        $this->full_name = $this->sanitize($this->full_name);
        
        // Get current user data for audit
        $current_query = "SELECT * FROM " . $this->table_name . " WHERE id = " . $this->id;
        $current_result = $this->conn->query($current_query);
        $current_data = $current_result->fetch_assoc();
        
        // Check if username already exists (for another user)
        $check_query = "SELECT id FROM " . $this->table_name . " 
                        WHERE username = '{$this->username}' AND id != {$this->id} LIMIT 1";
        $check_result = $this->conn->query($check_query);
        
        if ($check_result->num_rows > 0) {
            return false; // Username already exists
        }
        
        $query = "UPDATE " . $this->table_name . " SET 
                  username = '{$this->username}', 
                  full_name = '{$this->full_name}'";
        
        // Add password update if provided
        if (!empty($this->password)) {
            $this->password = md5($this->sanitize($this->password));
            $query .= ", password = '{$this->password}'";
        }
        
        $query .= " WHERE id = {$this->id}";
        
        // Execute query
        if ($this->conn->query($query)) {
            // Add audit logs
            if ($current_data['username'] != $this->username) {
                $this->addAudit('users', 'username', $current_data['username'], $this->username);
            }
            if ($current_data['full_name'] != $this->full_name) {
                $this->addAudit('users', 'full_name', $current_data['full_name'], $this->full_name);
            }
            if (!empty($this->password)) {
                $this->addAudit('users', 'password', 'changed', 'changed');
            }
            
            return true;
        }
        
        return false;
    }
    
    // Delete user
    public function delete() {
        $this->id = intval($this->id);
        
        // Get current user data for audit
        $current_query = "SELECT * FROM " . $this->table_name . " WHERE id = " . $this->id;
        $current_result = $this->conn->query($current_query);
        $current_data = $current_result->fetch_assoc();
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id = " . $this->id;
        
        if ($this->conn->query($query)) {
            // Add audit logs
            $this->addAudit('users', 'username', $current_data['username'], 'deleted');
            $this->addAudit('users', 'full_name', $current_data['full_name'], 'deleted');
            
            return true;
        }
        
        return false;
    }
    
    // Helper functions
    private function sanitize($input) {
        return $this->conn->real_escape_string(trim($input));
    }
    
    private function addAudit($table, $field, $old_value, $new_value, $entry_id = null) {
        $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 1;
        $entry_id = $entry_id ? intval($entry_id) : 'NULL';
        
        $query = "INSERT INTO audit (table_name, field_name, old_value, new_value, user_id, entry_id) 
                  VALUES ('{$this->sanitize($table)}', '{$this->sanitize($field)}', 
                         '{$this->sanitize($old_value)}', '{$this->sanitize($new_value)}', 
                         {$user_id}, {$entry_id})";
                  
        return $this->conn->query($query);
    }

    // Reset user password  
    public function resetPassword() {
    // Sanitize and hash the password
    $this->password = md5($this->sanitize($this->password));
    
    // Update query
    $query = "UPDATE " . $this->table_name . " SET 
              password = '{$this->password}'
              WHERE id = {$this->id}";
    
    // Execute query
    if ($this->conn->query($query)) {
        // Add audit log
        $this->addAudit('users', 'password', 'changed', 'reset');
        return true;
    }
    
    return false;
    }
    
}
?>