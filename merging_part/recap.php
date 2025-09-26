<?php
session_start();
require_once 'db_config.php';

// Check if we have form data
if (empty($_SESSION['form'])) {
    header("Location: index.php");
    exit;
}

$form = $_SESSION['form'];
$success = isset($_GET['success']) ? 'Informations enregistrées avec succès dans la base de données!' : '';

// Process "autre" fields for display
$centres_affichage = $form['centres_interet'] ?? [];
if (!empty($form['autre_centre'])) {
    if (is_array($centres_affichage)) {
        $centres_affichage[] = $form['autre_centre'];
    } else {
        $centres_affichage = explode(', ', $centres_affichage);
        $centres_affichage[] = $form['autre_centre'];
    }
}

$langues_affichage = $form['langues'] ?? [];
if (!empty($form['autre_langue'])) {
    if (is_array($langues_affichage)) {
        $langues_affichage[] = $form['autre_langue'];
    } else {
        $langues_affichage = explode(', ', $langues_affichage);
        $langues_affichage[] = $form['autre_langue'];
    }
}

// Handle file download (VALIDATE button)
if (isset($_POST['valider'])) {
    $prenom = $form['prenom'] ?? 'Etudiant';
    $nom = $form['nom'] ?? 'Inconnu';

    $contenu = "FICHE DE RENSEIGNEMENTS - ENSA TÉTOUAN\n";
    $contenu .= "==========================================\n\n";
    $contenu .= "INFORMATIONS PERSONNELLES\n";
    $contenu .= "--------------------------\n";
    $contenu .= "Nom : " . ($form['nom'] ?? '-') . "\n";
    $contenu .= "Prénom : " . ($form['prenom'] ?? '-') . "\n";
    $contenu .= "Âge : " . ($form['age'] ?? '-') . " ans\n";
    $contenu .= "Téléphone : " . ($form['telephone'] ?? '-') . "\n";
    $contenu .= "Email : " . ($form['email'] ?? '-') . "\n\n";
    
    $contenu .= "INFORMATIONS ACADÉMIQUES\n";
    $contenu .= "-------------------------\n";
    $contenu .= "Filière : " . ($form['filiere'] ?? '-') . "\n";
    $contenu .= "Année d'études : " . ($form['annee'] ?? '-') . "ème année\n";
    
    if (!empty($form['modules'])) {
        $modules_str = is_array($form['modules']) ? implode(', ', $form['modules']) : $form['modules'];
        $contenu .= "Modules suivis : " . $modules_str . "\n";
    } else {
        $contenu .= "Modules suivis : -\n";
    }
    
    $contenu .= "\nPROJETS ET STAGES\n";
    $contenu .= "------------------\n";
    $contenu .= "Projets réalisés : " . ($form['projets_realises'] ?: 'Aucun projet mentionné') . "\n";
    $contenu .= "Stages réalisés : " . ($form['projets_stages'] ?: 'Aucun stage mentionné') . "\n\n";
    
    $contenu .= "CENTRES D'INTÉRÊT ET LANGUES\n";
    $contenu .= "-----------------------------\n";
    if (!empty($centres_affichage)) {
        $centres_str = is_array($centres_affichage) ? implode(', ', $centres_affichage) : $centres_affichage;
        $contenu .= "Centres d'intérêt : " . $centres_str . "\n";
    } else {
        $contenu .= "Centres d'intérêt : -\n";
    }
    
    if (!empty($langues_affichage)) {
        $langues_str = is_array($langues_affichage) ? implode(', ', $langues_affichage) : $langues_affichage;
        $contenu .= "Langues parlées : " . $langues_str . "\n";
    } else {
        $contenu .= "Langues parlées : -\n";
    }
    
    if (!empty($form['remarques'])) {
        $contenu .= "\nREMARQUES\n";
        $contenu .= "----------\n";
        $contenu .= $form['remarques'] . "\n";
    }
    
    $contenu .= "\n==========================================\n";
    $contenu .= "Fiche générée le " . date('d/m/Y à H:i:s') . "\n";

    $nomFichier = "fiche_" . preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($nom . "_" . $prenom)) . ".txt";

    header('Content-Type: text/plain; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"$nomFichier\"");
    header('Content-Length: ' . strlen($contenu));
    echo $contenu;
    exit;
}

// Handle modify button
if (isset($_POST['modifier'])) {
    header("Location: formulaire_alaa.php");
    exit;
}

// Convert arrays to strings for display if needed
if (is_array($form['modules'] ?? null)) {
    $modules_display = implode(', ', $form['modules']);
} else {
    $modules_display = $form['modules'] ?? '';
}

if (is_array($centres_affichage)) {
    $centres_display = implode(', ', $centres_affichage);
} else {
    $centres_display = $centres_affichage;
}

if (is_array($langues_affichage)) {
    $langues_display = implode(', ', $langues_affichage);
} else {
    $langues_display = $langues_affichage;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif - ENSA Tétouan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .header { text-align: center; color: #2c3e50; margin-bottom: 30px; }
        .section { margin: 25px 0; padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #3498db; }
        .section h3 { margin-bottom: 15px; color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; }
        .info-row { margin: 12px 0; padding: 8px 0; border-bottom: 1px solid #ecf0f1; }
        .info-label { font-weight: bold; color: #34495e; width: 180px; display: inline-block; }
        .info-value { color: #2c3e50; }
        .btn-group { display: flex; gap: 15px; justify-content: center; margin: 30px 0; }
        button, .btn { background: #3498db; color: white; border: none; padding: 15px 25px; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 16px; }
        button:hover, .btn:hover { background: #2980b9; transform: translateY(-2px); transition: all 0.3s; }
        .btn-validate { background: #27ae60; }
        .btn-validate:hover { background: #229954; }
        .btn-modify { background: #f39c12; }
        .btn-modify:hover { background: #e67e22; }
        .nav-links { text-align: center; margin: 20px 0; }
        .nav-links a { background: #95a5a6; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; margin: 0 5px; }
        .nav-links a:hover { background: #7f8c8d; }
        .alert { padding: 15px; margin: 15px 0; border-radius: 4px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Récapitulatif des Informations</h1>
            <p>Vérifiez vos informations avant validation</p>
        </div>

        <div class="nav-links">
            <a href="index.php">Nouveau Formulaire</a>
            <a href="part3_facultative.php">Gestion Base de Données</a>
            <a href="formulaire_cv.php">Générateur CV</a>
        </div>

        <?php if ($success): ?>
            <div class="alert"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="section">
            <h3>Informations Personnelles</h3>
            <div class="info-row">
                <span class="info-label">Nom :</span>
                <span class="info-value"><?= htmlspecialchars($form['nom'] ?? '') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Prénom :</span>
                <span class="info-value"><?= htmlspecialchars($form['prenom'] ?? '') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Âge :</span>
                <span class="info-value"><?= htmlspecialchars($form['age'] ?? '') ?> ans</span>
            </div>
            <div class="info-row">
                <span class="info-label">Téléphone :</span>
                <span class="info-value"><?= htmlspecialchars($form['telephone'] ?? '') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email :</span>
                <span class="info-value"><?= htmlspecialchars($form['email'] ?? '') ?></span>
            </div>
        </div>

        <div class="section">
            <h3>Informations Académiques</h3>
            <div class="info-row">
                <span class="info-label">Filière :</span>
                <span class="info-value"><?= htmlspecialchars($form['filiere'] ?? '') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Année d'études :</span>
                <span class="info-value"><?= htmlspecialchars($form['annee'] ?? '') ?>ème année</span>
            </div>
            <div class="info-row">
                <span class="info-label">Modules suivis :</span>
                <span class="info-value"><?= htmlspecialchars($modules_display ?: 'Aucun module sélectionné') ?></span>
            </div>
        </div>

        <div class="section">
            <h3>Projets et Stages</h3>
            <div class="info-row">
                <span class="info-label">Projets réalisés :</span>
                <span class="info-value"><?= htmlspecialchars($form['projets_realises'] ?: 'Aucun projet mentionné') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Stages réalisés :</span>
                <span class="info-value"><?= htmlspecialchars($form['projets_stages'] ?: 'Aucun stage mentionné') ?></span>
            </div>
        </div>

        <div class="section">
            <h3>Centres d'Intérêt et Langues</h3>
            <div class="info-row">
                <span class="info-label">Centres d'intérêt :</span>
                <span class="info-value"><?= htmlspecialchars($centres_display ?: 'Aucun centre d\'intérêt mentionné') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Langues parlées :</span>
                <span class="info-value"><?= htmlspecialchars($langues_display ?: 'Aucune langue mentionnée') ?></span>
            </div>
        </div>

        <?php if (!empty($form['remarques'])): ?>
        <div class="section">
            <h3>Remarques</h3>
            <div class="info-row">
                <span class="info-value"><?= nl2br(htmlspecialchars($form['remarques'])) ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="btn-group">
            <form method="post" style="display: inline;">
                <button type="submit" name="valider" class="btn-validate">📄 VALIDER et Télécharger</button>
            </form>

            <form method="post" style="display: inline;">
                <button type="submit" name="modifier" class="btn-modify">✏️ MODIFIER</button>
            </form>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <p><strong>Actions disponibles :</strong></p>
            <p>• <strong>VALIDER :</strong> Télécharge un fichier texte contenant vos informations</p>
            <p>• <strong>MODIFIER :</strong> Retourne au formulaire pour corriger les informations</p>
        </div>
    </div>
</body>
</html>