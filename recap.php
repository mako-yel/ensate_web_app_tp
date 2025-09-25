<?php
session_start();

// Initialize data arrays in session
if (!isset($_SESSION['students'])) {
    $_SESSION['students'] = [
        [
            'nom' => 'Alami', 'prenom' => 'Ahmed', 'age' => 22, 'telephone' => '0661234567',
            'email' => 'ahmed.alami@etu.ensa.ac.ma', 'filiere' => 'Génie Informatique',
            'annee' => '2ème année', 'modules' => 'Java, PHP, MySQL',
            'projets_realises' => 'Site web e-commerce', 'projets_stages' => 'Application mobile',
            'centres_interet' => 'Intelligence Artificielle, Développement Web',
            'langues' => 'Arabe, Français, Anglais', 'remarques' => 'Étudiant motivé', 'photo' => ''
        ],
        [
            'nom' => 'Benjelloun', 'prenom' => 'Fatima', 'age' => 21, 'telephone' => '0677890123',
            'email' => 'fatima.benjelloun@etu.ensa.ac.ma', 'filiere' => 'Génie Civil',
            'annee' => '3ème année', 'modules' => 'AutoCAD, Structure, Béton armé',
            'projets_realises' => 'Conception d\'un pont', 'projets_stages' => 'Bureau d\'études',
            'centres_interet' => 'Architecture, BIM', 'langues' => 'Arabe, Français, Espagnol',
            'remarques' => 'Excellente en conception', 'photo' => ''
        ]
    ];
}

if (!isset($_SESSION['companies'])) {
    $_SESSION['companies'] = [
        ['id' => 1, 'name' => 'TechnoSoft Maroc', 'sector' => 'Technologies de l\'information', 'city' => 'Tétouan', 'email' => 'rh@technosoft.ma'],
        ['id' => 2, 'name' => 'Build & Co', 'sector' => 'Construction', 'city' => 'Tanger', 'email' => 'stages@buildco.ma']
    ];
}

if (!isset($_SESSION['internships'])) {
    $_SESSION['internships'] = [];
}

// Handle form submissions
$success = '';
$error = '';

// Check for success message from form submission
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $success = "Nouvel étudiant ajouté avec succès dans la base de données!";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_company'])) {
        if (!empty($_POST['name']) && !empty($_POST['sector']) && !empty($_POST['city'])) {
            $company = [
                'id' => time(),
                'name' => htmlspecialchars($_POST['name']),
                'sector' => htmlspecialchars($_POST['sector']),
                'city' => htmlspecialchars($_POST['city']),
                'email' => htmlspecialchars($_POST['email'] ?? '')
            ];
            $_SESSION['companies'][] = $company;
            $success = "Entreprise ajoutée avec succès!";
        } else {
            $error = "Veuillez remplir tous les champs obligatoires.";
        }
    }
    
    if (isset($_POST['assign_internship'])) {
        $email = $_POST['student_email'];
        $student = null;
        foreach ($_SESSION['students'] as $s) {
            if (strtolower(trim($s['email'])) === strtolower(trim($email))) {
                $student = $s;
                break;
            }
        }
        
        if ($student && !empty($_POST['company_id'])) {
            $company = null;
            foreach ($_SESSION['companies'] as $c) {
                if ($c['id'] == $_POST['company_id']) {
                    $company = $c;
                    break;
                }
            }
            
            if ($company) {
                // Vérifier si l'étudiant a déjà un stage affecté
                $already_has_internship = false;
                foreach ($_SESSION['internships'] as $internship) {
                    if (strtolower(trim($internship['student_email'])) === strtolower(trim($email))) {
                        $already_has_internship = true;
                        break;
                    }
                }
                
                if ($already_has_internship) {
                    $error = "Cet étudiant a déjà un stage affecté!";
                } else {
                    $_SESSION['internships'][] = [
                        'id' => time(),
                        'student_email' => $email,
                        'student_name' => $student['prenom'] . ' ' . $student['nom'],
                        'company_name' => $company['name'],
                        'position' => htmlspecialchars($_POST['position'] ?? 'Stagiaire'),
                        'duration' => htmlspecialchars($_POST['duration'] ?? 'Non spécifié'),
                        'date' => date('d/m/Y')
                    ];
                    $success = "Stage affecté avec succès!";
                }
            }
        } else {
            if (!$student) {
                $error = "Aucun étudiant trouvé avec l'email: " . htmlspecialchars($email) . ". Vérifiez l'adresse email ou ajoutez d'abord l'étudiant.";
            } else {
                $error = "Entreprise non sélectionnée!";
            }
        }
    }
}

// Search functionality
$search_result = [];
$searched = false;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $email = strtolower(trim($_GET['search']));
    foreach ($_SESSION['students'] as $student) {
        if (strpos(strtolower($student['email']), $email) !== false) {
            $search_result[] = $student;
        }
    }
    $searched = true;
}

$students = $_SESSION['students'];
$companies = $_SESSION['companies'];
$internships = $_SESSION['internships'];
$active_tab = $_GET['tab'] ?? 'database';

// Function to render student card
function renderStudentCard($student) {
    echo '<div class="student-card">
        <div class="student-header">
            <div class="student-name">' . htmlspecialchars($student['prenom'] . ' ' . $student['nom']) . '</div>
            <div style="color: #3498db; font-weight: 600;">' . htmlspecialchars($student['filiere']) . '</div>
        </div>
        <div class="student-info">
            <div class="info-item">
                <span class="info-label">Email:</span>
                <span>' . htmlspecialchars($student['email']) . '</span>
            </div>
            <div class="info-item">
                <span class="info-label">Âge:</span>
                <span>' . htmlspecialchars($student['age']) . ' ans</span>
            </div>
            <div class="info-item">
                <span class="info-label">Téléphone:</span>
                <span>' . htmlspecialchars($student['telephone']) . '</span>
            </div>
            <div class="info-item">
                <span class="info-label">Année:</span>
                <span>' . htmlspecialchars($student['annee']) . '</span>
            </div>
            <div class="info-item">
                <span class="info-label">Modules:</span>
                <span>' . htmlspecialchars($student['modules']) . '</span>
            </div>
            <div class="info-item">
                <span class="info-label">Projets:</span>
                <span>' . htmlspecialchars($student['projets_realises']) . '</span>
            </div>
            <div class="info-item">
                <span class="info-label">Langues:</span>
                <span>' . htmlspecialchars($student['langues']) . '</span>
            </div>
            <div class="info-item">
                <span class="info-label">Centres d\'intérêt:</span>
                <span>' . htmlspecialchars($student['centres_interet']) . '</span>
            </div>
        </div>
    </div>';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Gestion des Étudiants - ENSA Tétouan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh; padding: 20px;
        }
        .container {
            max-width: 1200px; margin: 0 auto; background: white;
            border-radius: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); overflow: hidden;
        }
        .header {
            background: linear-gradient(45deg, #2c3e50, #34495e);
            color: white; padding: 30px; text-align: center;
        }
        .header h1 { font-size: 2.5em; margin-bottom: 10px; }
        .header p { font-size: 1.2em; opacity: 0.9; }
        .tabs { display: flex; background: #f8f9fa; border-bottom: 1px solid #dee2e6; }
        .tab-button {
            flex: 1; padding: 20px; background: none; border: none;
            cursor: pointer; font-size: 1.1em; font-weight: 600; text-decoration: none;
            color: inherit; display: block; text-align: center; transition: all 0.3s ease;
        }
        .tab-button:hover { background: #e9ecef; }
        .tab-button.active { background: #007bff; color: white; }
        .tab-content { padding: 30px; }
        .form-section {
            background: #f8f9fa; padding: 25px; border-radius: 10px; margin-bottom: 25px;
        }
        .form-section h3 {
            color: #2c3e50; margin-bottom: 20px; font-size: 1.4em;
            border-bottom: 2px solid #3498db; padding-bottom: 10px;
        }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .form-group { display: flex; flex-direction: column; }
        label { font-weight: 600; margin-bottom: 5px; color: #34495e; }
        input, select { 
            padding: 12px; border: 2px solid #e1e8ed; border-radius: 8px;
            font-size: 16px; transition: border-color 0.3s ease;
        }
        input:focus, select:focus { outline: none; border-color: #3498db; }
        button {
            background: linear-gradient(45deg, #3498db, #2980b9); color: white;
            border: none; padding: 15px 30px; border-radius: 8px;
            font-size: 16px; font-weight: 600; cursor: pointer; transition: transform 0.2s ease;
        }
        button:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4); }
        .student-card {
            background: white; border: 1px solid #e1e8ed; border-radius: 10px;
            padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        .student-card:hover { transform: translateY(-5px); box-shadow: 0 5px 20px rgba(0,0,0,0.15); }
        .student-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;
        }
        .student-name { font-size: 1.3em; font-weight: bold; color: #2c3e50; }
        .student-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 10px; }
        .info-item {
            display: flex; justify-content: space-between; padding: 5px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        .info-label { font-weight: 600; color: #7f8c8d; }
        .stats {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px; margin-bottom: 25px;
        }
        .stat-card {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white; padding: 20px; border-radius: 10px; text-align: center;
        }
        .stat-number { font-size: 2em; font-weight: bold; }
        .stat-label { font-size: 0.9em; opacity: 0.9; }
        .alert {
            padding: 15px; border-radius: 8px; margin-bottom: 20px;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .search-box {
            width: 100%; padding: 15px; font-size: 16px;
            border: 2px solid #3498db; border-radius: 10px; margin-bottom: 20px;
        }
        .add-student-button {
            background: linear-gradient(45deg, #27ae60, #229954);
            color: white; padding: 15px 30px; border-radius: 8px;
            text-decoration: none; display: inline-block; font-weight: 600;
            margin-bottom: 20px; transition: transform 0.2s ease;
        }
        .add-student-button:hover {
            transform: translateY(-2px); box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }
        @media (max-width: 768px) {
            .tabs { flex-direction: column; }
            .form-grid { grid-template-columns: 1fr; }
            .student-header { flex-direction: column; align-items: flex-start; gap: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 ENSA Tétouan</h1>
            <p>Système de Gestion des Étudiants et Stages</p>
        </div>

        <div class="tabs">
            <a href="?tab=database" class="tab-button <?= $active_tab === 'database' ? 'active' : '' ?>">
                📊 Base de Données
            </a>
            <a href="?tab=company" class="tab-button <?= $active_tab === 'company' ? 'active' : '' ?>">
                🏢 Gestion des Stages
            </a>
        </div>

        <div class="tab-content">
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($active_tab === 'database'): ?>
                <!-- Add Student Button -->
                <a href="index.php" class="add-student-button">➕ Ajouter un Nouvel Étudiant</a>

                <div class="form-section">
                    <h3>🔍 Recherche d'Étudiant</h3>
                    <form method="GET">
                        <input type="hidden" name="tab" value="database">
                        <input type="email" name="search" class="search-box" 
                               placeholder="Rechercher par email..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button type="submit">Rechercher</button>
                    </form>
                    
                    <?php if ($searched): ?>
                        <?php if (count($search_result) > 0): ?>
                            <div class="alert alert-success">
                                <strong>Étudiant(s) trouvé(s):</strong>
                            </div>
                            <?php foreach ($search_result as $student): ?>
                                <?php renderStudentCard($student); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-error">Aucun étudiant trouvé avec cet email.</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="stats">
                    <div class="stat-card">
                        <div class="stat-number"><?= count($students) ?></div>
                        <div class="stat-label">Étudiants Total</div>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(45deg, #27ae60, #229954)">
                        <div class="stat-number"><?= count(array_unique(array_column($students, 'filiere'))) ?></div>
                        <div class="stat-label">Filières</div>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(45deg, #f39c12, #e67e22)">
                        <div class="stat-number"><?= count($internships) ?></div>
                        <div class="stat-label">Stages Affectés</div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>👥 Liste des Étudiants</h3>
                    <?php foreach ($students as $student): ?>
                        <?php renderStudentCard($student); ?>
                    <?php endforeach; ?>
                </div>

            <?php elseif ($active_tab === 'company'): ?>
                <div class="form-section">
                    <h3>🏢 Ajouter une Entreprise</h3>
                    <form method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Nom de l'entreprise *</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Secteur d'activité *</label>
                                <input type="text" name="sector" required>
                            </div>
                            <div class="form-group">
                                <label>Ville *</label>
                                <input type="text" name="city" required>
                            </div>
                            <div class="form-group">
                                <label>Email contact</label>
                                <input type="email" name="email">
                            </div>
                        </div>
                        <button type="submit" name="add_company">Ajouter Entreprise</button>
                    </form>
                </div>

                <div class="form-section">
                    <h3>🎯 Affecter un Stage</h3>
                    <form method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Email de l'étudiant *</label>
                                <input type="email" name="student_email" required>
                            </div>
                            <div class="form-group">
                                <label>Entreprise *</label>
                                <select name="company_id" required>
                                    <option value="">Sélectionner une entreprise</option>
                                    <?php foreach ($companies as $company): ?>
                                        <option value="<?= $company['id'] ?>"><?= htmlspecialchars($company['name']) ?> (<?= htmlspecialchars($company['city']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Poste de stage</label>
                                <input type="text" name="position">
                            </div>
                            <div class="form-group">
                                <label>Durée (mois)</label>
                                <input type="number" name="duration" min="1" max="12">
                            </div>
                        </div>
                        <button type="submit" name="assign_internship">Affecter le Stage</button>
                    </form>
                </div>

                <div class="form-section">
                    <h3>📋 Gestion des Candidatures</h3>
                    <h4>🏢 Entreprises Partenaires (<?= count($companies) ?>)</h4>
                    <?php foreach ($companies as $company): ?>
                        <div class="student-card">
                            <div class="student-header">
                                <div class="student-name"><?= htmlspecialchars($company['name']) ?></div>
                                <div style="color: #e74c3c; font-weight: 600;"><?= htmlspecialchars($company['sector']) ?></div>
                            </div>
                            <div class="student-info">
                                <div class="info-item">
                                    <span class="info-label">Ville:</span>
                                    <span><?= htmlspecialchars($company['city']) ?></span>
                                </div>
                                <?php if (!empty($company['email'])): ?>
                                    <div class="info-item">
                                        <span class="info-label">Email:</span>
                                        <span><?= htmlspecialchars($company['email']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <h4 style="margin-top: 20px;">🎯 Stages Affectés (<?= count($internships) ?>)</h4>
                    <?php if (count($internships) === 0): ?>
                        <p style="text-align: center; color: #7f8c8d;">Aucun stage affecté pour le moment.</p>
                    <?php else: ?>
                        <?php foreach ($internships as $internship): ?>
                            <div class="student-card">
                                <div class="student-header">
                                    <div class="student-name"><?= htmlspecialchars($internship['student_name']) ?></div>
                                    <div style="color: #27ae60; font-weight: 600;"><?= htmlspecialchars($internship['company_name']) ?></div>
                                </div>
                                <div class="student-info">
                                    <div class="info-item">
                                        <span class="info-label">Email:</span>
                                        <span><?= htmlspecialchars($internship['student_email']) ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Poste:</span>
                                        <span><?= htmlspecialchars($internship['position']) ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Durée:</span>
                                        <span><?= htmlspecialchars($internship['duration']) ?> mois</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Date d'affectation:</span>
                                        <span><?= htmlspecialchars($internship['date']) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>