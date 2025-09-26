<?php
session_start();
require_once 'db_config.php';

// Check if company is logged in
if (!isset($_SESSION['company_email'])) {
    header("Location: company_login.php");
    exit;
}

$db = new Database();
$company_email = $_SESSION['company_email'];
$company_name = $_SESSION['company_name'];
$success = '';
$error = '';

// Handle application status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $app_id = $_POST['app_id'];
    $status = $_POST['status'];
    $notes = trim($_POST['notes'] ?? '');
    
    if ($db->updateApplicationStatus($app_id, $status, $notes)) {
        $success = "Statut de candidature mis à jour!";
    } else {
        $error = "Erreur lors de la mise à jour.";
    }
}

// Handle adding new offer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_offer'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $requirements = trim($_POST['requirements'] ?? '');
    $duration = trim($_POST['duration'] ?? '3 mois');
    $location = trim($_POST['location'] ?? '');
    $deadline = $_POST['deadline'] ?? date('Y-m-d', strtotime('+1 month'));
    
    if (!empty($title) && !empty($description)) {
        if ($db->addInternshipOffer($company_email, $title, $description, $requirements, $duration, $location, $deadline)) {
            $success = "Offre de stage créée avec succès!";
        } else {
            $error = "Erreur lors de la création de l'offre.";
        }
    } else {
        $error = "Titre et description sont obligatoires.";
    }
}

// Get company data
$offers = $db->getCompanyOffers($company_email);
$applications = $db->getCompanyApplications($company_email);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Entreprise - <?= htmlspecialchars($company_name) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: #e74c3c; color: white; padding: 20px; text-align: center; border-radius: 10px; margin-bottom: 20px; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin: 15px 0; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        button:hover { background: #c0392b; }
        .btn-success { background: #27ae60; }
        .btn-success:hover { background: #229954; }
        .btn-warning { background: #f39c12; }
        .btn-warning:hover { background: #e67e22; }
        .alert { padding: 15px; margin: 15px 0; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin: 10px 0; }
        .status-pending { border-left: 4px solid #f39c12; }
        .status-accepted { border-left: 4px solid #27ae60; }
        .status-rejected { border-left: 4px solid #e74c3c; }
        .nav-links { text-align: center; margin: 20px 0; }
        .nav-links a { background: #95a5a6; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin: 0 5px; }
        .nav-links a:hover { background: #7f8c8d; }
        .logout { background: #95a5a6; float: right; }
        .logout:hover { background: #7f8c8d; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tableau de Bord - <?= htmlspecialchars($company_name) ?></h1>
            <p>Gestion des offres de stage et candidatures</p>
            <a href="company_login.php" class="logout" onclick="<?php session_destroy(); ?>">Déconnexion</a>
        </div>

        <div class="nav-links">
            <a href="company_login.php">Retour Interface Publique</a>
            <a href="part3_facultative.php">Base de Données</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Section: Créer une nouvelle offre -->
        <div class="section">
            <h2>Créer une Nouvelle Offre de Stage</h2>
            <form method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Titre du stage *</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Durée</label>
                        <input type="text" name="duration" placeholder="3 mois">
                    </div>
                    <div class="form-group">
                        <label>Lieu</label>
                        <input type="text" name="location">
                    </div>
                    <div class="form-group">
                        <label>Date limite de candidature</label>
                        <input type="date" name="deadline" value="<?= date('Y-m-d', strtotime('+1 month')) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description du stage *</label>
                    <textarea name="description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Exigences et qualifications</label>
                    <textarea name="requirements" rows="2"></textarea>
                </div>
                <button type="submit" name="add_offer">Créer l'Offre</button>
            </form>
        </div>

        <!-- Section: Mes offres -->
        <div class="section">
            <h2>Mes Offres de Stage</h2>
            <?php if (count($offers) > 0): ?>
                <table>
                    <tr>
                        <th>Titre</th>
                        <th>Date de publication</th>
                        <th>Date limite</th>
                        <th>Statut</th>
                        <th>Candidatures</th>
                    </tr>
                    <?php foreach ($offers as $offer): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($offer['title']) ?></strong><br>
                                <small><?= htmlspecialchars($offer['description']) ?></small></td>
                            <td><?= date('d/m/Y', strtotime($offer['posted_date'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($offer['deadline'])) ?></td>
                            <td>
                                <span style="color: <?= $offer['status'] === 'active' ? '#27ae60' : '#e74c3c' ?>">
                                    <?= $offer['status'] === 'active' ? 'Active' : 'Fermée' ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $count = 0;
                                foreach ($applications as $app) {
                                    if ($app['offer_title'] === $offer['title']) $count++;
                                }
                                echo $count . ' candidature(s)';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>Aucune offre publiée pour le moment.</p>
            <?php endif; ?>
        </div>

        <!-- Section: Candidatures reçues -->
        <div class="section">
            <h2>Candidatures Reçues</h2>
            <?php if (count($applications) > 0): ?>
                <?php foreach ($applications as $app): ?>
                    <div class="card status-<?= $app['status'] ?>">
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                            <div>
                                <h3><?= htmlspecialchars($app['prenom'] . ' ' . $app['nom']) ?></h3>
                                <p><strong>Offre:</strong> <?= htmlspecialchars($app['offer_title']) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($app['email']) ?></p>
                                <p><strong>Téléphone:</strong> <?= htmlspecialchars($app['telephone']) ?></p>
                                <p><strong>Filière:</strong> <?= htmlspecialchars($app['filiere']) ?> - <?= htmlspecialchars($app['annee']) ?>ème année</p>
                                <p><strong>Compétences:</strong> <?= htmlspecialchars($app['modules']) ?></p>
                                
                                <div style="margin: 15px 0; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                                    <strong>Lettre de motivation:</strong><br>
                                    <?= nl2br(htmlspecialchars($app['cover_letter'])) ?>
                                </div>
                                
                                <?php if (!empty($app['company_notes'])): ?>
                                    <div style="margin: 10px 0; padding: 10px; background: #e8f4f8; border-radius: 4px;">
                                        <strong>Notes internes:</strong><br>
                                        <?= nl2br(htmlspecialchars($app['company_notes'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div>
                                <p><strong>Date de candidature:</strong><br><?= date('d/m/Y', strtotime($app['application_date'])) ?></p>
                                <p><strong>Statut actuel:</strong><br>
                                    <span style="color: <?= $app['status'] === 'pending' ? '#f39c12' : ($app['status'] === 'accepted' ? '#27ae60' : '#e74c3c') ?>">
                                        <?= $app['status'] === 'pending' ? 'En attente' : ($app['status'] === 'accepted' ? 'Acceptée' : 'Refusée') ?>
                                    </span>
                                </p>
                                
                                <form method="POST" style="margin-top: 15px;">
                                    <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                                    
                                    <div class="form-group">
                                        <label>Nouveau statut:</label>
                                        <select name="status" required>
                                            <option value="pending" <?= $app['status'] === 'pending' ? 'selected' : '' ?>>En attente</option>
                                            <option value="accepted" <?= $app['status'] === 'accepted' ? 'selected' : '' ?>>Accepter</option>
                                            <option value="rejected" <?= $app['status'] === 'rejected' ? 'selected' : '' ?>>Refuser</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Notes (optionnel):</label>
                                        <textarea name="notes" rows="2" placeholder="Commentaires internes..."><?= htmlspecialchars($app['company_notes']) ?></textarea>
                                    </div>
                                    
                                    <button type="submit" name="update_status" class="btn-success">Mettre à jour</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune candidature reçue pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>