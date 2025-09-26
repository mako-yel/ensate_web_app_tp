<?php
session_start();
require_once 'tcpdf/tcpdf.php';

// Vérification des champs obligatoires
$champs_obligatoires = ['nom', 'prenom', 'telephone', 'email', 'formations', 'projets_stages', 'competences'];
foreach ($champs_obligatoires as $champ) {
    if (empty($_POST[$champ])) {
        die("Erreur: Le champ $champ est obligatoire.");
    }
}

// Vérification de la photo
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    die("Erreur: La photo est obligatoire.");
}

// Traitement de la photo
$photo_tmp = $_FILES['photo']['tmp_name'];
$photo_data = base64_encode(file_get_contents($photo_tmp));
$photo_src = 'data:' . $_FILES['photo']['type'] . ';base64,' . $photo_data;

// Récupération des données
$donnees = [
    'nom' => $_POST['nom'],
    'prenom' => $_POST['prenom'],
    'age' => $_POST['age'] ?? '',
    'telephone' => $_POST['telephone'],
    'email' => $_POST['email'],
    'adresse' => $_POST['adresse'] ?? '',
    'ville' => $_POST['ville'] ?? '',
    'formations' => $_POST['formations'],
    'projets_stages' => $_POST['projets_stages'],
    'competences' => $_POST['competences'],
    'langues' => $_POST['langues'] ?? [],
    'centres_interet' => $_POST['centres_interet'] ?? [],
    'projets_realises' => $_POST['projets_realises'] ?? '',
    'remarques' => $_POST['remarques'] ?? ''
];

// Création du PDF
class MonCVPDF extends TCPDF {
    public function Header() {
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(0, 10, 'cv', 0, 1, 'C');
        $this->Line(10, 25, 200, 25);
        $this->Ln(10);
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
    }
}

$pdf = new MonCVPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('Générateur CV');
$pdf->SetAuthor($donnees['prenom'] . ' ' . $donnees['nom']);
$pdf->SetTitle('CV - ' . $donnees['prenom'] . ' ' . $donnees['nom']);
$pdf->SetMargins(15, 30, 15);
$pdf->AddPage();

// Style CSS pour le PDF
$style = '
    <style>
        .section { margin-bottom: 15px; }
        .titre-section { 
            background-color: #f0f0f0; 
            padding: 5px; 
            font-weight: bold; 
            font-size: 14px;
        }
        .info-personnelle { font-size: 12px; }
        .photo { text-align: center; margin-bottom: 10px; }
    </style>
';

// Contenu du CV
$contenu = $style;

// Section photo et coordonnées
$contenu .= '
    <div class="photo">
        <img src="'.$photo_src.'" width="100" style="border-radius: 50%;">
    </div>
    <div class="section">
        <div class="titre-section">INFORMATIONS PERSONNELLES</div>
        <div class="info-personnelle">
            <strong>'.$donnees['prenom'].' '.$donnees['nom'].'</strong><br/>
            Âge: '.$donnees['age'].' ans<br/>
            Téléphone: '.$donnees['telephone'].'<br/>
            Email: '.$donnees['email'].'<br/>
            Adresse: '.$donnees['adresse'].' '.$donnees['ville'].'
        </div>
    </div>
';

// Section Formations
$contenu .= '
    <div class="section">
        <div class="titre-section">FORMATIONS ET STAGES</div>
        '.nl2br(htmlspecialchars($donnees['formations'])).'
        <br/><br/>
        <strong>Stages réalisés:</strong><br/>
        '.nl2br(htmlspecialchars($donnees['projets_stages'])).'
    </div>
';

// Section Compétences
$contenu .= '
    <div class="section">
        <div class="titre-section">COMPÉTENCES</div>
        '.nl2br(htmlspecialchars($donnees['competences'])).'
    </div>
';

// Section Langues
if (!empty($donnees['langues'])) {
    $contenu .= '
        <div class="section">
            <div class="titre-section">LANGUES</div>
            '.implode(', ', $donnees['langues']).'
        </div>
    ';
}

// Section Centres d'intérêt
if (!empty($donnees['centres_interet'])) {
    $contenu .= '
        <div class="section">
            <div class="titre-section">CENTRES D\'INTÉRÊT</div>
            '.implode(', ', $donnees['centres_interet']).'
        </div>
    ';
}

// Section Projets
if (!empty($donnees['projets_realises'])) {
    $contenu .= '
        <div class="section">
            <div class="titre-section">PROJETS RÉALISÉS</div>
            '.nl2br(htmlspecialchars($donnees['projets_realises'])).'
        </div>
    ';
}

// Section Remarques
if (!empty($donnees['remarques'])) {
    $contenu .= '
        <div class="section">
            <div class="titre-section">INFORMATIONS COMPLÉMENTAIRES</div>
            '.nl2br(htmlspecialchars($donnees['remarques'])).'
        </div>
    ';
}

// Génération du PDF
$pdf->writeHTML($contenu, true, false, true, false, '');

// Sauvegarde en session pour édition ultérieure
$_SESSION['form'] = $donnees;

// Output du PDF
$pdf->Output('cv_'.$donnees['prenom'].'_'.$donnees['nom'].'.pdf', 'D');
?>