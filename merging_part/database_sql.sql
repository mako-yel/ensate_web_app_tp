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
    password VARCHAR(255), -- For company login
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for internship offers posted by companies
CREATE TABLE IF NOT EXISTS internship_offers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT,
    duration VARCHAR(50) DEFAULT '3 mois',
    location VARCHAR(100),
    posted_date DATE DEFAULT (CURRENT_DATE),
    deadline DATE,
    status ENUM('active', 'closed') DEFAULT 'active',
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Table for student applications to internship offers
CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    offer_id INT NOT NULL,
    cover_letter TEXT,
    application_date DATE DEFAULT (CURRENT_DATE),
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    company_notes TEXT,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (offer_id) REFERENCES internship_offers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (student_id, offer_id)
);

-- Insert sample data
INSERT INTO students (nom, prenom, age, telephone, email, filiere, annee, modules, projets_realises, projets_stages, centres_interet, langues, remarques) VALUES
('Alami', 'Ahmed', 22, '0661234567', 'ahmed.alami@etu.ensa.ac.ma', 'GÃ©nie Informatique', '2Ã¨me annÃ©e', 'Java, PHP, MySQL', 'Site web e-commerce', 'Application mobile', 'Intelligence Artificielle, DÃ©veloppement Web', 'Arabe, FranÃ§ais, Anglais', 'Ã‰tudiant motivÃ©'),
('Benjelloun', 'Fatima', 21, '0677890123', 'fatima.benjelloun@etu.ensa.ac.ma', 'GÃ©nie Civil', '3Ã¨me annÃ©e', 'AutoCAD, Structure, BÃ©ton armÃ©', 'Conception d\'un pont', 'Bureau d\'Ã©tudes', 'Architecture, BIM', 'Arabe, FranÃ§ais, Espagnol', 'Excellente en conception');

INSERT INTO companies (name, sector, city, email, password) VALUES
('TechnoSoft Maroc', 'Technologies de l\'information', 'TÃ©touan', 'rh@technosoft.ma', MD5('password123')),
('Build & Co', 'Construction', 'Tanger', 'stages@buildco.ma', MD5('password123')),
('Digital Solutions', 'DÃ©veloppement Web', 'Rabat', 'contact@digitalsol.ma', MD5('password123'));

INSERT INTO internship_offers (company_id, title, description, requirements, duration, location, deadline) VALUES
(1, 'Stage DÃ©veloppement Web', 'DÃ©veloppement d\'applications web avec PHP/MySQL', 'Connaissances en PHP, HTML/CSS, JavaScript', '4 mois', 'TÃ©touan', '2024-03-15'),
(2, 'Stage Bureau d\'Ã‰tudes', 'Assistance dans les projets de construction', 'MaÃ®trise AutoCAD, notions BTP', '6 mois', 'Tanger', '2024-02-28'),
(3, 'Stage DÃ©veloppement Mobile', 'CrÃ©ation d\'applications mobiles', 'Java, Android Studio', '3 mois', 'Rabat', '2024-04-10');