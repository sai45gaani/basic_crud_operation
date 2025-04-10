<?php
// models/Audit.php
class Audit {
    // Database connection and table name
    private $conn;
    private $table_name = "audit";
    
    // Object properties
    public $id;
    public $table_name_field;
    public $field_name;
    public $old_value;
    public $new_value;
    public $user_id;
    public $entry_id;
    public $created_at;
    
    // Constructor with database connection
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Read all audit logs
    public function readAll() {
        $query = "SELECT a.*, u.username FROM " . $this->table_name . " a
                  LEFT JOIN users u ON a.user_id = u.id
                  ORDER BY a.created_at DESC";
        return $this->conn->query($query);
    }
    
    // Read audit logs for specific entry
    public function readByEntry($entry_id) {
        $entry_id = intval($entry_id);
        $query = "SELECT a.*, u.username FROM " . $this->table_name . " a
                  LEFT JOIN users u ON a.user_id = u.id
                  WHERE a.entry_id = {$entry_id}
                  ORDER BY a.created_at DESC";
        return $this->conn->query($query);
    }
}
?>