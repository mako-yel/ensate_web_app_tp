<?php
session_start();

// Valeurs par défaut
$form = isset($_SESSION['form']) ? $_SESSION['form'] : [
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
    'adresse' => '',
    'ville' => '',
    'code_postal' => '',
    'formations' => '',
    'competences' => ''
];
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Générateur de CV</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    fieldset {
        border: 2px solid #333;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    legend {
        font-weight: bold;
        padding: 0 10px;
    }
    label { display: block; margin-top: 8px; }
    .inline { display: inline-block; margin-right: 12px; }
    .required::after { content: " *"; color: red; }
  </style>
</head>
<body>
  <h1>Générateur de CV - Formulaire</h1>

  <form action="generer_cv.php" method="post" enctype="multipart/form-data">

    <!-- Section Renseignements personnels (OBLIGATOIRE) -->
    <fieldset>
      <legend>Coordonnées personnelles <small>(Champs obligatoires)</small></legend>
      <label class="required">Nom: <input type="text" name="nom" value="<?= $form['nom'] ?>" required></label>
      <label class="required">Prénom: <input type="text" name="prenom" value="<?= $form['prenom'] ?>" required></label>
      <label>Âge: <input type="number" name="age" value="<?= $form['age'] ?>"></label>
      <label class="required">Téléphone: <input type="text" name="telephone" value="<?= $form['telephone'] ?>" required></label>
      <label class="required">Email: <input type="email" name="email" value="<?= $form['email'] ?>" required></label>
      <label>Adresse: <input type="text" name="adresse" value="<?= $form['adresse'] ?? '' ?>"></label>
      <label>Code Postal: <input type="text" name="code_postal" value="<?= $form['code_postal'] ?? '' ?>"></label>
      <label>Ville: <input type="text" name="ville" value="<?= $form['ville'] ?? '' ?>"></label>
    </fieldset>

    <!-- Section Photo (OBLIGATOIRE) -->
    <fieldset>
      <legend>Photo <small>(Obligatoire)</small></legend>
      <label class="required">Photo de profil :
        <input type="file" name="photo" accept="image/*" required>
      </label>
      <small>Format acceptés: JPG, PNG, GIF (Max: 2MB)</small>
    </fieldset>

    <!-- Section Stages et Formations (OBLIGATOIRE) -->
    <fieldset>
      <legend>Stages et Formations <small>(Obligatoire)</small></legend>
      <label class="required">Formations suivies :
        <textarea name="formations" rows="4" required><?= $form['formations'] ?? '' ?></textarea>
      </label>
      <small>Ex: Licence Informatique - Université XYZ (2020-2023)</small>
      
      <label class="required">Stages réalisés :
        <textarea name="projets_stages" rows="4" required><?= $form['projets_stages'] ?></textarea>
      </label>
    </fieldset>

    <!-- Section Compétences et Langues (OBLIGATOIRE) -->
    <fieldset>
      <legend>Compétences et Langues <small>(Obligatoire)</small></legend>
      
      <label class="required">Compétences techniques :
        <textarea name="competences" rows="3" required><?= $form['competences'] ?? '' ?></textarea>
      </label>
      <small>Ex: PHP, HTML/CSS, JavaScript, MySQL, etc.</small>

      <p class="required">Langues parlées :</p>
      <?php
        $langues = ['Arabe','Français','Anglais','Espagnol','Allemand'];
        foreach($langues as $l){
            $checked = in_array($l,$form['langues'])?'checked':'';
            echo "<label class='inline'><input type='checkbox' name='langues[]' value='$l' $checked required> $l</label>";
        }
      ?>
      <label>Autre : <input type="text" name="autre_langue" value="<?= $form['autre_langue'] ?? '' ?>"></label>
    </fieldset>

    <!-- Section Centres d'intérêt (OBLIGATOIRE) -->
    <fieldset>
      <legend>Centres d'intérêt <small>(Obligatoire)</small></legend>
      <p class="required">Centres d'intérêt :</p>
      <?php
        $centres = ['Informatique','Sport','Musique','Lecture','Voyage','Photographie','Art'];
        foreach($centres as $c){
            $checked = in_array($c,$form['centres_interet'])?'checked':'';
            echo "<label class='inline'><input type='checkbox' name='centres_interet[]' value='$c' $checked required> $c</label>";
        }
      ?>
      <label>Autre : <input type="text" name="autre_centre" value="<?= $form['autre_centre'] ?? '' ?>"></label>
    </fieldset>

    <!-- Section Informations académiques supplémentaires -->
    <fieldset>
      <legend>Informations académiques supplémentaires</legend>
      <p>Filière :</p>
      <?php
        $filieres = ['GSTR','GI','BDIA','GC','SCM','GCSE'];
        foreach($filieres as $f) {
            $checked = ($form['filiere']===$f)?'checked':'';
            echo "<label class='inline'><input type='radio' name='filiere' value='$f' $checked> $f</label>";
        }
      ?>

      <p>Modules suivis :</p>
      <?php
        $modules = ['Genie Logiciel','Réseaux','Web Avancé','Technologie .Net'];
        foreach($modules as $m){
            $checked = in_array($m, $form['modules'])?'checked':'';
            echo "<label class='inline'><input type='checkbox' name='modules[]' value='$m' $checked> $m</label>";
        }
      ?>
    </fieldset>

    <!-- Section Projets -->
    <fieldset>
      <legend>Projets réalisés</legend>
      <label>Projets académiques/personnels :
        <textarea name="projets_realises" rows="3"><?= $form['projets_realises'] ?></textarea>
      </label>
    </fieldset>

    <!-- Section Remarques -->
    <fieldset>
      <legend>Informations complémentaires</legend>
      <textarea name="remarques" rows="3" cols="50" placeholder="Autres informations..."><?= $form['remarques'] ?></textarea>
    </fieldset>

    <button type="submit" name="generer_cv">Générer le CV PDF</button>
    <button type="reset">Réinitialiser</button>
  </form>
</body>
</html>