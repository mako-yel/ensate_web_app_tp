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
    'remarques' => ''
];
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Fiche de renseignements</title>
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
  </style>
</head>
<body>
  <h1>Fiche de renseignements</h1>

  <form action="recap.php" method="post" enctype="multipart/form-data">

    <!-- Section Renseignements personnels -->
    <fieldset>
      <legend>Renseignements personnels</legend>
      <label>Nom: <input type="text" name="nom" value="<?= $form['nom'] ?>"></label>
      <label>Prénom: <input type="text" name="prenom" value="<?= $form['prenom'] ?>"></label>
      <label>Âge: <input type="number" name="age" value="<?= $form['age'] ?>"></label>
      <label>Téléphone: <input type="text" name="telephone" value="<?= $form['telephone'] ?>"></label>
      <label>Email: <input type="email" name="email" value="<?= $form['email'] ?>"></label>
    </fieldset>

    <!-- Section Académique -->
    <fieldset>
      <legend>Renseignements académiques</legend>
      <p>Filière :</p>
      <?php
        $filieres = ['GSTR','GI','BDIA','GC','SCM','GCSE'];
        foreach($filieres as $f) {
            $checked = ($form['filiere']===$f)?'checked':'';
            echo "<label class='inline'><input type='radio' name='filiere' value='$f' $checked> $f</label>";
        }
      ?>

      <p>Année :</p>
      <?php
        $annees = ['1'=>'1ère','2'=>'2ème','3'=>'3ème'];
        foreach($annees as $val => $txt){
            $checked = ($form['annee']===$val)?'checked':'';
            echo "<label class='inline'><input type='radio' name='annee' value='$val' $checked> $txt</label>";
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
      <legend>Projets / Stages</legend>
      <label>Projets réalisés :
        <textarea name="projets_realises" rows="3"><?= $form['projets_realises'] ?></textarea>
      </label>
      <label>Stages réalisés :
        <textarea name="projets_stages" rows="3"><?= $form['projets_stages'] ?></textarea>
      </label>
    </fieldset>

    <!-- Section Centres d'intérêt et langues -->
    <fieldset>
      <legend>Centres d'intérêt et langues</legend>
      <p>Centres d'intérêt :</p>
      <?php
        $centres = ['Informatique','Sport','Musique','Lecture'];
        foreach($centres as $c){
            $checked = in_array($c,$form['centres_interet'])?'checked':'';
            echo "<label class='inline'><input type='checkbox' name='centres_interet[]' value='$c' $checked> $c</label>";
        }
      ?>
      <label>Autre : <input type="text" name="autre_centre" value="<?= $form['autre_centre'] ?? '' ?>"></label>

      <p>Langues parlées :</p>
      <?php
        $langues = ['Arabe','Français','Anglais','Espagnol'];
        foreach($langues as $l){
            $checked = in_array($l,$form['langues'])?'checked':'';
            echo "<label class='inline'><input type='checkbox' name='langues[]' value='$l' $checked> $l</label>";
        }
      ?>
      <label>Autre : <input type="text" name="autre_langue" value="<?= $form['autre_langue'] ?? '' ?>"></label>
    </fieldset>

    <!-- Section Remarques -->
    <fieldset>
      <legend>Remarques</legend>
      <textarea name="remarques" rows="3" cols="50"><?= $form['remarques'] ?></textarea>
    </fieldset>

    <fieldset>
      <legend>Upload de fichier</legend>
      <label>Choisissez un fichier :
        <input type="file" name="fichier">
      </label>
    </fieldset>


    <button type="submit" name="envoyer">Envoyer</button>
  </form>
</body>
</html>
