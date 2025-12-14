<?php
// classes/Category.php
require_once 'Database.php';

class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $sql = "SELECT * FROM categories ORDER BY category_name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function add($name) {
        $sql = "INSERT INTO categories (category_name) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name]);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM categories WHERE category_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM categories";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }
}
?>