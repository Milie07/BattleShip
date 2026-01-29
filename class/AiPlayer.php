<?php
namespace App\class;

/**
 * AiPlayer - Représente l'ordinateur comme adversaire
 * Hérite de Player et ajoute le comportement automatique (placement et tirs)
 */
class AiPlayer extends Player
{
  public function __construct()
  {
    // L'ordinateur s'appelle toujours "Ordinateur"
    parent::__construct('Ordinateur');
  }

  /**
   * Place tous les bateaux de l'ordinateur de façon aléatoire
   * Respecte les règles : pas de chevauchement, dans la grille
   */
  public function placeShipsRandomly(): void
  {
    // Liste des bateaux à placer avec leur taille
    $shipsToPlace = [
      'aircraft-carrier' => 5,
      'cruiser' => 4,
      'destroyer1' => 3,
      'destroyer2' => 3,
      'torpedo-boat' => 2
    ];

    // Placer chaque bateau
    foreach ($shipsToPlace as $type => $size) {
      $placed = false;

      // Essayer jusqu'à trouver une position valide
      while (!$placed) {
        // Choisir une orientation au hasard
        $horizontal = rand(0, 1) == 1;

        // Choisir une position de départ au hasard
        $coordinates = $this->generateRandomCoordinates($size, $horizontal);

        // Vérifier si le placement est valide
        if ($this->validateShipPlacement($coordinates)) {
          $this->placeShip($type, $size, $coordinates, $horizontal);
          $placed = true;
        }
      }
    }
  }

  /**
   * Génère des coordonnées aléatoires pour un bateau
   * @param int $size Taille du bateau
   * @param bool $horizontal Orientation du bateau
   */
  private function generateRandomCoordinates(int $size, bool $horizontal): array
  {
    $coordinates = [];
    $rows = $this->board->getRow();       // ['A', 'B', 'C', ...]
    $cols = $this->board->getColumn();    // [1, 2, 3, ...]

    if ($horizontal) {
      // Bateau horizontal : même ligne, colonnes consécutives
      $rowIndex = rand(0, 9);
      $colStart = rand(0, 10 - $size);

      for ($i = 0; $i < $size; $i++) {
        $coordinates[] = $rows[$rowIndex] . ($colStart + 1 + $i);
      }
    } else {
      // Bateau vertical : même colonne, lignes consécutives
      $rowStart = rand(0, 10 - $size);
      $colIndex = rand(0, 9);

      for ($i = 0; $i < $size; $i++) {
        $coordinates[] = $rows[$rowStart + $i] . $cols[$colIndex];
      }
    }

    return $coordinates;
  }

  /**
   * Choisit une coordonnée de tir pour l'ordinateur
   * Tir aléatoire parmi les cases non encore visées
   */
  public function chooseTarget(): string
  {
    $rows = $this->board->getRow();
    $cols = $this->board->getColumn();
    $target = '';
    $found = false;

    // Chercher une case non encore tirée
    while (!$found) {
      $rowIndex = rand(0, 9);
      $colIndex = rand(0, 9);
      $target = $rows[$rowIndex] . $cols[$colIndex];

      // Vérifier qu'on n'a pas déjà tiré ici
      if (!$this->hasAlreadyFiredAt($target)) {
        $found = true;
      }
    }

    return $target;
  }
}
