<?php
namespace App\class;

class Player {
  public string $playerName;
  // public array $playerGrid;
  // public array $playerShips;
  
  public function __construct(string $playerName)
  {
    $this->playerName = $playerName;
    // $this->playerGrid = $playerGrid;
    // $this->playerShips = $playerShips;
  }

  public function getPlayerName(): string
  {
    return $this->playerName;
  }
  public function setPlayerName(string $playerName):void
  {
    $this->playerName = $playerName;
  }
  // public function getPlayerGrid(): array
  // {
  //   return $this->playerGrid;
  // }
  // public function setPlayerGrid(array $playerGrid): void
  // {
  //   $this->playerGrid = $playerGrid;
  // }
  // public function getPlayerShips(): array
  // {

  //   return $this->playerShips;
  // }
  // public function setPlayerShips(array $playerShips): void
  // {
  //   $this->playerShips = $playerShips;
  // }





}
