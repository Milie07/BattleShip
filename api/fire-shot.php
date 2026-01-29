<?php
/**
 * Endpoint AJAX pour traiter les tirs du joueur
 * CP7 - Développement back-end
 *
 * Utilise le GameService pour la logique métier
 */

session_start();
header('Content-Type: application/json');

// Autoloading (composer ou manuel)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    // Interfaces en premier
    require_once __DIR__ . '/../class/Interfaces/BoardInterface.php';
    require_once __DIR__ . '/../class/Interfaces/PlayerInterface.php';
    // Classes
    require_once __DIR__ . '/../class/Ships.php';
    require_once __DIR__ . '/../class/BoardGame.php';
    require_once __DIR__ . '/../class/Shots.php';
    require_once __DIR__ . '/../class/Player.php';
    require_once __DIR__ . '/../class/AiPlayer.php';
    require_once __DIR__ . '/../class/Game.php';
    require_once __DIR__ . '/../class/Services/GameService.php';
}

use App\class\Services\GameService;

// Vérifie la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// Récupère les données JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['coordinate'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Coordonnée manquante']);
    exit;
}

// Vérifie qu'une partie est en cours
if (!isset($_SESSION['game'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Aucune partie en cours']);
    exit;
}

try {
    // Initialiser le service
    $gameService = new GameService();

    // Récupérer l'objet Game depuis la session
    $game = unserialize($_SESSION['game']);

    // Traiter le tir via le service
    $response = $gameService->processPlayerShot($game, $data['coordinate']);

    // Sauvegarder l'état du jeu
    $_SESSION['game'] = serialize($game);

    echo json_encode($response);

} catch (\InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
