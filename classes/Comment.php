<?php
// classes/Comment.php
require_once 'Database.php';

class Comment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function add($content, $author_username, $article_id, $type = 'normal') {
        $sql = "INSERT INTO comments (content, author_username, article_id, type) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$content, $author_username, $article_id, $type]);
    }
    
    public function getByArticle($article_id) {
        $sql = "SELECT c.*, 
                COALESCE(CONCAT(u.name, ' ', u.last_name), u.name, c.author_username) as display_name
                FROM comments c 
                LEFT JOIN users u ON c.author_username = u.username 
                WHERE c.article_id = ? 
                ORDER BY c.create_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$article_id]);
        return $stmt->fetchAll();
    }
    
    public function delete($id) {
        $sql = "DELETE FROM comments WHERE comment_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    public function deleteByArticle($article_id) {
        $sql = "DELETE FROM comments WHERE article_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$article_id]);
    }
    
    public function getAll($limit = 50) {
        $sql = "SELECT c.*, 
                COALESCE(CONCAT(u.name, ' ', u.last_name), u.name, c.author_username) as display_name,
                a.title as article_title 
                FROM comments c 
                LEFT JOIN users u ON c.author_username = u.username 
                LEFT JOIN articles a ON c.article_id = a.article_id 
                ORDER BY c.create_date DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM comments";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }
    
    public function countByArticle($article_id) {
        $sql = "SELECT COUNT(*) as total FROM comments WHERE article_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$article_id]);
        return $stmt->fetch()['total'];
    }
}
?>