<?php
/**
 * Fichier de test pour vérifier le bon fonctionnement des classes
 * À exécuter en ligne de commande : php test.php
 */

require_once 'class/BoardGame.php';
require_once 'class/Ships.php';
require_once 'class/Shots.php';
require_once 'class/Player.php';
require_once 'class/AiPlayer.php';
require_once 'class/Game.php';

use App\class\BoardGame;
use App\class\Ships;
use App\class\Shots;
use App\class\Player;
use App\class\AiPlayer;
use App\class\Game;

echo "=== TESTS DES CLASSES BATAILLE NAVALE ===\n\n";

// ============================================
// TEST 1 : BoardGame
// ============================================
echo "--- TEST BoardGame ---\n";

$board = new BoardGame();
echo "Taille de la grille : " . $board->getSize() . " (attendu: 10)\n";
echo "Nombre de lignes : " . count($board->getRow()) . " (attendu: 10)\n";
echo "Nombre de colonnes : " . count($board->getColumn()) . " (attendu: 10)\n";

// Test validation coordonnées
$testCoord1 = $board->validateCoordinates(['row' => 'A', 'col' => 1]);
$testCoord2 = $board->validateCoordinates(['row' => 'Z', 'col' => 99]);
$testCoord3 = $board->validateCoordinates(['row' => 'J', 'col' => 9]);
echo "Coordonnée A1 valide : " . ($testCoord1 ? 'OUI' : 'NON') . " (attendu: OUI)\n";
echo "Coordonnée Z99 valide : " . ($testCoord2 ? 'OUI' : 'NON') . " (attendu: NON)\n";
echo "Coordonnée J9 valide : " . ($testCoord3 ? 'OUI' : 'NON') . " (attendu: OUI)\n";

echo "\n";

// ============================================
// TEST 2 : Ships
// ============================================
echo "--- TEST Ships ---\n";

$ship = new Ships('cruiser', 4, ['A1', 'A2', 'A3', 'A4'], true);
echo "Type du bateau : " . $ship->getType() . " (attendu: cruiser)\n";
echo "Taille : " . $ship->getSize() . " (attendu: 4)\n";
echo "Touches initiales : " . $ship->getHits() . " (attendu: 0)\n";
echo "Est coulé : " . ($ship->isSunk() ? 'OUI' : 'NON') . " (attendu: NON)\n";

// Test hasCoordinate
echo "A2 fait partie du bateau : " . ($ship->hasCoordinate('A2') ? 'OUI' : 'NON') . " (attendu: OUI)\n";
echo "B5 fait partie du bateau : " . ($ship->hasCoordinate('B5') ? 'OUI' : 'NON') . " (attendu: NON)\n";

// Test hit
echo "\nOn tire 3 fois sur le bateau...\n";
$result1 = $ship->hit();
echo "Tir 1 : $result1 (attendu: TOUCHE)\n";
$result2 = $ship->hit();
echo "Tir 2 : $result2 (attendu: TOUCHE)\n";
$result3 = $ship->hit();
echo "Tir 3 : $result3 (attendu: TOUCHE)\n";
$result4 = $ship->hit();
echo "Tir 4 : $result4 (attendu: COULE)\n";
echo "Est coulé maintenant : " . ($ship->isSunk() ? 'OUI' : 'NON') . " (attendu: OUI)\n";

echo "\n";

// ============================================
// TEST 3 : Shots
// ============================================
echo "--- TEST Shots ---\n";

$shot = new Shots('Jean', 'B5', 'TOUCHE');
echo "Tireur : " . $shot->getShooter() . " (attendu: Jean)\n";
echo "Coordonnée : " . $shot->getCoordShot() . " (attendu: B5)\n";
echo "Résultat : " . $shot->getResult() . " (attendu: TOUCHE)\n";
echo "Est un hit : " . ($shot->isHit() ? 'OUI' : 'NON') . " (attendu: OUI)\n";
echo "Même coord que B5 : " . ($shot->isSameCoord('B5') ? 'OUI' : 'NON') . " (attendu: OUI)\n";
echo "Même coord que C3 : " . ($shot->isSameCoord('C3') ? 'OUI' : 'NON') . " (attendu: NON)\n";

echo "\n";

// ============================================
// TEST 4 : Player
// ============================================
echo "--- TEST Player ---\n";

$player = new Player('Alice');
echo "Nom du joueur : " . $player->getPlayerName() . " (attendu: Alice)\n";
echo "Bateaux placés : " . count($player->getShips()) . " (attendu: 0)\n";

// Test placement bateau
$coords = ['C1', 'C2', 'C3'];
$placed = $player->placeShip('destroyer1', 3, $coords, true);
echo "Placement destroyer en C1-C3 : " . ($placed ? 'OK' : 'ECHEC') . " (attendu: OK)\n";
echo "Bateaux placés : " . count($player->getShips()) . " (attendu: 1)\n";

// Test chevauchement
$coords2 = ['C2', 'C3', 'C4'];
$placed2 = $player->placeShip('destroyer2', 3, $coords2, true);
echo "Placement avec chevauchement : " . ($placed2 ? 'OK' : 'ECHEC') . " (attendu: ECHEC)\n";

// Test receiveShot
echo "\nTest des tirs sur le joueur...\n";
$result = $player->receiveShot('C1', 'Ordinateur');
echo "Tir en C1 (bateau) : $result (attendu: TOUCHE)\n";
$result = $player->receiveShot('D5', 'Ordinateur');
echo "Tir en D5 (eau) : $result (attendu: RATE)\n";
$result = $player->receiveShot('C1', 'Ordinateur');
echo "Tir en C1 (déjà tiré) : $result (attendu: DEJA_TIRE)\n";

echo "\n";

// ============================================
// TEST 5 : AiPlayer
// ============================================
echo "--- TEST AiPlayer ---\n";

$ai = new AiPlayer();
echo "Nom de l'IA : " . $ai->getPlayerName() . " (attendu: Ordinateur)\n";

$ai->placeShipsRandomly();
echo "Bateaux placés par l'IA : " . count($ai->getShips()) . " (attendu: 5)\n";

// Vérifier que tous les bateaux ont été placés correctement
$shipsStatus = $ai->getShipsStatus();
echo "Détail des bateaux de l'IA :\n";
foreach ($shipsStatus as $s) {
    echo "  - " . $s['type'] . " (taille: " . $s['size'] . ")\n";
}

// Test chooseTarget
$target = $ai->chooseTarget();
echo "Cible choisie par l'IA : $target\n";

echo "\n";

// ============================================
// TEST 6 : Game (partie complète simulée)
// ============================================
echo "--- TEST Game ---\n";

$game = new Game('Bob');
echo "Joueur : " . $game->getPlayer()->getPlayerName() . " (attendu: Bob)\n";
echo "Statut initial : " . $game->getStatus() . " (attendu: PLACEMENT)\n";

// Placer les bateaux du joueur
$game->getPlayer()->placeShip('aircraftCarrier', 5, ['A1', 'A2', 'A3', 'A4', 'A5'], true);
$game->getPlayer()->placeShip('cruiser', 4, ['B1', 'B2', 'B3', 'B4'], true);
$game->getPlayer()->placeShip('destroyer1', 3, ['C1', 'C2', 'C3'], true);
$game->getPlayer()->placeShip('destroyer2', 3, ['D1', 'D2', 'D3'], true);
$game->getPlayer()->placeShip('torpedoBoat', 2, ['E1', 'E2'], true);

echo "Tous les bateaux placés : " . ($game->getPlayer()->allShipsPlaced() ? 'OUI' : 'NON') . " (attendu: OUI)\n";

// Démarrer la partie
$started = $game->startGame();
echo "Partie démarrée : " . ($started ? 'OUI' : 'NON') . " (attendu: OUI)\n";
echo "Statut après démarrage : " . $game->getStatus() . " (attendu: EN_COURS)\n";
echo "Tour actuel : " . $game->getCurrentTurn() . " (attendu: PLAYER)\n";

// Simuler un tir du joueur
$turnResult = $game->playerTurn('A1');
echo "Tir joueur en A1 - succès : " . ($turnResult['success'] ? 'OUI' : 'NON') . "\n";
echo "Résultat : " . $turnResult['result'] . " (RATE, TOUCHE ou COULE)\n";

echo "\n";

// ============================================
// RÉSUMÉ
// ============================================
echo "=== TESTS TERMINÉS ===\n";
echo "Si tous les résultats correspondent aux attentes, les classes fonctionnent correctement.\n";
