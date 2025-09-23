<?php
session_start();

// Récupération des données du formulaire
$form = [
    'nom' => $_POST['nom'] ?? '',
    'prenom' => $_POST['prenom'] ?? '',
    'age' => $_POST['age'] ?? '',
    'telephone' => $_POST['telephone'] ?? '',
    'email' => $_POST['email'] ?? '',
    'filiere' => $_POST['filiere'] ?? '',
    'annee' => $_POST['annee'] ?? '',
    'modules' => $_POST['modules'] ?? [],
    'projets_realises' => $_POST['projets_realises'] ?? '',
    'projets_stages' => $_POST['projets_stages'] ?? '',
    'centres_interet' => $_POST['centres_interet'] ?? [],
    'autre_centre' => $_POST['autre_centre'] ?? '',
    'langues' => $_POST['langues'] ?? [],
    'autre_langue' => $_POST['autre_langue'] ?? '',
    'remarques' => $_POST['remarques'] ?? ''
];

$_SESSION['form'] = $form;

// Préparer les listes complètes en fusionnant avec "autre"
$centres_affichage = $form['centres_interet'];
if(!empty($form['autre_centre'])) {
    $centres_affichage[] = $form['autre_centre'];
}

$langues_affichage = $form['langues'];
if(!empty($form['autre_langue'])) {
    $langues_affichage[] = $form['autre_langue'];
}
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Récapitulatif</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { margin-bottom: 20px; }
        p { margin: 6px 0; }
        b { width: 150px; display: inline-block; }
    </style>
</head>
<body>
    <h1>Récapitulatif des informations</h1>

    <p><b>Nom :</b> <?= $form['nom'] ?></p>
    <p><b>Prénom :</b> <?= $form['prenom'] ?></p>
    <p><b>Âge :</b> <?= $form['age'] ?></p>
    <p><b>Téléphone :</b> <?= $form['telephone'] ?></p>
    <p><b>Email :</b> <?= $form['email'] ?></p>
    <p><b>Filière :</b> <?= $form['filiere'] ?></p>
    <p><b>Année :</b> <?= $form['annee'] ?></p>
    <p><b>Modules :</b> <?= !empty($form['modules']) ? implode(', ', $form['modules']) : '-' ?></p>
    <p><b>Projets réalisés :</b> <?= $form['projets_realises'] ?: '-' ?></p>
    <p><b>Stages réalisés :</b> <?= $form['projets_stages'] ?: '-' ?></p>
    <p><b>Centres d'intérêt :</b> <?= !empty($centres_affichage) ? implode(', ', $centres_affichage) : '-' ?></p>
    <p><b>Langues :</b> <?= !empty($langues_affichage) ? implode(', ', $langues_affichage) : '-' ?></p>
    <p><b>Remarques :</b> <?= $form['remarques'] ?: '-' ?></p>

</body>
</html>
