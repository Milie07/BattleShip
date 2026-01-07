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

  public function isSunk(): void
  {
    # à implémenter
  }
  public function hit(): void
  {
    # à implémenter
  }
  public function allShipsSunk(): void
  {
    # à implémenter
  }




}

