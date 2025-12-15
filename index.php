<?php
include 'class/Ships.php';
include 'class/BoardGame.php';
include 'class/Player.php';  
include 'class/Shots.php';
include 'class/Game.php';
include 'class/AiPlayer.php';

$board = new BoardGame(10);

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
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>">
    <title>BattleShip</title>
</head>
<body>
  <header>
    <h1>Bienvenue !</h1>
    <h2>Inscris ton nom et démarre ton aventure maritime !</h2>
    <form action="GET" class="header_form">
      <input type="text" placeholder="Inscris ton nom !"class="header_input">
      <!-- Soumet le formulaire avec le nom et fait apparaitre la modale du joueur -->
      <div class="header_buttons">
        <button type="submit"class="buttons">
          <i class="fa-solid fa-play fa-xl"></i>
          </button>
        <button type="reset"class="buttons">
          <i class="fa-solid fa-arrow-rotate-right fa-xl"></i>
        </button>
      </div>
    </form>
  </header>

  <main>
    <!-- Remaining ships player -->
    <article class="ships_player">
      <h3>Bateaux restants {Joueur 1} </h3>
      <!-- Affiche les bateaux restants du joueur 1 à gauche -->
      <div class="ships_container">
        <div class="porte-avion"></div>
        <div class="croiseur"></div>
        <div class="contre-torpilleur"></div>
        <div class="sous-marin"></div>
        <div class="torpilleur"></div>
      </div>
    </article>
    
    <!-- Game board -->
    <div class="game_board">
      <?php $board->displayGrid(10); ?>
    </div>
      <!-- Affiche la grille de jeu au centre -->
      
    <!-- Remaining ships computer -->
    <article class="ships_computer">
      <h3>Bateaux restants {Ordinateur} </h3>
      <div class="ships_container">
        <!-- Affiche les bateaux restants de l'ordianteur à droite-->
        <div class="porte-avion"></div>
        <div class="croiseur"></div>
        <div class="contre-torpilleur"></div>
        <div class="sous-marin"></div>
        <div class="torpilleur"></div>
      </div>
    </article>
  </main>
  <footer>
    <p>© 2025 BattleShip - Student Project - Made by Milie</p>
  </footer>
  <script src="assets/js/script.js"></script>
</body>
</html>
