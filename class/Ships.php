<?php
namespace App\class;

class Ships {
  private string $type;
  private int $size;
  private array $coordShips; 
  private bool $orientation = true; # à l'horizontal par défaut 
  private int $hits = 0; # 0 par défaut la bateau n'a aucun dommage

  public function __construct(string $type, int $size, array $coordShips, bool $orientation = true, int $hits = 0) 
  {
    $this->type = $type;
    $this->size = $size;
    $this->coordShips = $coordShips;
    $this->orientation = $orientation;
    $this->hits = $hits;
  }

  public function getType(): string
  {
    return $this->type;
  }
  public function setType(string $type): void
  {
    $this->type = $type;
  }
  public function getSize(): int
  {
    return $this->size;
  }
  public function setSize(int $size): void
  {
    $this->size = $size;
  }
  public function getCoordShips(): array
  {
    return $this->coordShips;
  }
  public function setCoordShips(array $coordShips): void
  {
    $this->coordShips = $coordShips;
  }
  public function getOrientation(): bool
  {
    return $this->orientation;
  }
  public function setOrientation(bool $orientation): void
  {
    $this->orientation = $orientation;
  }
  public function getHits(): int
  {
    return $this->hits;
  }
  public function setHits(int $hits): void
  {
    $this->hits = $hits;
  }

  
  /**
   * Génère le HTML d'un bateau avec ses cellules individuelles
   * @param string $type Type du bateau (aircraftCarrier, cruiser, etc.)
   * @param int $size Nombre de cellules du bateau
   * @param bool $withId Si true, ajoute un attribut id au bateau
   * @return string Le HTML du bateau
  */
  public static function renderShip(string $type, int $size, bool $withId = false, bool $withRotateBtn = false): string
  {
    $idAttribute = $withId ? " id=\"$type\"" : "";
    $html = "<div class=\"ship $type\" data-size=\"$size\" data-orientation=\"horizontal\" data-name=\"$type\" draggable=\"true\"$idAttribute>";

    // Bouton de rotation (optionnel)
    if ($withRotateBtn) {
      $html .= "<button type=\"button\" class=\"rotate-btn\" title=\"Pivoter\">↻</button>";
    }

    // Générer les cellules individuelles
    for ($i = 0; $i < $size; $i++) {
      $html .= "<div class=\"ship-cell\" data-cell-index=\"$i\"></div>";
    }

    $html .= "</div>";
    return $html;
  }
  
  /**
   * Génère tous les bateaux standard du jeu
   * @param bool $withIds Si true, ajoute des attributs id aux bateaux
   * @param bool $withRotateBtn Si true, ajoute un bouton de rotation
   * @return string Le HTML de tous les bateaux
  */
  public static function renderAllShips(bool $withIds = false, bool $withRotateBtn = false): string
  {
    $ships = [
      'aircraft-carrier' => 5,
      'cruiser' => 4,
      'destroyer1' => 3,
      'destroyer2' => 3,
      'torpedo-boat' => 2
    ];

    $html = '';
    foreach ($ships as $type => $size) {
      $html .= self::renderShip($type, $size, $withIds, $withRotateBtn);
    }

    return $html;
  }
  
  /**
   * Vérifie si le bateau est coulé
   * Un bateau est coulé quand le nombre de touches égale sa taille
   */
  public function isSunk(): bool
  {
    if ($this->hits >= $this->size) {
      return true;
    }
    return false;
  }

  /**
   * Enregistre un tir sur le bateau
   * Retourne le résultat : TOUCHE ou COULE
   */
  public function hit(): string
  {
    // On ajoute une touche au bateau
    $this->hits = $this->hits + 1;

    // On vérifie si le bateau est coulé après ce tir
    if ($this->isSunk()) {
      return 'COULE';
    }
    return 'TOUCHE';
  }

  /**
   * Vérifie si une coordonnée fait partie de ce bateau
   * @param string $coord La coordonnée à vérifier (ex: "A1", "B5")
   */
  public function hasCoordinate(string $coord): bool
  {
    // On parcourt toutes les coordonnées du bateau
    foreach ($this->coordShips as $shipCoord) {
      if ($shipCoord == $coord) {
        return true;
      }
    }
    return false;
  }
}

