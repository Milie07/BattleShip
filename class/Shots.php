<?php
namespace App\class;

/**
 * Shots - Représente un tir dans le jeu
 * Stocke les informations d'un tir : qui a tiré, où, et le résultat
 */
class Shots
{
  private string $shooter;      // Qui a tiré (joueur ou ordinateur)
  private string $coordShot;    // Coordonnée du tir (ex: "A1", "B5")
  private string $result;       // Résultat : RATE, TOUCHE ou COULE
  private string $timestamp;    // Moment du tir

  public function __construct(string $shooter, string $coordShot, string $result = 'RATE')
  {
    $this->shooter = $shooter;
    $this->coordShot = $coordShot;
    $this->result = $result;
    $this->timestamp = date('Y-m-d H:i:s');
  }

  // Getters
  public function getShooter(): string
  {
    return $this->shooter;
  }

  public function getCoordShot(): string
  {
    return $this->coordShot;
  }

  public function getResult(): string
  {
    return $this->result;
  }

  public function getTimestamp(): string
  {
    return $this->timestamp;
  }

  // Setters
  public function setResult(string $result): void
  {
    $this->result = $result;
  }

  /**
   * Vérifie si le tir a touché un bateau
   */
  public function isHit(): bool
  {
    if ($this->result == 'TOUCHE' || $this->result == 'COULE') {
      return true;
    }
    return false;
  }

  /**
   * Vérifie si ce tir est aux mêmes coordonnées qu'un autre
   * @param string $coord Coordonnée à comparer
   */
  public function isSameCoord(string $coord): bool
  {
    if ($this->coordShot == $coord) {
      return true;
    }
    return false;
  }
}
