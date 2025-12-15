<?php

class BoardGame
{
  private array $grid;
  private array $column;
  private array $line;
  private int $size;

  public function __construct(int $size = 10)
  {
    $this->size = $size;
    $this->column = range('A', chr(ord('A') + $size - 1));
    $this->line = range(1, $size);
    $this->grid = [];

    foreach ($this->column as $columnLetter) {
      $this->grid[$columnLetter] = [];
      foreach ($this->line as $lineNumber) {
        $this->grid[$columnLetter][$lineNumber] = null;     
      }
    }
  }

  public function getGrid(): array
  {
    return $this->grid;
  }

  public function displayGrid(): void
  {
    
  }
}