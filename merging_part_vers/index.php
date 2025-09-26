<?php
session_start();
require_once 'db_config.php';

// Initialize database
$db = new Database();
$success = '';
$error = '';

// Handle student addition from Part 1
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $required_fields = ['nom', 'prenom', 'age', 'telephone', 'email', 'filiere', 'annee'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        $error = "Veuillez remplir tous les champs obligatoires : " . implode(', ', $missing_fields);
    } else {
        // Handle file upload
        $photo_path = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $photo_path = $upload_dir . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
        }
        
        // Prepare data for database
        $student_data = [
            'nom' => trim($_POST['nom']),
            'prenom' => trim($_POST['prenom']),
            'age' => intval($_POST['age']),
            'telephone' => trim($_POST['telephone']),
            'email' => trim($_POST['email']),
            'filiere' => $_POST['filiere'],
            'annee' => $_POST['annee'],
            'modules' => isset($_POST['modules']) ? implode(', ', $_POST['modules']) : '',
            'projets_realises' => trim($_POST['projets_realises'] ?? ''),
            'projets_stages' => trim($_POST['projets_stages'] ?? ''),
            'centres_interet' => isset($_POST['centres_interet']) ? implode(', ', $_POST['centres_interet']) : '',
            'langues' => isset($_POST['langues']) ? implode(', ', $_POST['langues']) : '',
            'remarques' => trim($_POST['remarques'] ?? ''),
            'photo' => $photo_path
        ];
        
        // Add additional fields from "autre"
        if (!empty($_POST['autre_centre'])) {
            $student_data['centres_interet'] .= ($student_data['centres_interet'] ? ', ' : '') . trim($_POST['autre_centre']);
        }
        if (!empty($_POST['autre_langue'])) {
            $student_data['langues'] .= ($student_data['langues'] ? ', ' : '') . trim($_POST['autre_langue']);
        }
        
        try {
            if ($db->addStudent($student_data)) {
                // Store in session for recap page
                $_SESSION['form'] = $student_data;
                $_SESSION['form']['modules'] = $_POST['modules'] ?? [];
                $_SESSION['form']['centres_interet'] = $_POST['centres_interet'] ?? [];
                $_SESSION['form']['langues'] = $_POST['langues'] ?? [];
                $_SESSION['form']['autre_centre'] = $_POST['autre_centre'] ?? '';
                $_SESSION['form']['autre_langue'] = $_POST['autre_langue'] ?? '';
                
                // Redirect to recap page
                header("Location: recap.php?success=1");
                exit;
            } else {
                $error = "Erreur lors de l'ajout de l'étudiant.";
            }
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $error = "Cet email est déjà utilisé. Veuillez utiliser un autre email.";
            } else {
                $error = "Erreur: " . $e->getMessage();
            }
        }
    }
}

// Default form values
$form = [
    'nom' => '',
    'prenom' => '',
    'age' => '',
    'telephone' => '',
    'email' => '',
    'filiere' => '',
    'annee' => '',
    'modules' => [],
    'projets_realises' => '',
    'projets_stages' => '',
    'centres_interet' => [],
    'langues' => [],
    'remarques' => '',
    'autre_centre' => '',
    'autre_langue' => ''
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Étudiant - ENSA Tétouan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; color: #2c3e50; }
        fieldset { border: 2px solid #3498db; border-radius: 8px; padding: 20px; margin: 20px 0; }
        legend { font-weight: bold; padding: 0 10px; color: #2c3e50; }
        .form-group { margin: 15px 0; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #34495e; }
        .inline { display: inline-block; margin-right: 15px; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; }
        input[type="radio"], input[type="checkbox"] { width: auto; margin-right: 5px; }
        button { background: #3498db; color: white; border: none; padding: 15px 30px; border-radius: 4px; cursor: pointer; font-size: 16px; margin: 10px 5px; }
        button:hover { background: #2980b9; }
        .btn-secondary { background: #95a5a6; }
        .btn-secondary:hover { background: #7f8c8d; }
        .alert { padding: 15px; margin: 15px 0; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .nav-links { text-align: center; margin: 20px 0; }
        .nav-links a { background: #27ae60; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin: 0 10px; }
        .nav-links a:hover { background: #229954; }
        .required { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Formulaire d'Inscription Étudiant</h1>
            <p>ENSA Tétouan - École Nationale des Sciences Appliquées</p>
        </div>

        <div class="nav-links">
            <a href="part3_facultative.php">Gestion Base de Données</a>
            <a href="formulaire_cv.php">Générateur CV</a>
            <a href="company_login.php">Espace Stage & Entreprises</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
            <!-- Section Renseignements personnels -->
            <fieldset>
                <legend>Renseignements Personnels</legend>
                <div class="form-group">
                    <label>Nom <span class="required">*</span></label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($form['nom']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Prénom <span class="required">*</span></label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($form['prenom']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Âge <span class="required">*</span></label>
                    <input type="number" name="age" min="16" max="35" value="<?= htmlspecialchars($form['age']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Téléphone <span class="required">*</span></label>
                    <input type="text" name="telephone" value="<?= htmlspecialchars($form['telephone']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" value="<?= htmlspecialchars($form['email']) ?>" required>
                </div>
            </fieldset>

            <!-- Section Académique -->
            <fieldset>
                <legend>Renseignements Académiques</legend>
                <div class="form-group">
                    <label>Filière <span class="required">*</span></label>
                    <?php
                    $filieres = [
                        'GSTR' => 'Génie des Systèmes de Télécommunications et Réseaux',
                        'GI' => 'Génie Informatique', 
                        'BDIA' => 'Big Data et Intelligence Artificielle',
                        'GC' => 'Génie Civil',
                        'SCM' => 'Supply Chain Management',
                        'GCSE' => 'Génie Civil et Systèmes Énergétiques'
                    ];
                    foreach($filieres as $code => $nom) {
                        $checked = ($form['filiere'] === $code) ? 'checked' : '';
                        echo "<label class='inline'><input type='radio' name='filiere' value='$code' $checked required> $code</label>";
                    }
                    ?>
                </div>

                <div class="form-group">
                    <label>Année <span class="required">*</span></label>
                    <?php
                    $annees = ['1'=>'1ère','2'=>'2ème','3'=>'3ème'];
                    foreach($annees as $val => $txt){
                        $checked = ($form['annee'] === $val) ? 'checked' : '';
                        echo "<label class='inline'><input type='radio' name='annee' value='$val' $checked required> $txt</label>";
                    }
                    ?>
                </div>

                <div class="form-group">
                    <label>Modules suivis</label>
                    <?php
                    $modules = ['Génie Logiciel','Réseaux','Web Avancé','Technologie .Net','Base de Données','Systèmes Distribués'];
                    foreach($modules as $m){
                        $checked = in_array($m, $form['modules']) ? 'checked' : '';
                        echo "<label class='inline'><input type='checkbox' name='modules[]' value='$m' $checked> $m</label>";
                    }
                    ?>
                </div>
            </fieldset>

            <!-- Section Projets -->
            <fieldset>
                <legend>Projets et Stages</legend>
                <div class="form-group">
                    <label>Projets réalisés</label>
                    <textarea name="projets_realises" rows="3" placeholder="Décrivez vos projets académiques ou personnels..."><?= htmlspecialchars($form['projets_realises']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Stages réalisés</label>
                    <textarea name="projets_stages" rows="3" placeholder="Décrivez vos expériences de stage..."><?= htmlspecialchars($form['projets_stages']) ?></textarea>
                </div>
            </fieldset>

            <!-- Section Centres d'intérêt et langues -->
            <fieldset>
                <legend>Centres d'Intérêt et Langues</legend>
                <div class="form-group">
                    <label>Centres d'intérêt</label>
                    <?php
                    $centres = ['Informatique','Sport','Musique','Lecture','Art','Voyage','Photographie'];
                    foreach($centres as $c){
                        $checked = in_array($c, $form['centres_interet']) ? 'checked' : '';
                        echo "<label class='inline'><input type='checkbox' name='centres_interet[]' value='$c' $checked> $c</label>";
                    }
                    ?>
                    <label>Autre</label>
                    <input type="text" name="autre_centre" value="<?= htmlspecialchars($form['autre_centre']) ?>" placeholder="Spécifiez...">
                </div>

                <div class="form-group">
                    <label>Langues parlées</label>
                    <?php
                    $langues = ['Arabe','Français','Anglais','Espagnol','Allemand','Italien'];
                    foreach($langues as $l){
                        $checked = in_array($l, $form['langues']) ? 'checked' : '';
                        echo "<label class='inline'><input type='checkbox' name='langues[]' value='$l' $checked> $l</label>";
                    }
                    ?>
                    <label>Autre</label>
                    <input type="text" name="autre_langue" value="<?= htmlspecialchars($form['autre_langue']) ?>" placeholder="Spécifiez...">
                </div>
            </fieldset>

            <!-- Section Remarques -->
            <fieldset>
                <legend>Informations Complémentaires</legend>
                <textarea name="remarques" rows="3" placeholder="Ajoutez toute information que vous jugez utile..."><?= htmlspecialchars($form['remarques']) ?></textarea>
            </fieldset>

            <!-- Section Photo -->
            <fieldset>
                <legend>Photo de Profil (Optionnelle)</legend>
                <input type="file" name="photo" accept="image/jpeg,image/jpg,image/png,image/gif">
                <small>Formats acceptés: JPG, PNG, GIF (Max: 5MB)</small>
            </fieldset>

            <div style="text-align: center;">
                <button type="submit" name="add_student">Enregistrer les Informations</button>
                <button type="reset" class="btn-secondary">Réinitialiser</button>
            </div>
        </form>
    </div>
</body>
</html>