<?php
// models/Entry.php
class Entry {
    // Database connection and table name
    private $conn;
    private $table_name = "entries";
    
    // Object properties
    public $id;
    public $account;
    public $narration;
    public $currency;
    public $credit;
    public $debit;
    public $user_id;
    public $created_at;
    public $updated_at;
    
    // Constructor with database connection
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create entry
    public function create() {
        // Sanitize inputs
        $this->account = $this->sanitize($this->account);
        $this->narration = $this->sanitize($this->narration);
        $this->currency = $this->sanitize($this->currency);
        $this->credit = floatval($this->credit);
        $this->debit = floatval($this->debit);
        $this->user_id = intval($this->user_id);
        
        // Create query
        $query = "INSERT INTO " . $this->table_name . " 
                 (account, narration, currency, credit, debit, user_id) 
                 VALUES ('{$this->account}', '{$this->narration}', '{$this->currency}', 
                 {$this->credit}, {$this->debit}, {$this->user_id})";
        
        // Execute query
        if ($this->conn->query($query)) {
            $this->id = $this->conn->insert_id;
            
            // Add audit logs
            $this->addAudit('entries', 'account', '', $this->account, $this->id);
            $this->addAudit('entries', 'narration', '', $this->narration, $this->id);
            $this->addAudit('entries', 'currency', '', $this->currency, $this->id);
            $this->addAudit('entries', 'credit', '', $this->credit, $this->id);
            $this->addAudit('entries', 'debit', '', $this->debit, $this->id);
            
            return true;
        }
        
        return false;
    }
    
    // Read all entries
    public function readAll() {
        $query = "SELECT e.*, u.username FROM " . $this->table_name . " e
                  LEFT JOIN users u ON e.user_id = u.id
                  ORDER BY e.created_at DESC";
        return $this->conn->query($query);
    }
    
    // Read one entry
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = " . intval($this->id);
        $result = $this->conn->query($query);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->account = $row['account'];
            $this->narration = $row['narration'];
            $this->currency = $row['currency'];
            $this->credit = $row['credit'];
            $this->debit = $row['debit'];
            $this->user_id = $row['user_id'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        
        return false;
    }
    
    // Update entry
    public function update() {
        // Sanitize inputs
        $this->id = intval($this->id);
        $this->account = $this->sanitize($this->account);
        $this->narration = $this->sanitize($this->narration);
        $this->currency = $this->sanitize($this->currency);
        $this->credit = floatval($this->credit);
        $this->debit = floatval($this->debit);
        
        // Get current entry data for audit
        $current_query = "SELECT * FROM " . $this->table_name . " WHERE id = " . $this->id;
        $current_result = $this->conn->query($current_query);
        $current_data = $current_result->fetch_assoc();
        
        // Update query
        $query = "UPDATE " . $this->table_name . " SET 
                  account = '{$this->account}',
                  narration = '{$this->narration}',
                  currency = '{$this->currency}',
                  credit = {$this->credit},
                  debit = {$this->debit}
                  WHERE id = {$this->id}";
        
        // Execute query
        if ($this->conn->query($query)) {
            // Add audit logs for changed fields
            if ($current_data['account'] != $this->account) {
                $this->addAudit('entries', 'account', $current_data['account'], $this->account, $this->id);
            }
            if ($current_data['narration'] != $this->narration) {
                $this->addAudit('entries', 'narration', $current_data['narration'], $this->narration, $this->id);
            }
            if ($current_data['currency'] != $this->currency) {
                $this->addAudit('entries', 'currency', $current_data['currency'], $this->currency, $this->id);
            }
            if ($current_data['credit'] != $this->credit) {
                $this->addAudit('entries', 'credit', $current_data['credit'], $this->credit, $this->id);
            }
            if ($current_data['debit'] != $this->debit) {
                $this->addAudit('entries', 'debit', $current_data['debit'], $this->debit, $this->id);
            }
            
            return true;
        }
        
        return false;
    }
    
    // Delete entry
    public function delete() {
        $this->id = intval($this->id);
        
        // Get current entry data for audit
        $current_query = "SELECT * FROM " . $this->table_name . " WHERE id = " . $this->id;
        $current_result = $this->conn->query($current_query);
        $current_data = $current_result->fetch_assoc();
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id = " . $this->id;
        
        if ($this->conn->query($query)) {
            // Add audit logs
            $this->addAudit('entries', 'account', $current_data['account'], 'deleted', $this->id);
            $this->addAudit('entries', 'narration', $current_data['narration'], 'deleted', $this->id);
            $this->addAudit('entries', 'currency', $current_data['currency'], 'deleted', $this->id);
            $this->addAudit('entries', 'credit', $current_data['credit'], 'deleted', $this->id);
            $this->addAudit('entries', 'debit', $current_data['debit'], 'deleted', $this->id);
            
            return true;
        }
        
        return false;
    }
    
    // Get statistics for dashboard
    public function getStats() {
        $stats = [];
        
        // Total entries
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        $stats['total_entries'] = $row['total'];
        
        // Total debit
        $query = "SELECT SUM(debit) as total_debit FROM " . $this->table_name;
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        $stats['total_debit'] = $row['total_debit'] ?: 0;
        
        // Total credit
        $query = "SELECT SUM(credit) as total_credit FROM " . $this->table_name;
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        $stats['total_credit'] = $row['total_credit'] ?: 0;
        
        // Entries by currency
        $query = "SELECT currency, COUNT(*) as count FROM " . $this->table_name . " GROUP BY currency";
        $result = $this->conn->query($query);
        $stats['entries_by_currency'] = [];
        
        while ($row = $result->fetch_assoc()) {
            $stats['entries_by_currency'][] = $row;
        }
        
        return $stats;
    }
    
    // Get entries for chart
    public function getChartData() {
        $query = "SELECT DATE(created_at) as date, SUM(credit) as total_credit, SUM(debit) as total_debit 
                  FROM " . $this->table_name . "
                  GROUP BY DATE(created_at) 
                  ORDER BY DATE(created_at) ASC
                  LIMIT 10";
        
        $result = $this->conn->query($query);
        $data = [];
        
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        return $data;
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
}
?>