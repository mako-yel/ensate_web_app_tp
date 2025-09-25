-- Database setup for Student Management System

CREATE DATABASE IF NOT EXISTS ensa_students;
USE ensa_students;

-- Table for students
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    filiere VARCHAR(100) NOT NULL,
    annee VARCHAR(50) NOT NULL,
    modules TEXT,
    projets_realises TEXT,
    projets_stages TEXT,
    centres_interet TEXT,
    langues TEXT,
    remarques TEXT,
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for companies
CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    sector VARCHAR(150) NOT NULL,
    city VARCHAR(100) NOT NULL,
    email VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for internships
CREATE TABLE IF NOT EXISTS internships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    company_id INT NOT NULL,
    position VARCHAR(150) DEFAULT 'Stagiaire',
    duration VARCHAR(50) DEFAULT 'Non spécifié',
    assigned_date DATE DEFAULT (CURRENT_DATE),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_internship (student_id)
);

-- Insert some sample data
INSERT INTO students (nom, prenom, age, telephone, email, filiere, annee, modules, projets_realises, projets_stages, centres_interet, langues, remarques) VALUES
('Alami', 'Ahmed', 22, '0661234567', 'ahmed.alami@etu.ensa.ac.ma', 'Génie Informatique', '2ème année', 'Java, PHP, MySQL', 'Site web e-commerce', 'Application mobile', 'Intelligence Artificielle, Développement Web', 'Arabe, Français, Anglais', 'Étudiant motivé'),
('Benjelloun', 'Fatima', 21, '0677890123', 'fatima.benjelloun@etu.ensa.ac.ma', 'Génie Civil', '3ème année', 'AutoCAD, Structure, Béton armé', 'Conception d\'un pont', 'Bureau d\'études', 'Architecture, BIM', 'Arabe, Français, Espagnol', 'Excellente en conception');

INSERT INTO companies (name, sector, city, email) VALUES
('TechnoSoft Maroc', 'Technologies de l\'information', 'Tétouan', 'rh@technosoft.ma'),
('Build & Co', 'Construction', 'Tanger', 'stages@buildco.ma'),
('Digital Solutions', 'Développement Web', 'Rabat', 'contact@digitalsol.ma'),
('Engineering Corp', 'Génie Civil', 'Casablanca', 'hr@engcorp.ma');