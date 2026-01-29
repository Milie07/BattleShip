<?php

namespace App\class;

use App\class\Interfaces\PlayerInterface;

/**
 * Player - Gère un joueur et ses éléments de jeu
 * Chaque joueur a un nom, une grille, ses bateaux et un historique de tirs
 */
class Player implements PlayerInterface
{
  protected string $playerName;
  protected BoardGame $board;
  protected array $ships = [];           // Liste des bateaux du joueur
  protected array $shotsReceived = [];   // Tirs reçus par ce joueur
  protected array $shotsFired = [];      // Tirs effectués par ce joueur

  public function __construct(string $playerName)
  {
    $this->playerName = $playerName;
    $this->board = new BoardGame();
    $this->ships = [];
    $this->shotsReceived = [];
    $this->shotsFired = [];
  }

  // Getters
  public function getPlayerName(): string
  {
    return $this->playerName;
  }

  public function getBoard(): BoardGame
  {
    return $this->board;
  }

  public function getShips(): array
  {
    return $this->ships;
  }

  public function getShotsReceived(): array
  {
    return $this->shotsReceived;
  }

  public function getShotsFired(): array
  {
    return $this->shotsFired;
  }

  // Setters
  public function setPlayerName(string $playerName): void
  {
    $this->playerName = $playerName;
  }

  /**
   * Ajoute un bateau à la flotte du joueur
   * @param Ships $ship Le bateau à ajouter
   */
  public function addShip(Ships $ship): void
  {
    $this->ships[] = $ship;
  }

  /**
   * Valide le nom du joueur
   * Règles : non vide, entre 2 et 30 caractères
   * @param string $name Le nom à valider
   * @return array Résultat de la validation avec 'valid', 'message' et 'sanitized'
   */
  public static function validatePlayerName(string $name): array
  {
    $sanitized = trim($name);

    if (empty($sanitized)) {
      return ['valid' => false, 'message' => 'Le nom est requis', 'sanitized' => ''];
    }

    if (mb_strlen($sanitized) < 2 || mb_strlen($sanitized) > 30) {
      return ['valid' => false, 'message' => 'Le nom doit faire entre 2 et 30 caractères', 'sanitized' => ''];
    }

    return ['valid' => true, 'message' => '', 'sanitized' => $sanitized];
  }
  
  /**
   * Vérifie si un bateau peut être placé à ces coordonnées
   * Règles : pas de chevauchement, pas hors grille
   * @param array $coordinates Liste des coordonnées du bateau
   */
  public function validateShipPlacement(array $coordinates): bool
  {
    // Vérifier chaque coordonnée
    foreach ($coordinates as $coord) {
      // Vérifier si la coordonnée est dans la grille
      // On transforme "A1" en ['A', 1]
      $row = substr($coord, 0, 1);
      $col = intval(substr($coord, 1));

      if (!$this->board->validateCoordinates(['row' => $row, 'col' => $col])) {
        return false;
      }

      // Vérifier si la case n'est pas déjà occupée par un autre bateau
      foreach ($this->ships as $ship) {
        if ($ship->hasCoordinate($coord)) {
          return false; // Chevauchement détecté
        }
      }
    }

    return true;
  }

  /**
   * Place un bateau sur la grille du joueur
   * @param string $type Type du bateau
   * @param int $size Taille du bateau
   * @param array $coordinates Coordonnées occupées par le bateau
   * @param bool $orientation true = horizontal, false = vertical
   */
  public function placeShip(string $type, int $size, array $coordinates, bool $orientation = true): bool
  {
    // Vérifier que le placement est valide
    if (!$this->validateShipPlacement($coordinates)) {
      return false;
    }

    // Créer le bateau et l'ajouter
    $ship = new Ships($type, $size, $coordinates, $orientation);
    $this->addShip($ship);

    // Marquer les cases comme occupées sur la grille
    foreach ($coordinates as $coord) {
      $row = substr($coord, 0, 1);
      $col = intval(substr($coord, 1));
      $this->board->getGrid()[$row][$col] = $type;
    }

    return true;
  }

  /**
   * Vérifie si tous les bateaux du joueur sont placés (5 bateaux)
   */
  public function allShipsPlaced(): bool
  {
    if (count($this->ships) == 5) {
      return true;
    }
    return false;
  }

  /**
   * Reçoit un tir de l'adversaire
   * @param string $coord Coordonnée du tir (ex: "A1")
   * @param string $shooterName Nom de celui qui tire
   * @return array Résultat avec 'result' (RATE, TOUCHE, COULE), 'shipType' et 'shipCoordinates' si coulé
   */
  public function receiveShot(string $coord, string $shooterName): array
  {
    // Vérifier si on a déjà tiré à cette coordonnée
    foreach ($this->shotsReceived as $shot) {
      if ($shot->getCoordShot() == $coord) {
        return ['result' => 'DEJA_TIRE', 'shipType' => null, 'shipCoordinates' => []];
      }
    }

    // Chercher si un bateau est à cette coordonnée
    $result = 'RATE';
    $shipType = null;
    $shipCoordinates = [];

    foreach ($this->ships as $ship) {
      if ($ship->hasCoordinate($coord)) {
        // Le tir touche ce bateau
        $result = $ship->hit();
        $shipType = $ship->getType();

        // Si le bateau est coulé, on retourne toutes ses coordonnées
        if ($result === 'COULE') {
          $shipCoordinates = $ship->getCoordShips();
        }
        break;
      }
    }

    // Enregistrer le tir
    $shot = new Shots($shooterName, $coord, $result);
    $this->shotsReceived[] = $shot;

    return [
      'result' => $result,
      'shipType' => $shipType,
      'shipCoordinates' => $shipCoordinates
    ];
  }

  /**
   * Enregistre un tir effectué par ce joueur
   * @param string $coord Coordonnée visée
   * @param string $result Résultat du tir
   */
  public function addShotFired(string $coord, string $result): void
  {
    $shot = new Shots($this->playerName, $coord, $result);
    $this->shotsFired[] = $shot;
  }

  /**
   * Vérifie si ce joueur a déjà tiré à cette coordonnée
   * @param string $coord Coordonnée à vérifier
   */
  public function hasAlreadyFiredAt(string $coord): bool
  {
    foreach ($this->shotsFired as $shot) {
      if ($shot->getCoordShot() == $coord) {
        return true;
      }
    }
    return false;
  }

  /**
   * Vérifie si tous les bateaux du joueur sont coulés
   */
  public function hasLost(): bool
  {
    // Si pas de bateaux, pas encore de partie
    if (count($this->ships) == 0) {
      return false;
    }

    // Vérifier chaque bateau
    foreach ($this->ships as $ship) {
      if (!$ship->isSunk()) {
        return false; // Au moins un bateau n'est pas coulé
      }
    }

    return true; // Tous les bateaux sont coulés
  }

  /**
   * Compte le nombre de bateaux coulés
   */
  public function countSunkShips(): int
  {
    $count = 0;
    foreach ($this->ships as $ship) {
      if ($ship->isSunk()) {
        $count = $count + 1;
      }
    }
    return $count;
  }

  /**
   * Retourne l'état de tous les bateaux du joueur
   * Pour l'affichage du score
   */
  public function getShipsStatus(): array
  {
    $status = [];
    foreach ($this->ships as $ship) {
      $status[] = [
        'type' => $ship->getType(),
        'size' => $ship->getSize(),
        'hits' => $ship->getHits(),
        'sunk' => $ship->isSunk()
      ];
    }
    return $status;
  }

  /**
   * Retourne l'historique des tirs pour affichage sur la grille
   * Format: ['A1' => 'TOUCHE', 'B2' => 'RATE', ...]
   */
  public function getShotsHistoryForDisplay(): array
  {
    $history = [];
    foreach ($this->shotsReceived as $shot) {
      $history[$shot->getCoordShot()] = $shot->getResult();
    }
    return $history;
  }

  /**
   * Génère une mini-grille de rappel montrant où sont placés les bateaux
   * @param array $shipsData Les données des bateaux depuis la session
   * @return string Le HTML de la mini-grille
   */
  public static function displayMiniGrid(array $shipsData): string
  {
    // Créer un tableau pour stocker les cases occupées
    $occupiedCells = [];

    // Parcourir tous les bateaux et leurs coordonnées
    foreach ($shipsData as $ship) {
      $type = $ship['type'];
      foreach ($ship['coordinates'] as $coord) {
        $occupiedCells[$coord] = $type;
      }
    }

    // Générer le HTML de la mini-grille
    $html = '<div class="mini-grid-container">';
    $html .= '<h4 class="visually-hidden">Tes bateaux</h4>';
    $html .= '<table class="mini-grid">';

    // En-tête avec les numéros de colonnes
    $html .= '<thead><tr><th></th>';
    for ($col = 1; $col <= 10; $col++) {
      $html .= '<th>' . $col . '</th>';
    }
    $html .= '</tr></thead>';

    // Corps de la grille
    $html .= '<tbody>';
    $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

    foreach ($rows as $row) {
      $html .= '<tr><th>' . $row . '</th>';

      for ($col = 1; $col <= 10; $col++) {
        $coord = $row . $col;
        $cellClass = 'mini-cell';

        // Vérifier si cette case contient un bateau
        if (isset($occupiedCells[$coord])) {
          $shipType = $occupiedCells[$coord];
          $cellClass .= ' ship-placed ' . $shipType;
        }

        $html .= '<td class="' . $cellClass . '" data-coord="' . $coord . '"></td>';
      }

      $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    $html .= '</div>';

    return $html;
  }
}
