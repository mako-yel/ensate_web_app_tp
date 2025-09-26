<?php
session_start();
require_once 'db_config.php';

// Initialize database
$db = new Database();

// Pre-fill data if email is provided
$prefill_data = [];
if (isset($_GET['email'])) {
    $student = $db->getStudentByEmail($_GET['email']);
    if ($student) {
        $prefill_data = [
            'nom' => $student['nom'],
            'prenom' => $student['prenom'],
            'age' => $student['age'],
            'telephone' => $student['telephone'],
            'email' => $student['email'],
            'filiere' => $student['filiere'],
            'annee' => $student['annee'],
            'modules' => explode(', ', $student['modules']),
            'projets_realises' => $student['projets_realises'],
            'projets_stages' => $student['projets_stages'],
            'centres_interet' => explode(', ', $student['centres_interet']),
            'langues' => explode(', ', $student['langues']),
            'remarques' => $student['remarques']
        ];
    }
}

// Merge with session data if exists
$form = isset($_SESSION['form']) ? $_SESSION['form'] : $prefill_data;

// Default values
$form = array_merge([
    'nom' => '',
    'prenom' => '',
    'age' => '',
    'telephone' => '',
    'email' => '',
    'adresse' => '',
    'ville' => '',
    'code_postal' => '',
    'filiere' => '',
    'annee' => '',
    'modules' => [],
    'projets_realises' => '',
    'projets_stages' => '',
    'centres_interet' => [],
    'langues' => [],
    'remarques' => '',
    'formations' => '',
    'competences' => ''
], $form);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur de CV - ENSA Tétouan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; color: #2c3e50; }
        fieldset { border: 2px solid #e74c3c; border-radius: 8px; padding: 20px; margin: 20px 0; }
        legend { font-weight: bold; padding: 0 10px; color: #e74c3c; }
        .form-group { margin: 15px 0; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #34495e; }
        .inline { display: inline-block; margin-right: 15px; }
        .required::after { content: " *"; color: red; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; }
        input[type="radio"], input[type="checkbox"] { width: auto; margin-right: 5px; }
        button { background: #e74c3c; color: white; border: none; padding: 15px 30px; border-radius: 4px; cursor: pointer; font-size: 16px; margin: 10px 5px; }
        button:hover { background: #c0392b; }
        .btn-secondary { background: #95a5a6; }
        .btn-secondary:hover { background: #7f8c8d; }
        .nav-links { text-align: center; margin: 20px 0; }
        .nav-links a { background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin: 0 10px; }
        .nav-links a:hover { background: #2980b9; }
        small { color: #7f8c8d; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Générateur de CV Professionnel</h1>
            <p>ENSA Tétouan - Créez votre CV en PDF</p>
        </div>

        <div class="nav-links">
            <a href="index.php">Formulaire Étudiant</a>
            <a href="part3_facultative.php">Base de Données</a>
        </div>

        <form action="generer_cv.php" method="post" enctype="multipart/form-data">

            <!-- Section Coordonnées personnelles (OBLIGATOIRE) -->
            <fieldset>
                <legend>Coordonnées Personnelles <small>(Champs obligatoires)</small></legend>
                <div class="form-group">
                    <label class="required">Nom</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($form['nom']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="required">Prénom</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($form['prenom']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Âge</label>
                    <input type="number" name="age" value="<?= htmlspecialchars($form['age']) ?>">
                </div>
                <div class="form-group">
                    <label class="required">Téléphone</label>
                    <input type="text" name="telephone" value="<?= htmlspecialchars($form['telephone']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="required">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($form['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Adresse</label>
                    <input type="text" name="adresse" value="<?= htmlspecialchars($form['adresse']) ?>">
                </div>
                <div class="form-group">
                    <label>Code Postal</label>
                    <input type="text" name="code_postal" value="<?= htmlspecialchars($form['code_postal']) ?>">
                </div>
                <div class="form-group">
                    <label>Ville</label>
                    <input type="text" name="ville" value="<?= htmlspecialchars($form['ville']) ?>">
                </div>
            </fieldset>

            <!-- Section Photo (OBLIGATOIRE) -->
            <fieldset>
                <legend>Photo <small>(Obligatoire)</small></legend>
                <div class="form-group">
                    <label class="required">Photo de profil</label>
                    <input type="file" name="photo" accept="image/*" required>
                    <small>Formats acceptés: JPG, PNG, GIF (Max: 2MB)</small>
                </div>
            </fieldset>

            <!-- Section Stages et Formations (OBLIGATOIRE) -->
            <fieldset>
                <legend>Stages et Formations <small>(Obligatoire)</small></legend>
                <div class="form-group">
                    <label class="required">Formations suivies</label>
                    <textarea name="formations" rows="4" required><?= htmlspecialchars($form['formations']) ?></textarea>
                    <small>Ex: Licence Informatique - Université XYZ (2020-2023)</small>
                </div>
                <div class="form-group">
                    <label class="required">Stages réalisés</label>
                    <textarea name="projets_stages" rows="4" required><?= htmlspecialchars($form['projets_stages']) ?></textarea>
                </div>
            </fieldset>

            <!-- Section Compétences et Langues (OBLIGATOIRE) -->
            <fieldset>
                <legend>Compétences et Langues <small>(Obligatoire)</small></legend>
                
                <div class="form-group">
                    <label class="required">Compétences techniques</label>
                    <textarea name="competences" rows="3" required><?= htmlspecialchars($form['competences']) ?></textarea>
                    <small>Ex: PHP, HTML/CSS, JavaScript, MySQL, etc.</small>
                </div>

                <div class="form-group">
                    <label class="required">Langues parlées</label>
                    <?php
                    $langues = ['Arabe','Français','Anglais','Espagnol','Allemand'];
                    foreach($langues as $l){
                        $checked = in_array($l, $form['langues']) ? 'checked' : '';
                        echo "<label class='inline'><input type='checkbox' name='langues[]' value='$l' $checked> $l</label>";
                    }
                    ?>
                    <label>Autre</label>
                    <input type="text" name="autre_langue" value="<?= htmlspecialchars($form['autre_langue'] ?? '') ?>">
                </div>
            </fieldset>

            <!-- Section Centres d'intérêt (OBLIGATOIRE) -->
            <fieldset>
                <legend>Centres d'Intérêt <small>(Obligatoire)</small></legend>
                <div class="form-group">
                    <label class="required">Centres d'intérêt</label>
                    <?php
                    $centres = ['Informatique','Sport','Musique','Lecture','Voyage','Photographie','Art'];
                    foreach($centres as $c){
                        $checked = in_array($c, $form['centres_interet']) ? 'checked' : '';
                        echo "<label class='inline'><input type='checkbox' name='centres_interet[]' value='$c' $checked> $c</label>";
                    }
                    ?>
                    <label>Autre</label>
                    <input type="text" name="autre_centre" value="<?= htmlspecialchars($form['autre_centre'] ?? '') ?>">
                </div>
            </fieldset>

            <!-- Section Informations académiques supplémentaires -->
            <fieldset>
                <legend>Informations Académiques Supplémentaires</legend>
                <div class="form-group">
                    <label>Filière</label>
                    <?php
                    $filieres = ['GSTR','GI','BDIA','GC','SCM','GCSE'];
                    foreach($filieres as $f) {
                        $checked = ($form['filiere']===$f) ? 'checked' : '';
                        echo "<label class='inline'><input type='radio' name='filiere' value='$f' $checked> $f</label>";
                    }
                    ?>
                </div>

                <div class="form-group">
                    <label>Modules suivis</label>
                    <?php
                    $modules = ['Génie Logiciel','Réseaux','Web Avancé','Technologie .Net'];
                    foreach($modules as $m){
                        $checked = in_array($m, $form['modules']) ? 'checked' : '';
                        echo "<label class='inline'><input type='checkbox' name='modules[]' value='$m' $checked> $m</label>";
                    }
                    ?>
                </div>
            </fieldset>

            <!-- Section Projets -->
            <fieldset>
                <legend>Projets Réalisés</legend>
                <div class="form-group">
                    <label>Projets académiques/personnels</label>
                    <textarea name="projets_realises" rows="3"><?= htmlspecialchars($form['projets_realises']) ?></textarea>
                </div>
            </fieldset>

            <!-- Section Remarques -->
            <fieldset>
                <legend>Informations Complémentaires</legend>
                <textarea name="remarques" rows="3" placeholder="Autres informations..."><?= htmlspecialchars($form['remarques']) ?></textarea>
            </fieldset>

            <div style="text-align: center;">
                <button type="submit" name="generer_cv">Générer le CV PDF</button>
                <button type="reset" class="btn-secondary">Réinitialiser</button>
            </div>
        </form>
    </div>
</body>
</html>