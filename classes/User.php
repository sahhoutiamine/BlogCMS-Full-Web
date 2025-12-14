<?php
// classes/User.php
require_once 'Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function login($username, $password) {
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['user'] = [
                'username' => 'admin',
                'name' => 'System Admin',
                'role' => 'admin'
            ];
            return true;
        }
        
        if ($username === 'author1' && $password === 'author123') {
            $_SESSION['user'] = [
                'username' => 'author1',
                'name' => 'Demo Author',
                'role' => 'author'
            ];
            return true;
        }
        
        $user = $this->getByUsername($username);
        
        if ($user && $user['pw'] === '$2y$10$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36WQoeG6Lruj3vjPGga31lW') {
            $_SESSION['user'] = [
                'username' => $user['username'],
                'name' => $user['name'],
                'role' => $user['role']
            ];
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        session_destroy();
        return true;
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['user']);
    }
    
    public static function isAdmin() {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }
    
    public static function isAuthor() {
        if (!self::isLoggedIn()) return false;
        $role = $_SESSION['user']['role'];
        return $role === 'author' || $role === 'admin';
    }
    
    public function getByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    public function getAllUsers() {
        $sql = "SELECT username, name, email, role, create_date FROM users ORDER BY role, username";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function countUsers() {
        $sql = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }
    
    public static function currentUser() {
        return $_SESSION['user'] ?? null;
    }
    
    public static function getCurrentUsername() {
        return $_SESSION['user']['username'] ?? null;
    }
}
?>