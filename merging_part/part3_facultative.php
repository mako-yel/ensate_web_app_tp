<?php
session_start();
require_once 'db_config.php';

$db = new Database();
$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_company'])) {
        if (!empty($_POST['name']) && !empty($_POST['sector']) && !empty($_POST['city'])) {
            try {
                $db->addCompany(
                    trim($_POST['name']),
                    trim($_POST['sector']),
                    trim($_POST['city']),
                    trim($_POST['email'] ?? ''),
                    trim($_POST['password'] ?? '')
                );
                $success = "Entreprise ajoutée avec succès!";
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $error = "Cette entreprise existe déjà.";
                } else {
                    $error = "Erreur lors de l'ajout de l'entreprise: " . $e->getMessage();
                }
            }
        } else {
            $error = "Veuillez remplir tous les champs obligatoires.";
        }
    }

    if (isset($_POST['add_offer'])) {
        if (!empty($_POST['company_id']) && !empty($_POST['title']) && !empty($_POST['description'])) {
            try {
                $db->addInternshipOffer(
                    $_POST['company_id'],
                    trim($_POST['title']),
                    trim($_POST['description']),
                    trim($_POST['requirements'] ?? ''),
                    trim($_POST['duration'] ?? '3 mois'),
                    trim($_POST['location'] ?? ''),
                    $_POST['deadline'] ?? date('Y-m-d', strtotime('+1 month'))
                );
                $success = "Offre de stage ajoutée avec succès!";
            } catch (Exception $e) {
                $error = "Erreur lors de l'ajout de l'offre: " . $e->getMessage();
            }
        } else {
            $error = "Veuillez remplir tous les champs obligatoires.";
        }
    }
}

// Search functionality
$search_result = [];
$searched = false;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $email = trim($_GET['search']);
    try {
        $search_result = $db->searchStudentsByEmail($email);
        $searched = true;
    } catch (Exception $e) {
        $error = "Erreur lors de la recherche: " . $e->getMessage();
    }
}

// Get data
try {
    $students = $db->getAllStudents();
    $companies = $db->getAllCompanies();
    $offers = $db->getActiveOffers();
    $stats = $db->getStats();
} catch (Exception $e) {
    $error = "Erreur de connexion à la base de données: " . $e->getMessage();
    $students = [];
    $companies = [];
    $offers = [];
    $stats = ['total_students' => 0, 'total_companies' => 0, 'active_offers' => 0, 'total_applications' => 0, 'unique_filieres' => 0];
}

$active_tab = $_GET['tab'] ?? 'students';

function renderStudentCard($student) {
    echo '<div class="card">
        <div class="card-header">
            <div class="student-name">' . htmlspecialchars($student['prenom'] . ' ' . $student['nom']) . '</div>
            <div class="filiere">' . htmlspecialchars($student['filiere']) . ' - ' . htmlspecialchars($student['annee']) . 'ème année</div>
            <div>
                <a href="formulaire_cv.php?email=' . urlencode($student['email']) . '" class="btn-cv">📄 Générer CV</a>
            </div>
        </div>
        <div class="card-info">
            <p><strong>📧 Email:</strong> ' . htmlspecialchars($student['email']) . '</p>
            <p><strong>📱 Téléphone:</strong> ' . htmlspecialchars($student['telephone']) . '</p>
            <p><strong>🎓 Modules:</strong> ' . htmlspecialchars($student['modules'] ?: 'Aucun') . '</p>
            <p><strong>📊 Projets:</strong> ' . htmlspecialchars(substr($student['projets_realises'] ?: 'Aucun', 0, 100)) . (strlen($student['projets_realises']) > 100 ? '...' : '') . '</p>
            <p><strong>⏰ Inscrit le:</strong> ' . date('d/m/Y', strtotime($student['created_at'])) . '</p>
        </div>
    </div>';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENSA Tétouan - Système de Gestion</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; min-height: 100vh; }
        .header { background: linear-gradient(135deg, #2c3e50, #3498db); color: white; padding: 25px; text-align: center; }
        .header h1 { margin-bottom: 10px; }
        .tabs { display: flex; background: #34495e; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .tab { flex: 1; padding: 15px; text-align: center; color: white; text-decoration: none; transition: all 0.3s; }
        .tab:hover, .tab.active { background: #3498db; transform: translateY(-2px); }
        .content { padding: 25px; }
        .form-section { background: #f8f9fa; padding: 25px; margin: 20px 0; border-radius: 10px; border-left: 5px solid #3498db; }
        .form-section h3 { margin-bottom: 20px; color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; }
        .form-group { margin: 15px 0; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #2c3e50; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        input:focus, select:focus, textarea:focus { border-color: #3498db; outline: none; box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2); }
        button { background: #3498db; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; font-size: 14px; transition: all 0.3s; }
        button:hover { background: #2980b9; transform: translateY(-2px); }
        .card { background: white; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin: 15px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: all 0.3s; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .student-name { font-size: 1.3em; font-weight: bold; color: #2c3e50; }
        .filiere { color: #3498db; font-weight: bold; font-size: 0.9em; }
        .btn-cv { background: #e74c3c; color: white; padding: 8px 15px; text-decoration: none; border-radius: 6px; font-size: 0.9em; transition: all 0.3s; }
        .btn-cv:hover { background: #c0392b; transform: scale(1.05); }
        .card-info p { margin: 8px 0; font-size: 0.9em; color: #34495e; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 25px 0; }
        .stat-card { background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 25px; text-align: center; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-number { font-size: 2.5em; font-weight: bold; margin-bottom: 5px; }
        .stat-label { font-size: 1.1em; opacity: 0.9; }
        .alert { padding: 15px; margin: 15px 0; border-radius: 6px; }
        .alert-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .alert-error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .search-box { width: 100%; padding: 12px; margin: 10px 0; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; }
        .search-box:focus { border-color: #3498db; }
        .main-nav { text-align: center; margin: 20px 0; padding: 15px; background: #ecf0f1; border-radius: 8px; }
        .main-nav a { background: #27ae60; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; margin: 0 10px; font-weight: bold; transition: all 0.3s; }
        .main-nav a:hover { background: #229954; transform: translateY(-2px); }
        .empty-state { text-align: center; padding: 40px; color: #7f8c8d; }
        .empty-state img { width: 100px; opacity: 0.5; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏫 ENSA Tétouan - Système de Gestion</h1>
            <p>Gestion des étudiants, entreprises et offres de stage</p>
        </div>

        <div class="main-nav">
            <a href="index.php">📝 Formulaire Étudiant</a>
            <a href="formulaire_cv.php">📄 Générateur CV</a>
            <a href="company_login.php">🏢 Espace Entreprises</a>
        </div>

        <div class="tabs">
            <a href="?tab=students" class="tab <?= $active_tab === 'students' ? 'active' : '' ?>">👥 Base de Données Étudiants</a>
            <a href="?tab=companies" class="tab <?= $active_tab === 'companies' ? 'active' : '' ?>">🏢 Entreprises</a>
            <a href="?tab=offers" class="tab <?= $active_tab === 'offers' ? 'active' : '' ?>">💼 Offres de Stage</a>
            <a href="?tab=stats" class="tab <?= $active_tab === 'stats' ? 'active' : '' ?>">📊 Statistiques</a>
        </div>

        <div class="content">
            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($active_tab === 'students'): ?>
                <div class="stats">
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['total_students'] ?></div>
                        <div class="stat-label">👥 Étudiants</div>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(135deg, #27ae60, #229954);">
                        <div class="stat-number"><?= $stats['unique_filieres'] ?></div>
                        <div class="stat-label">🎓 Filières</div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>🔍 Recherche d'Étudiant</h3>
                    <form method="GET">
                        <input type="hidden" name="tab" value="students">
                        <input type="email" name="search" class="search-box" placeholder="Rechercher par email (exemple: ahmed.alami@etu.ensa.ac.ma)..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button type="submit">Rechercher</button>
                    </form>
                    
                    <?php if ($searched): ?>
                        <?php if (count($search_result) > 0): ?>
                            <div class="alert alert-success">✅ Étudiant(s) trouvé(s): <?= count($search_result) ?></div>
                            <?php foreach ($search_result as $student): ?>
                                <?php renderStudentCard($student); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-error">❌ Aucun étudiant trouvé avec cet email.</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="form-section">
                    <h3>📋 Liste des Étudiants (<?= count($students) ?>)</h3>
                    <?php if (count($students) > 0): ?>
                        <?php foreach ($students as $student): ?>
                            <?php renderStudentCard($student); ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>Aucun étudiant enregistré</p>
                            <p><a href="index.php">➕ Ajouter le premier étudiant</a></p>
                        </div>
                    <?php endif; ?>
                </div>

            <?php elseif ($active_tab === 'companies'): ?>
                <div class="form-section">
                    <h3>➕ Ajouter une Entreprise</h3>
                    <form method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Nom de l'entreprise *</label>
                                <input type="text" name="name" required placeholder="Ex: TechnoSoft Maroc">
                            </div>
                            <div class="form-group">
                                <label>Secteur d'activité *</label>
                                <input type="text" name="sector" required placeholder="Ex: Technologies de l'information">
                            </div>
                            <div class="form-group">
                                <label>Ville *</label>
                                <input type="text" name="city" required placeholder="Ex: Tétouan">
                            </div>
                            <div class="form-group">
                                <label>Email (pour connexion)</label>
                                <input type="email" name="email" placeholder="rh@entreprise.com">
                            </div>
                            <div class="form-group">
                                <label>Mot de passe (pour connexion)</label>
                                <input type="password" name="password" placeholder="Mot de passe sécurisé">
                            </div>
                        </div>
                        <button type="submit" name="add_company">➕ Ajouter Entreprise</button>
                    </form>
                </div>

                <div class="form-section">
                    <h3>🏢 Liste des Entreprises (<?= count($companies) ?>)</h3>
                    <?php if (count($companies) > 0): ?>
                        <?php foreach ($companies as $company): ?>
                            <div class="card">
                                <div class="card-header">
                                    <div class="student-name"><?= htmlspecialchars($company['name']) ?></div>
                                    <div class="filiere"><?= htmlspecialchars($company['sector']) ?></div>
                                </div>
                                <div class="card-info">
                                    <p><strong>📍 Ville:</strong> <?= htmlspecialchars($company['city']) ?></p>
                                    <?php if ($company['email']): ?>
                                        <p><strong>📧 Email:</strong> <?= htmlspecialchars($company['email']) ?></p>
                                        <p><strong>🔐 Accès:</strong> Compte de connexion configuré</p>
                                    <?php else: ?>
                                        <p><strong>🔐 Accès:</strong> Pas de compte de connexion</p>
                                    <?php endif; ?>
                                    <p><strong>⏰ Ajoutée le:</strong> <?= date('d/m/Y', strtotime($company['created_at'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>Aucune entreprise enregistrée</p>
                        </div>
                    <?php endif; ?>
                </div>

            <?php elseif ($active_tab === 'offers'): ?>
                <div class="form-section">
                    <h3>➕ Créer une Offre de Stage</h3>
                    <?php if (count($companies) > 0): ?>
                        <form method="POST">
                            <div class="form-grid">
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
                                    <label>Titre du stage *</label>
                                    <input type="text" name="title" required placeholder="Ex: Stage Développement Web">
                                </div>
                                <div class="form-group">
                                    <label>Durée</label>
                                    <input type="text" name="duration" placeholder="3 mois" value="3 mois">
                                </div>
                                <div class="form-group">
                                    <label>Lieu</label>
                                    <input type="text" name="location" placeholder="Tétouan">
                                </div>
                                <div class="form-group">
                                    <label>Date limite de candidature</label>
                                    <input type="date" name="deadline" value="<?= date('Y-m-d', strtotime('+1 month')) ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Description du stage *</label>
                                <textarea name="description" rows="4" required placeholder="Décrivez les missions du stagiaire..."></textarea>
                            </div>
                            <div class="form-group">
                                <label>Exigences et qualifications</label>
                                <textarea name="requirements" rows="3" placeholder="Compétences requises, niveau d'études..."></textarea>
                            </div>
                            <button type="submit" name="add_offer">➕ Créer l'Offre</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-error">❌ Vous devez d'abord ajouter au moins une entreprise dans l'onglet "Entreprises".</div>
                    <?php endif; ?>
                </div>

                <div class="form-section">
                    <h3>💼 Offres de Stage Actives (<?= count($offers) ?>)</h3>
                    <?php if (count($offers) > 0): ?>
                        <?php foreach ($offers as $offer): ?>
                            <div class="card">
                                <div class="card-header">
                                    <div class="student-name"><?= htmlspecialchars($offer['title']) ?></div>
                                    <div class="filiere"><?= htmlspecialchars($offer['company_name']) ?></div>
                                </div>
                                <div class="card-info">
                                    <p><strong>📝 Description:</strong> <?= htmlspecialchars(substr($offer['description'], 0, 150)) . (strlen($offer['description']) > 150 ? '...' : '') ?></p>
                                    <p><strong>⏱️ Durée:</strong> <?= htmlspecialchars($offer['duration']) ?></p>
                                    <p><strong>📍 Lieu:</strong> <?= htmlspecialchars($offer['location'] ?: $offer['company_city']) ?></p>
                                    <p><strong>📅 Date limite:</strong> <?= date('d/m/Y', strtotime($offer['deadline'])) ?></p>
                                    <p><strong>📊 Statut:</strong> <span style="color: #27ae60; font-weight: bold;">Active</span></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>Aucune offre de stage active</p>
                        </div>
                    <?php endif; ?>
                </div>

            <?php elseif ($active_tab === 'stats'): ?>
                <div class="stats">
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['total_students'] ?></div>
                        <div class="stat-label">👥 Étudiants Inscrits</div>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(135deg, #27ae60, #229954);">
                        <div class="stat-number"><?= $stats['total_companies'] ?></div>
                        <div class="stat-label">🏢 Entreprises Partenaires</div>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                        <div class="stat-number"><?= $stats['active_offers'] ?></div>
                        <div class="stat-label">💼 Offres Actives</div>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(135deg, #9b59b6, #8e44ad);">
                        <div class="stat-number"><?= $stats['total_applications'] ?></div>
                        <div class="stat-label">📋 Candidatures</div>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                        <div class="stat-number"><?= $stats['unique_filieres'] ?></div>
                        <div class="stat-label">🎓 Filières Représentées</div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>📊 Vue d'ensemble du système</h3>
                    <div class="card-info">
                        <p>Le système ENSA Tétouan permet de gérer efficacement les informations des étudiants, des entreprises partenaires et des offres de stage.</p>
                        <br>
                        <p><strong>Fonctionnalités disponibles :</strong></p>
                        <p>• Inscription et gestion des profils étudiants</p>
                        <p>• Génération automatique de CV en PDF</p>
                        <p>• Gestion des entreprises partenaires</p>
                        <p>• Publication et gestion des offres de stage</p>
                        <p>• Système de candidature en ligne</p>
                        <p>• Statistiques et suivi des données</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>