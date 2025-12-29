<?php
namespace App\class;

/**
 * BoardGame - Gère la structure de la grille de jeu (10x10)
 * Responsabilité : UNIQUEMENT la structure et validation des coordonnées
 */
class BoardGame
{
  protected array $grid;
  protected array $row; // Lignes (A-J)
  protected array $column; // Colonnes (1-10)
  private int $size = 10;

  public function __construct(int $size = 10)
  {
    $this->size = $size;
    $this->row = range('A', chr(ord('A') + $size - 1));
    $this->column = range(1, $size);
    $this->grid = [];
    $this->createGrid();
  }

  // Getters
  public function getGrid(): array
  {
    return $this->grid;
  }

  public function getRow(): array
  {
    return $this->row;
  }

  public function getColumn(): array
  {
    return $this->column;
  }

  public function getSize(): int
  {
    return $this->size;
  }

  /**
   * Crée la structure 10x10 (A-J, 1-10)
   * Chaque case = null par défaut
   * Structure: $grid['A'][1], $grid['B'][5], etc.
   */
  public function createGrid(): void
  {
    foreach ($this->row as $rowLetter) {
      $this->grid[$rowLetter] = [];
      foreach ($this->column as $columnNumber) {
        $this->grid[$rowLetter][$columnNumber] = null;
      }
    }
  }

  /**
   * Valide qu'une coordonnée existe dans la grille
   * @param array $coord Format: ['A', 1] ou ['row' => 'A', 'col' => 1]
   * @return bool true si la coordonnée existe, false sinon
   */
  public function validateCoordinates(array $coord): bool
  {
    // Gère les deux formats possibles
    if (isset($coord['row']) && isset($coord['col'])) {
      $rowLetter = $coord['row'];
      $columnNumber = $coord['col'];
    } elseif (isset($coord[0]) && isset($coord[1])) {
      $rowLetter = $coord[0];
      $columnNumber = $coord[1];
    } else {
      return false;
    }
    // Vérifie que la ligne existe (A-J)
    if (!in_array($rowLetter, $this->row)) {
      return false;
    }
    // Vérifie que la colonne existe (1-10)
    if (!in_array($columnNumber, $this->column)) {
      return false;
    }
    return true;
  }

  /**
   * Génère le HTML de la grille principale
   * @param array $shotsHistory Historique des tirs pour afficher les croix
   * @return string HTML de la grille
   */
  public function displayGrid(array $shotsHistory = []): string
  {
    $html = '<table class="battleship-grid">';

    // En-tête avec les numéros de colonnes (1-10)
    $html .= '<thead><tr><th></th>';
    foreach ($this->column as $col) {
      $html .= "<th>$col</th>";
    }
    $html .= '</tr></thead>';

    // Corps de la grille
    $html .= '<tbody>';
    foreach ($this->row as $rowLetter) {
      $html .= "<tr><th>$rowLetter</th>";
      foreach ($this->column as $col) {
        $cellId = $rowLetter . $col;
        $cellClass = 'cell';

        // Vérifie si un tir a été effectué à cette position
        if (isset($shotsHistory[$cellId])) {
          $cellClass .= ' shot-' . strtolower($shotsHistory[$cellId]);
        }

        $html .= "<td class='$cellClass' data-coord='$cellId'></td>";
      }
      $html .= '</tr>';
    }
    $html .= '</tbody></table>';

    return $html;
  }
}