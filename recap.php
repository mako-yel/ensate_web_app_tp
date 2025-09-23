<?php
session_start();

// Récupération des données du formulaire initial
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['envoyer'])){
    $_SESSION['form'] = $_POST;
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

    $_SESSION['form'] = $form; // stocker en session pour usage ultérieur
}

// Récupérer les données depuis la session
$form = $_SESSION['form'] ?? [];

// Fusionner les champs "autre"
$centres_affichage = $form['centres_interet'] ?? [];
if(!empty($form['autre_centre'])) {
    $centres_affichage[] = $form['autre_centre'];
}

$langues_affichage = $form['langues'] ?? [];
if(!empty($form['autre_langue'])) {
    $langues_affichage[] = $form['autre_langue'];
}

// Gestion du fichier uploadé
$fichier_info = '';
if(isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK){
    $nomTemp = $_FILES['fichier']['tmp_name'];
    $nomOriginal = $_FILES['fichier']['name'];
    $taille = $_FILES['fichier']['size'];
    $type = $_FILES['fichier']['type'];

    $destination = __DIR__ . '/uploads/' . basename($nomOriginal);
    if(!is_dir(__DIR__.'/uploads')) mkdir(__DIR__.'/uploads', 0777, true);

    if(move_uploaded_file($nomTemp, $destination)){
        $fichier_info = "Fichier uploadé avec succès : $nomOriginal ($taille bytes, type: $type)";
    } else {
        $fichier_info = "Erreur lors de l'upload du fichier.";
    }
} else {
    $fichier_info = "Aucun fichier uploadé.";
}

// Générer et forcer le téléchargement du fichier texte
if(isset($_POST['valider']) && !empty($form)){
    $prenom_concerne = $form['prenom'] ?? '';
$nom_concerne = $form['nom'] ?? 'Inconnu';

// Première ligne avec le nom de la personne
$contenu = "Récapitulatif du formulaire de $prenom_concerne $nom_concerne\n";

$contenu .= "Nom : " . ($form['nom'] ?? '-') . "\n";
$contenu .= "Prénom : " . ($form['prenom'] ?? '-') . "\n";
$contenu .= "Âge : " . ($form['age'] ?? '-') . "\n";
$contenu .= "Téléphone : " . ($form['telephone'] ?? '-') . "\n";
$contenu .= "Email : " . ($form['email'] ?? '-') . "\n";
$contenu .= "Filière : " . ($form['filiere'] ?? '-') . "\n";
$contenu .= "Année : " . ($form['annee'] ?? '-') . "\n";
$contenu .= "Modules : " . (!empty($form['modules']) ? implode(', ', $form['modules']) : '-') . "\n";
$contenu .= "Projets réalisés : " . ($form['projets_realises'] ?: '-') . "\n";
$contenu .= "Stages réalisés : " . ($form['projets_stages'] ?: '-') . "\n";
$contenu .= "Centres d'intérêt : " . (!empty($centres_affichage) ? implode(', ', $centres_affichage) : '-') . "\n";
$contenu .= "Langues : " . (!empty($langues_affichage) ? implode(', ', $langues_affichage) : '-') . "\n";
$contenu .= "Remarques : " . ($form['remarques'] ?: '-') . "\n";

$nomFichier = "fiche_{$form['nom']}_{$form['prenom']}.txt";

header('Content-Type: text/plain');
header("Content-Disposition: attachment; filename=\"$nomFichier\"");
echo $contenu;

    exit;
}
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Récapitulatif</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { margin-bottom: 20px; }
        p { margin: 6px 0; }
        b { width: 150px; display: inline-block; }
    </style>
</head>
<body class="m-8">
<h1 class="text-2xl font-bold mb-6">Fiche de renseignements</h1>

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
    <p><b>Fichier uploadé :</b> <?= $fichier_info ?></p>
    <br></br>
    
    <div class="flex gap-4">
  <form method="post">
    <button type="submit" name="valider" class="bg-blue-900 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
      VALIDER
    </button>
  </form>

  <form method="post" action="formulaire.php">
    <button type="submit" name="modifier" class="bg-blue-900 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
      Modifier
    </button>
  </form>
</div>



</body>
</html>
