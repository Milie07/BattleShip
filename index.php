<?php
include 'class/Ships.php';
include 'class/BoardGame.php';
include 'class/Player.php';  
include 'class/Shots.php';
include 'class/Game.php';
include 'class/AiPlayer.php';
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/1c347601e2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>BattleShip</title>
</head>
<body>
  <header>
      <h1>Bienvenue !</h1>
      <h2>Inscris ton nom et démarre ton aventure maritime !</h2>
  </header>
  <main>
    
    <form action="GET" class="name_form">
      <input type="text" placeholder="Inscris ton nom !"class="input_name">
      <!-- Soumet le formulaire avec le nom et fait apparaitre la modale du joueur -->
      <button type="submit"class="main_buttons">
        <i class="fa-solid fa-play"></i>
        </button>
      <button type="reset"class="main_buttons">
        <i class="fa-solid fa-arrow-rotate-right"></i>
      </button>
    </form>

    <!-- Remaining ships player -->
    <article class="ships_player">
      <h3>Bateaux restants {Joueur 1} </h3>
      <div class="ships_container">
        <!-- Affiche les bateaux restants du joueur 1 à gauche -->
        <div class="porte-avion"></div>
        <div class="croiseur"></div>
        <div class="contre-torpilleur"></div>
        <div class="sous-marin"></div>
        <div class="torpilleur"></div>
      </div>
    </article>
    
    <!-- Game board -->
    <div class="game_board">
      <!-- Affiche la grille de jeu au centre -->
    </div>
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
  <script src="assets/js/app.js"></script>
</body>
</html>
