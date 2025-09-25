<?php
// Simple & Powerful Database Configuration
class Database {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'ensa_students';
    private $conn;
    
    public function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->database};charset=utf8",
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch(PDOException $e) {
            die("❌ Database Error: " . $e->getMessage());
        }
    }
    
    // Get all students
    public function getAllStudents() {
        $stmt = $this->conn->query("SELECT * FROM students ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Search students by email
    public function searchStudentsByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE email LIKE ?");
        $stmt->execute(['%' . $email . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get student by exact email
    public function getStudentByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Add new student
    public function addStudent($data) {
        $sql = "INSERT INTO students (nom, prenom, age, telephone, email, filiere, annee, modules, projets_realises, projets_stages, centres_interet, langues, remarques, photo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['nom'], $data['prenom'], $data['age'], $data['telephone'],
            $data['email'], $data['filiere'], $data['annee'], $data['modules'],
            $data['projets_realises'], $data['projets_stages'], 
            $data['centres_interet'], $data['langues'], $data['remarques'], $data['photo']
        ]);
    }
    
    // Get all companies
    public function getAllCompanies() {
        $stmt = $this->conn->query("SELECT * FROM companies ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Add company
    public function addCompany($name, $sector, $city, $email = '') {
        $stmt = $this->conn->prepare("INSERT INTO companies (name, sector, city, email) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $sector, $city, $email]);
    }
    
    // Get all internships with details
    public function getAllInternships() {
        $sql = "SELECT i.*, s.nom, s.prenom, s.email as student_email, c.name as company_name 
                FROM internships i 
                JOIN students s ON i.student_id = s.id 
                JOIN companies c ON i.company_id = c.id 
                ORDER BY i.created_at DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Assign internship
    public function assignInternship($student_email, $company_id, $position, $duration) {
        // Get student
        $student = $this->getStudentByEmail($student_email);
        if (!$student) return false;
        
        // Check if already has internship
        $stmt = $this->conn->prepare("SELECT id FROM internships WHERE student_id = ?");
        $stmt->execute([$student['id']]);
        if ($stmt->fetch()) return 'duplicate';
        
        // Insert internship
        $stmt = $this->conn->prepare("INSERT INTO internships (student_id, company_id, position, duration) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$student['id'], $company_id, $position, $duration]);
    }
    
    // Get statistics
    public function getStats() {
        $stats = [];
        
        // Students count
        $stmt = $this->conn->query("SELECT COUNT(*) as count FROM students");
        $stats['total_students'] = $stmt->fetch()['count'];
        
        // Companies count
        $stmt = $this->conn->query("SELECT COUNT(*) as count FROM companies");
        $stats['total_companies'] = $stmt->fetch()['count'];
        
        // Internships count
        $stmt = $this->conn->query("SELECT COUNT(*) as count FROM internships");
        $stats['total_internships'] = $stmt->fetch()['count'];
        
        // Unique fields count
        $stmt = $this->conn->query("SELECT COUNT(DISTINCT filiere) as count FROM students");
        $stats['unique_filieres'] = $stmt->fetch()['count'];
        
        return $stats;
    }
}