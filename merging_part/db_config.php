<?php
// Enhanced Database Configuration for Complete Application Management
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
            die("Database Error: " . $e->getMessage());
        }
    }
    
    // ===== STUDENT OPERATIONS (Part 1) =====
    public function getAllStudents() {
        $stmt = $this->conn->query("SELECT * FROM students ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function searchStudentsByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE email LIKE ?");
        $stmt->execute(['%' . $email . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getStudentByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function addStudent($data) {
        $sql = "INSERT INTO students (nom, prenom, age, telephone, email, filiere, annee, modules, projets_realises, projets_stages, centres_interet, langues, remarques, photo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['nom'], $data['prenom'], $data['age'], $data['telephone'],
            $data['email'], $data['filiere'], $data['annee'], $data['modules'],
            $data['projets_realises'], $data['projets_stages'], 
            $data['centres_interet'], $data['langues'], $data['remarques'], $data['photo'] ?? ''
        ]);
    }
    
    // ===== COMPANY OPERATIONS (Part 3) =====
    public function getAllCompanies() {
        $stmt = $this->conn->query("SELECT * FROM companies ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addCompany($name, $sector, $city, $email = '', $password = '') {
        $hash_password = !empty($password) ? md5($password) : '';
        $stmt = $this->conn->prepare("INSERT INTO companies (name, sector, city, email, password) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $sector, $city, $email, $hash_password]);
    }
    
    public function authenticateCompany($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM companies WHERE email = ? AND password = ?");
        $stmt->execute([$email, md5($password)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // ===== INTERNSHIP OFFERS (Facultative) =====
    public function addInternshipOffer($company_id, $title, $description, $requirements, $duration, $location, $deadline) {
        $stmt = $this->conn->prepare("INSERT INTO internship_offers (company_id, title, description, requirements, duration, location, deadline) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$company_id, $title, $description, $requirements, $duration, $location, $deadline]);
    }
    
    public function getActiveOffers() {
        $stmt = $this->conn->query("
            SELECT o.*, c.name as company_name, c.city as company_city 
            FROM internship_offers o 
            JOIN companies c ON o.company_id = c.id 
            WHERE o.status = 'active' AND o.deadline >= CURDATE()
            ORDER BY o.posted_date DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCompanyOffers($company_id) {
        $stmt = $this->conn->prepare("SELECT * FROM internship_offers WHERE company_id = ? ORDER BY posted_date DESC");
        $stmt->execute([$company_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ===== APPLICATIONS (Facultative) =====
    public function submitApplication($student_email, $offer_id, $cover_letter) {
        $student = $this->getStudentByEmail($student_email);
        if (!$student) return false;
        
        $stmt = $this->conn->prepare("SELECT id FROM applications WHERE student_id = ? AND offer_id = ?");
        $stmt->execute([$student['id'], $offer_id]);
        if ($stmt->fetch()) return 'duplicate';
        
        $stmt = $this->conn->prepare("INSERT INTO applications (student_id, offer_id, cover_letter) VALUES (?, ?, ?)");
        return $stmt->execute([$student['id'], $offer_id, $cover_letter]);
    }
    
    public function getCompanyApplications($company_id) {
        $stmt = $this->conn->prepare("
            SELECT a.*, s.nom, s.prenom, s.email, s.telephone, s.filiere, s.annee, 
                   o.title as offer_title, s.modules, s.projets_realises, s.langues
            FROM applications a
            JOIN students s ON a.student_id = s.id
            JOIN internship_offers o ON a.offer_id = o.id
            WHERE o.company_id = ?
            ORDER BY a.application_date DESC
        ");
        $stmt->execute([$company_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateApplicationStatus($application_id, $status, $notes = '') {
        $stmt = $this->conn->prepare("UPDATE applications SET status = ?, company_notes = ? WHERE id = ?");
        return $stmt->execute([$status, $notes, $application_id]);
    }
    
    public function getStudentApplications($student_email) {
        $student = $this->getStudentByEmail($student_email);
        if (!$student) return [];
        
        $stmt = $this->conn->prepare("
            SELECT a.*, o.title, o.description, c.name as company_name, c.city
            FROM applications a
            JOIN internship_offers o ON a.offer_id = o.id
            JOIN companies c ON o.company_id = c.id
            WHERE a.student_id = ?
            ORDER BY a.application_date DESC
        ");
        $stmt->execute([$student['id']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ===== STATISTICS =====
    public function getStats() {
        $stats = [];
        
        $stmt = $this->conn->query("SELECT COUNT(*) as count FROM students");
        $stats['total_students'] = $stmt->fetch()['count'];
        
        $stmt = $this->conn->query("SELECT COUNT(*) as count FROM companies");
        $stats['total_companies'] = $stmt->fetch()['count'];
        
        $stmt = $this->conn->query("SELECT COUNT(*) as count FROM internship_offers WHERE status = 'active'");
        $stats['active_offers'] = $stmt->fetch()['count'];
        
        $stmt = $this->conn->query("SELECT COUNT(*) as count FROM applications");
        $stats['total_applications'] = $stmt->fetch()['count'];
        
        $stmt = $this->conn->query("SELECT COUNT(DISTINCT filiere) as count FROM students");
        $stats['unique_filieres'] = $stmt->fetch()['count'];
        
        return $stats;
    }
}
?>