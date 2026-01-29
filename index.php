<?php
// Démarrer la session pour garder les données entre les pages
session_start();

// Si on demande une réinitialisation, on vide la session
if (isset($_GET['reset'])) {
  session_destroy();
  header('Location: index.php');
  exit;
}

// Interfaces (doivent être chargées en premier)
include 'class/Interfaces/BoardInterface.php';
include 'class/Interfaces/PlayerInterface.php';

// Classes
include 'class/Ships.php';
include 'class/BoardGame.php';
include 'class/Shots.php';
include 'class/Player.php';
include 'class/AiPlayer.php';
include 'class/Game.php';

use App\class\BoardGame;
use App\class\Player;
use App\class\Ships;

// Créer la grille principale
$board = new BoardGame(10);

// Récupérer le nom du joueur depuis le formulaire
if (isset($_POST['name']) && !empty($_POST['name'])) {
  // Le joueur vient de soumettre son nom
  $playerName = $_POST['name'];
  $_SESSION['playerName'] = $playerName;
} else {
  // récupérer le nom depuis la session
  $playerName = isset($_SESSION['playerName']) ? $_SESSION['playerName'] : null;
}

// Créer le joueur si il a un nom
$player = $playerName ? new Player($playerName) : null;

// Vérifier si les bateaux ont été placés et sauvegardés
$shipsPlaced = false;
$playerShips = [];

if (isset($_SESSION['playerShips']) && isset($_SESSION['gameStarted'])) {
  $shipsPlaced = true;
  $playerShips = $_SESSION['playerShips'];
  // Créer le jeu si il n'existe pas déjà en session
  if (!isset($_SESSION['game'])) {
    $game = new \App\class\Game($playerName);
    
    // Placer les bateaux du joueur
    foreach ($playerShips as $shipData) {
      $game->getPlayer()->placeShip(
        $shipData['type'],
        $shipData['size'],
        $shipData['coordinates'],
        $shipData['orientation'] === 'horizontal'
      );
    }
    
    // Démarrer le jeu (l'IA place ses bateaux)
    $game->startGame();
    
    // Sauvegarder dans la session
    $_SESSION['game'] = serialize($game);
  } else {
    $game = unserialize($_SESSION['game']);
  }
}

?>

<!DOCTYPE html>
<html lang="fr-FR">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Projet d'étude reprenant le jeu classique de la Bataille Navale. Placez vos bateaux et affrontez l'ordinateur !">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Carter+One&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/1c347601e2.js" crossorigin="anonymous"></script>
  <!-- <link rel="stylesheet" href="assets/css/modale.css"> -->
  <link rel="stylesheet" href="assets/css/styles.css">

  <title>BattleShip</title>
</head>

<body data-open-modal="<?php echo ($playerName && !$shipsPlaced) ? 'true' : 'false'; ?>">
  <header>
    <h1>Bienvenue !</h1>
    <h2>Inscris ton nom et démarre ton aventure maritime !</h2>
    <form class="header-form" method="POST" action="index.php" aria-label="Formulaire d'inscription au jeu">
      <input class="header-input" id="player-name"type="text" name="name" placeholder="Inscris ton nom !" aria-label="Nom du joueur"
        aria-required="true">
      <!-- Soumet le formulaire avec le nom et fait apparaitre la modale du joueur -->
      <div class="header-buttons">
        <button class="buttons open-modal_btn" type="submit" aria-label="Démarrer la partie">
          <i class="fa-solid fa-play fa-xl" aria-hidden="true"></i>
        </button>
        <a class="buttons reset-btn" href="index.php?reset=1" aria-label="Réinitialiser le jeu">
          <i class="fa-solid fa-arrow-rotate-right fa-xl" aria-hidden="true"></i>
        </a>
      </div>
    </form>
  </header>

  <main>
    <!-- Game board -->
    <section class="game-board" aria-label="Plateau de jeu principal">
      <?php echo $board->displayGrid([], 'mainGrid'); ?>
    </section>
    <!-- Affiche la grille de jeu au centre -->
    <section class="ships">
      <!-- Remaining ships player -->
      <div class="ships-player" aria-labelledby="player-title">
        <h3 id="player-title"><?php echo $player ? htmlspecialchars($player->getPlayerName(), ENT_QUOTES, 'UTF-8') : 'Joueur'; ?></h3>
        <!-- Affiche les bateaux restants du joueur 1 à gauche -->
        <div class="ships-container-player">
          <?php echo Ships::renderAllShips(false); ?>
        </div>
      </div>
      <!-- Remaining ships computer -->
      <div class="ships-computer" aria-labelledby="computer-title">
        <h3 id="computer-title">Ordinateur </h3>
        <div class="ships-container-computer">
          <!-- Affiche les bateaux restants de l'ordinateur à droite-->
          <?php echo Ships::renderAllShips(false); ?>
        </div>
      </div>
    </section>
    <!-- Mini-grille de rappel (affichée seulement si les bateaux sont placés) -->
    <?php if ($shipsPlaced): ?>
      <?php echo Player::displayMiniGrid($playerShips); ?>
    <?php endif; ?>
  </main>
  <footer>
    <p>© 2025 BattleShip - Student Project - Made by Milie</p>
  </footer>

  <!-- Modale choix des bateaux Joueur -->
  <div class="modal toggle-modal" role="dialog"
    aria-modal="true"
    aria-labelledby="modal-title"
    aria-describedby="modal-description">
    <div class="modal-content">
      <h2 id="modal-title" class="visually-hidden">Placement des bateaux</h2>
      <p id="modal-description">Place les bateaux sur la grille ! <br> Clique dessus pour les positionner à la verticale ou à l'horizontale et sauvegarde.</p>
      <div class="content">
        <div class="ships-modal">
          <?php echo Ships::renderAllShips(true); ?>
        </div>
        <?php echo $board->displayGrid([], 'modalGrid'); ?>
        <button class="close-modal-btn toggle-modal" aria-label="Fermer la fenêtre">X</button>
      </div>
      <div class="save-button" role="button">
        <button class="buttons" type="submit" aria-label="Sauvegarder le placement des bateaux">Sauvegarder</button>
      </div>
    </div>
  </div>

  <!-- <script src="assets/js/models/modale.js"></script>
  <script src="assets/js/models/grid.js"></script> -->
  <script src="assets/js/script.js" type="module"></script>
  <script src="assets/js/models/dragAndDrop.js"></script>
</body>

</html>