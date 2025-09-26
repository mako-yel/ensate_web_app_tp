CREATE DATABASE IF NOT EXISTS ensa_students_em;
USE ensa_students_em;

-- Table for students
CREATE TABLE IF NOT EXISTS students (
    email VARCHAR(150) PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    telephone VARCHAR(20) NOT NULL,
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

-- Table for companies (email as primary key)
CREATE TABLE IF NOT EXISTS companies (
    email VARCHAR(150) PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    sector VARCHAR(150) NOT NULL,
    city VARCHAR(100) NOT NULL,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for internship offers (reference company by email)
CREATE TABLE IF NOT EXISTS internship_offers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_email VARCHAR(150) NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT,
    duration VARCHAR(50) DEFAULT '3 mois',
    location VARCHAR(100),
    posted_date DATE DEFAULT (CURRENT_DATE),
    deadline DATE,
    status ENUM('active', 'closed') DEFAULT 'active',
    FOREIGN KEY (company_email) REFERENCES companies(email) ON DELETE CASCADE
);

-- Table for student applications (reference student and company by email)
CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_email VARCHAR(150) NOT NULL,
    offer_id INT NOT NULL,
    cover_letter TEXT,
    application_date DATE DEFAULT (CURRENT_DATE),
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    company_notes TEXT,
    FOREIGN KEY (student_email) REFERENCES students(email) ON DELETE CASCADE,
    FOREIGN KEY (offer_id) REFERENCES internship_offers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (student_email, offer_id)
);

-- Insert sample data
INSERT INTO students (email, nom, prenom, age, telephone, filiere, annee, modules, projets_realises, projets_stages, centres_interet, langues, remarques) VALUES
('ahmed.alami@etu.ensa.ac.ma', 'Alami', 'Ahmed', 22, '0661234567', 'Génie Informatique', '2ème année', 'Java, PHP, MySQL', 'Site web e-commerce', 'Application mobile', 'Intelligence Artificielle, Développement Web', 'Arabe, Français, Anglais', 'Étudiant motivé'),
('fatima.benjelloun@etu.ensa.ac.ma', 'Benjelloun', 'Fatima', 21, '0677890123', 'Génie Civil', '3ème année', 'AutoCAD, Structure, Béton armé', 'Conception d\'un pont', 'Bureau d\'études', 'Architecture, BIM', 'Arabe, Français, Espagnol', 'Excellente en conception');

INSERT INTO companies (email, name, sector, city, password) VALUES
('rh@technosoft.ma', 'TechnoSoft Maroc', 'Technologies de l\'information', 'Tétouan', MD5('password123')),
('stages@buildco.ma', 'Build & Co', 'Construction', 'Tanger', MD5('password123')),
('contact@digitalsol.ma', 'Digital Solutions', 'Développement Web', 'Rabat', MD5('password123'));

INSERT INTO internship_offers (company_email, title, description, requirements, duration, location, deadline) VALUES
('rh@technosoft.ma', 'Stage Développement Web', 'Développement d\'applications web avec PHP/MySQL', 'Connaissances en PHP, HTML/CSS, JavaScript', '4 mois', 'Tétouan', '2024-03-15'),
('stages@buildco.ma', 'Stage Bureau d\'Études', 'Assistance dans les projets de construction', 'Maîtrise AutoCAD, notions BTP', '6 mois', 'Tanger', '2024-02-28'),
('contact@digitalsol.ma', 'Stage Développement Mobile', 'Création d\'applications mobiles', 'Java, Android Studio', '3 mois', 'Rabat', '2024-04-10');