<?php
namespace App\class;

class Player {
  public string $playerName;

  public function __construct(string $playerName)
  {
    $this->playerName = $playerName;

  }
  public function getPlayerName(): string
  {
    return $this->playerName;
  }
  public function setPlayerName(string $playerName):void
  {
    $this->playerName = $playerName;
  }
  


}
