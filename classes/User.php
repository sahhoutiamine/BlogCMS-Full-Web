<?php
// classes/User.php
require_once 'Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function login($username, $password) {
        $user = $this->getByUsername($username);
        
        // Check if user exists and password matches exactly
        if ($user && $password === $user['pw']) {
            $_SESSION['user'] = [
                'username' => $user['username'],
                'name' => $this->getFullName($user),
                'role' => $user['role']
            ];
            return true;
        }
        
        return false;
    }
    
    private function getFullName($user) {
        $name = trim($user['name'] ?? '');
        $lastName = trim($user['last_name'] ?? '');
        
        if ($name && $lastName) {
            return $name . ' ' . $lastName;
        } elseif ($name) {
            return $name;
        } else {
            return $user['username'];
        }
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
    
    public function getFirstThreeUsers() {
        $sql = "SELECT username, name, last_name, role, email, pw FROM users ORDER BY username LIMIT 3";
        $stmt = $this->db->query($sql);
        $users = $stmt->fetchAll();
        
        // Add full name to each user
        foreach ($users as &$user) {
            $user['full_name'] = $this->getFullName($user);
        }
        
        return $users;
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