<?php
session_start();
require_once 'db_config.php';

$db = new Database();
$error = '';
$success = '';

// Handle company login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (!empty($email) && !empty($password)) {
        $company = $db->authenticateCompany($email, $password);
        if ($company) {
            $_SESSION['company_emai'] = $company['email'];
            $_SESSION['company_name'] = $company['name'];
            header("Location: company_dashboard.php");
            exit;
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

// Handle student application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    $student_email = trim($_POST['student_email']);
    $offer_id = $_POST['offer_id'];
    $cover_letter = trim($_POST['cover_letter']);
    
    if (!empty($student_email) && !empty($offer_id) && !empty($cover_letter)) {
        $result = $db->submitApplication($student_email, $offer_id, $cover_letter);
        if ($result === true) {
            $success = "Candidature envoyée avec succès! L'entreprise a été notifiée.";
        } elseif ($result === 'duplicate') {
            $error = "Vous avez déjà postulé pour cette offre.";
        } else {
            $error = "Étudiant non trouvé. Veuillez d'abord vous inscrire via le formulaire principal.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

// Get active offers for students
$offers = $db->getActiveOffers();
$stats = $db->getStats();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateforme de Stage - ENSA Tétouan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #2c3e50, #3498db); color: white; padding: 40px; text-align: center; margin-bottom: 30px; border-radius: 15px; }
        .header h1 { font-size: 2.5em; margin-bottom: 10px; }
        .header p { font-size: 1.2em; opacity: 0.9; }
        .section { background: white; padding: 30px; margin: 20px 0; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .form-group { margin: 15px 0; }
        label { display: block; font-weight: bold; margin-bottom: 8px; color: #2c3e50; }
        input, textarea, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; }
        input:focus, textarea:focus { border-color: #3498db; outline: none; box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2); }
        button { background: #3498db; color: white; border: none; padding: 15px 25px; border-radius: 8px; cursor: pointer; font-size: 16px; transition: all 0.3s; }
        button:hover { background: #2980b9; transform: translateY(-2px); }
        .btn-company { background: #e74c3c; }
        .btn-company:hover { background: #c0392b; }
        .alert { padding: 15px; margin: 20px 0; border-radius: 8px; }
        .alert-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .alert-error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .offer-card { border: 1px solid #ddd; border-radius: 12px; padding: 25px; margin: 20px 0; transition: all 0.3s; }
        .offer-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .offer-title { font-size: 1.4em; font-weight: bold; color: #2c3e50; margin-bottom: 10px; }
        .offer-company { color: #3498db; font-weight: bold; font-size: 1.1em; margin-bottom: 15px; }
        .offer-meta { background: #f8f9fa; padding: 10px; border-radius: 6px; margin: 10px 0; }
        .nav-links { text-align: center; margin: 20px 0; }
        .nav-links a { background: #27ae60; color: white; padding: 12px 20px; text-decoration: none; border-radius: 8px; margin: 0 10px; font-weight: bold; transition: all 0.3s; }
        .nav-links a:hover { background: #229954; transform: translateY(-2px); }
        .tabs { display: flex; background: #34495e; border-radius: 10px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .tab { flex: 1; padding: 18px; text-align: center; color: white; text-decoration: none; transition: all 0.3s; font-weight: bold; }
        .tab:hover, .tab.active { background: #3498db; transform: translateY(-2px); }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0; }
        .stat-card { background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 25px; text-align: center; border-radius: 12px; }
        .stat-number { font-size: 2.2em; font-weight: bold; }
        .required { color: #e74c3c; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Plateforme de Stage</h1>
            <p>ENSA Tétouan - Interface Étudiants & Entreprises</p>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_students'] ?></div>
                <div>Étudiants Inscrits</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #27ae60, #229954);">
                <div class="stat-number"><?= $stats['active_offers'] ?></div>
                <div>Offres Actives</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                <div class="stat-number"><?= $stats['total_companies'] ?></div>
                <div>Entreprises Partenaires</div>
            </div>
        </div>

        <div class="nav-links">
            <a href="index.php">Formulaire Étudiant</a>
            <a href="part3_facultative.php">Gestion Base de Données</a>
            <a href="formulaire_cv.php">Générateur CV</a>
        </div>

        <div class="tabs">
            <a href="#" onclick="showTab('student')" class="tab active" id="tab-student">Espace Étudiant - Candidatures</a>
            <a href="#" onclick="showTab('company')" class="tab" id="tab-company">Espace Entreprise - Connexion</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Espace Étudiant -->
        <div id="student-section" class="section">
            <h2>📋 Offres de Stage Disponibles</h2>
            <p>Postulez directement aux offres qui vous intéressent. Votre email doit être enregistré dans notre base de données.</p>
            
            <?php if (count($offers) > 0): ?>
                <?php foreach ($offers as $offer): ?>
                    <div class="offer-card">
                        <div class="offer-title"><?= htmlspecialchars($offer['title']) ?></div>
                        <div class="offer-company">🏢 <?= htmlspecialchars($offer['company_name']) ?> - <?= htmlspecialchars($offer['company_city']) ?></div>
                        
                        <div class="offer-meta">
                            <strong>📝 Description:</strong> <?= htmlspecialchars($offer['description']) ?>
                        </div>
                        
                        <?php if (!empty($offer['requirements'])): ?>
                        <div class="offer-meta">
                            <strong>✅ Exigences:</strong> <?= htmlspecialchars($offer['requirements']) ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="offer-meta">
                            <strong>⏱️ Durée:</strong> <?= htmlspecialchars($offer['duration']) ?> | 
                            <strong>📍 Lieu:</strong> <?= htmlspecialchars($offer['location'] ?: $offer['company_city']) ?> | 
                            <strong>📅 Date limite:</strong> <?= date('d/m/Y', strtotime($offer['deadline'])) ?>
                        </div>
                        
                        <form method="POST" style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
                            <input type="hidden" name="offer_id" value="<?= $offer['id'] ?>">
                            
                            <div class="form-group">
                                <label>Votre Email <span class="required">*</span></label>
                                <input type="email" name="student_email" required placeholder="Votre email enregistré (ex: prenom.nom@etu.ensa.ac.ma)">
                                <small>Cet email doit correspondre à celui utilisé lors de votre inscription</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Lettre de Motivation <span class="required">*</span></label>
                                <textarea name="cover_letter" rows="5" required placeholder="Expliquez en quelques lignes pourquoi vous souhaitez effectuer ce stage et quelles compétences vous pouvez apporter à l'entreprise..."></textarea>
                            </div>
                            
                            <button type="submit" name="apply">📤 Postuler pour ce Stage</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="offer-card" style="text-align: center; color: #7f8c8d;">
                    <h3>Aucune offre disponible actuellement</h3>
                    <p>Les entreprises partenaires publieront bientôt de nouvelles offres de stage.</p>
                    <p>En attendant, vous pouvez :</p>
                    <p>• <a href="index.php">Compléter votre profil étudiant</a></p>
                    <p>• <a href="formulaire_cv.php">Préparer votre CV</a></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Espace Entreprise -->
        <div id="company-section" class="section" style="display: none;">
            <h2>🏢 Connexion Entreprise</h2>
            <p>Accédez à votre tableau de bord pour gérer vos offres et consulter les candidatures</p>
            
            <form method="POST" style="max-width: 400px; margin: 30px auto;">
                <div class="form-group">
                    <label>Email de l'entreprise <span class="required">*</span></label>
                    <input type="email" name="email" required placeholder="contact@entreprise.com">
                </div>
                <div class="form-group">
                    <label>Mot de passe <span class="required">*</span></label>
                    <input type="password" name="password" required placeholder="Votre mot de passe">
                </div>
                <button type="submit" name="login" class="btn-company" style="width: 100%;">🔐 Se Connecter</button>
            </form>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 30px;">
                <h3>💡 Informations pour les Entreprises</h3>
                <p><strong>Vous êtes une entreprise et souhaitez :</strong></p>
                <ul style="margin: 15px 0; padding-left: 20px;">
                    <li>Publier des offres de stage</li>
                    <li>Consulter les profils d'étudiants</li>
                    <li>Gérer vos candidatures</li>
                </ul>
                <p><strong>Pour obtenir un compte :</strong></p>
                <p>Contactez l'administration ENSA via la <a href="part3_facultative.php?tab=companies">page de gestion</a> ou demandez la création de votre compte entreprise.</p>
            </div>
        </div>
    </div>

    <script>
        function showTab(tab) {
            if (tab === 'student') {
                document.getElementById('student-section').style.display = 'block';
                document.getElementById('company-section').style.display = 'none';
                document.getElementById('tab-student').classList.add('active');
                document.getElementById('tab-company').classList.remove('active');
            } else {
                document.getElementById('student-section').style.display = 'none';
                document.getElementById('company-section').style.display = 'block';
                document.getElementById('tab-student').classList.remove('active');
                document.getElementById('tab-company').classList.add('active');
            }
        }
    </script>
</body>
</html>