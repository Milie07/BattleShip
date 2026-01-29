<?php
/**
 * Fichier qui reçoit les bateaux placés par le joueur
 * et les enregistre dans la session PHP
 */

// Démarrer la session pour pouvoir sauvegarder les données
session_start();

// On dit au navigateur que la réponse sera en JSON
header('Content-Type: application/json');

// Récupérer les données envoyées par le JavaScript
$jsonData = file_get_contents('php://input');

// Transformer le JSON en tableau PHP
$data = json_decode($jsonData, true);

// Vérifier si on a bien reçu des données
if ($data == null) {
    echo json_encode([
        'success' => false,
        'message' => 'Pas de données reçues'
    ]);
    exit;
}

// Vérifier si on a bien les bateaux
if (!isset($data['ships'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Pas de bateaux dans les données'
    ]);
    exit;
}

// Récupérer la liste des bateaux
$ships = $data['ships'];

// Vérifier qu'on a bien 5 bateaux
if (count($ships) != 5) {
    echo json_encode([
        'success' => false,
        'message' => 'Il faut placer les 5 bateaux'
    ]);
    exit;
}

// Tout est bon, on sauvegarde en session
$_SESSION['playerShips'] = $ships;
$_SESSION['gameStarted'] = true;

// Envoyer une réponse de succès au JavaScript
echo json_encode([
    'success' => true,
    'message' => 'Bateaux enregistrés'
]);
