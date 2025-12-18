<?php
// classes/Article.php
require_once 'Database.php';

class Article {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($title, $content, $author_username, $category_id, $status = 'Published') {
        $sql = "INSERT INTO articles (title, content, author_username, category_id, article_status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$title, $content, $author_username, $category_id, $status]);
    }
    
    public function getById($id) {
        $sql = "SELECT a.*, c.category_name, u.name as author_name FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                LEFT JOIN users u ON a.author_username = u.username 
                WHERE a.article_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getAll($category_id = null) {
        $sql = "SELECT a.*, c.category_name, u.name as author_name FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                LEFT JOIN users u ON a.author_username = u.username 
                WHERE a.article_status = 'Published'";
        
        $params = [];
        if ($category_id) {
            $sql .= " AND a.category_id = ?";
            $params[] = $category_id;
        }
        
        $sql .= " ORDER BY a.create_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function delete($id) {
        $sql = "DELETE FROM articles WHERE article_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function isAuthor($article_id, $username) {
        $sql = "SELECT author_username FROM articles WHERE article_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$article_id]);
        $article = $stmt->fetch();
        
        return $article && $article['author_username'] === $username;
    }
    
    // Add update method
    public function update($id, $title, $content, $category_id, $status) {
        $sql = "UPDATE articles SET title = ?, content = ?, category_id = ?, article_status = ?, modify_date = NOW() WHERE article_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$title, $content, $category_id, $status, $id]);
    }
    
    // Add method to get articles by author
    public function getByAuthor($username, $include_drafts = true) {
        $sql = "SELECT a.*, c.category_name FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                WHERE a.author_username = ?";
        
        if (!$include_drafts) {
            $sql .= " AND a.article_status = 'Published'";
        }
        
        $sql .= " ORDER BY a.create_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetchAll();
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM articles";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }
}
?>