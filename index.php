<?php
include 'class/Ships.php';
include 'class/BoardGame.php';
include 'class/Player.php';
include 'class/Shots.php';
include 'class/Game.php';
include 'class/AiPlayer.php';

use App\class\BoardGame;
use App\class\Player;
use App\class\Ships;

// Créer la grille principale
$board = new BoardGame(10);

// Récupérer le nom du joueur depuis le formulaire
$playerName = isset($_POST['name']) && !empty($_POST['name']) ? $_POST['name'] : null;
$player = $playerName ? new Player($playerName) : null;

// Afficher 

?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Carter+One&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/1c347601e2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/modale.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>BattleShip</title>
</head>
<body data-open-modal="<?php echo $playerName ? 'true' : 'false'; ?>">
  <header>
    <h1>Bienvenue !</h1>
    <h2>Inscris ton nom et démarre ton aventure maritime !</h2>
    <form class="header_form" method="POST" action="index.php" >
      <input class="header_input" type="text" name="name" placeholder="Inscris ton nom !">
      <!-- Soumet le formulaire avec le nom et fait apparaitre la modale du joueur -->
      <div class="header_buttons">
        <button class="buttons open_modal_btn" type="submit">
          <i class="fa-solid fa-play fa-xl"></i>
          </button>
        <button class="buttons" type="reset">
          <i class="fa-solid fa-arrow-rotate-right fa-xl"></i>
        </button>
      </div>
    </form>
  </header>

  <main>
    <!-- Remaining ships player -->
    <section class="ships_player">
      <h3>Bateaux restants <?php echo $player ? htmlspecialchars($player->getPlayerName()) : 'Joueur'; ?></h3>
      <!-- Affiche les bateaux restants du joueur 1 à gauche -->
      <div class="ships_container">
        <div class="aircraftCarrier"></div>
        <div class="cruiser"></div>
        <div class="destroyer1"></div>
        <div class="destroyer2"></div>
        <div class="torpedoBoat"></div>
      </div>
    </section>
    
    <!-- Game board -->
    <section class="game_board">
      <?php echo $board->displayGrid(); ?>
    </section>
      <!-- Affiche la grille de jeu au centre -->
      
    <!-- Remaining ships computer -->
    <section class="ships_computer">
      <h3>Bateaux restants Ordinateur </h3>
      <div class="ships_container">
        <!-- Affiche les bateaux restants de l'ordianteur à droite-->
        <div class="aircraftCarrier"></div>
        <div class="cruiser"></div>
        <div class="destroyer1"></div>
        <div class="destroyer2"></div>
        <div class="torpedoBoat"></div>
      </div>
    </section>
  </main>
  <footer>
    <p>© 2025 BattleShip - Student Project - Made by Milie</p>
  </footer>

  <!-- Modale choix des bateaux Joueur -->
  <div class="modal toggle_modal" >
    <div class="modal_content">
      <p>Place les bateaux sur la grille et sauvegarde ! <br> Clique dessus pour les positionner à la verticale ou à l'horizontale !</p>
      <div class="content">
        <div class="ships_modal">
          <div class="aircraftCarrier"></div>
          <div class="cruiser"></div>
          <div class="destroyer1"></div>
          <div class="destroyer2"></div>
          <div class="torpedoBoat"></div>
        </div>
        <?php echo $board->displayGrid(); ?>
        <button class="close_modal_btn toggle_modal">X</button>
      </div>
      <button class="buttons" type="submit">Sauvegarder</button>
    </div>
  </div>

  <!-- <script src="assets/js/models/modale.js"></script>
  <script src="assets/js/models/grid.js"></script> -->
  <script src="assets/js/script.js" type="module"></script>
</body>

</html>
