<?php
namespace App\class;

/**
 * Game - Gère le déroulement d'une partie de bataille navale
 * Orchestre les interactions entre le joueur et l'ordinateur
 */
class Game
{
  private Player $player;
  private AiPlayer $ai;
  private string $currentTurn;    // 'PLAYER' ou 'AI'
  private string $status;         // 'PLACEMENT', 'EN_COURS', 'TERMINE'
  private int $turnCount;         // Compteur de tours
  private string $winner;         // Nom du gagnant
  private string $lastActivity;   // Timestamp dernière action

  // Constantes pour les états
  const STATUS_PLACEMENT = 'PLACEMENT';
  const STATUS_EN_COURS = 'EN_COURS';
  const STATUS_TERMINE = 'TERMINE';
  const MAX_TURNS = 100;

  public function __construct(string $playerName)
  {
    $this->player = new Player($playerName);
    $this->ai = new AiPlayer();
    $this->currentTurn = 'PLAYER';
    $this->status = self::STATUS_PLACEMENT;
    $this->turnCount = 0;
    $this->winner = '';
    $this->lastActivity = date('Y-m-d H:i:s');
  }

  // Getters
  public function getPlayer(): Player
  {
    return $this->player;
  }

  public function getAi(): AiPlayer
  {
    return $this->ai;
  }

  public function getCurrentTurn(): string
  {
    return $this->currentTurn;
  }

  public function getStatus(): string
  {
    return $this->status;
  }

  public function getTurnCount(): int
  {
    return $this->turnCount;
  }

  public function getWinner(): string
  {
    return $this->winner;
  }

  /**
   * Démarre la partie après le placement des bateaux
   * L'IA place ses bateaux automatiquement
   */
  public function startGame(): bool
  {
    // Vérifier que le joueur a placé tous ses bateaux
    if (!$this->player->allShipsPlaced()) {
      return false;
    }

    // L'ordinateur place ses bateaux
    $this->ai->placeShipsRandomly();

    // Changer le statut de la partie
    $this->status = self::STATUS_EN_COURS;
    $this->currentTurn = 'PLAYER';
    $this->updateLastActivity();

    return true;
  }

  /**
   * Traite un tour de jeu (tir du joueur)
   * @param string $coord Coordonnée visée par le joueur
   * @return array Résultat du tour avec shipType et shipCoordinates
   */
  public function playerTurn(string $coord): array
  {
    $result = [
      'success' => false,
      'result' => '',
      'shipType' => null,
      'shipCoordinates' => [],
      'message' => ''
    ];

    // Vérifier que c'est bien le tour du joueur
    if ($this->currentTurn != 'PLAYER') {
      $result['message'] = "Ce n'est pas votre tour";
      return $result;
    }

    // Vérifier que la partie est en cours
    if ($this->status != self::STATUS_EN_COURS) {
      $result['message'] = "La partie n'est pas en cours";
      return $result;
    }

    // Vérifier que le joueur n'a pas déjà tiré ici
    if ($this->player->hasAlreadyFiredAt($coord)) {
      $result['message'] = "Vous avez déjà tiré à cette coordonnée";
      return $result;
    }

    // Effectuer le tir sur la grille de l'IA
    $shotData = $this->ai->receiveShot($coord, $this->player->getPlayerName());
    $shotResult = $shotData['result'];
    $this->player->addShotFired($coord, $shotResult);
    $this->turnCount = $this->turnCount + 1;

    $result['success'] = true;
    $result['result'] = $shotResult;
    $result['shipType'] = $shotData['shipType'];
    $result['shipCoordinates'] = $shotData['shipCoordinates'];

    // Message selon le résultat
    if ($shotResult == 'RATE') {
      $result['message'] = 'Raté !';
      // Passer au tour de l'IA
      $this->currentTurn = 'AI';
    } elseif ($shotResult == 'TOUCHE') {
      $result['message'] = 'Touché !';
      // Le joueur rejoue
    } elseif ($shotResult == 'COULE') {
      $result['message'] = 'Coulé !';
      // Le joueur rejoue
    }

    // Vérifier la victoire
    if ($this->checkVictory()) {
      $result['message'] = $this->winner . ' gagne la partie !';
    }

    $this->updateLastActivity();
    return $result;
  }

  /**
   * Traite le tour de l'ordinateur
   * @return array Résultat du tour de l'IA avec shipType et shipCoordinates
   */
  public function aiTurn(): array
  {
    $result = [
      'success' => false,
      'coord' => '',
      'result' => '',
      'shipType' => null,
      'shipCoordinates' => [],
      'message' => ''
    ];

    // Vérifier que c'est le tour de l'IA
    if ($this->currentTurn != 'AI') {
      $result['message'] = "Ce n'est pas le tour de l'ordinateur";
      return $result;
    }

    // L'IA choisit une cible
    $coord = $this->ai->chooseTarget();
    $result['coord'] = $coord;

    // Effectuer le tir sur la grille du joueur
    $shotData = $this->player->receiveShot($coord, 'Ordinateur');
    $shotResult = $shotData['result'];
    $this->ai->addShotFired($coord, $shotResult);

    $result['success'] = true;
    $result['result'] = $shotResult;
    $result['shipType'] = $shotData['shipType'];
    $result['shipCoordinates'] = $shotData['shipCoordinates'];

    // Message selon le résultat
    if ($shotResult == 'RATE') {
      $result['message'] = "L'ordinateur tire en $coord... Raté !";
      // Passer au tour du joueur
      $this->currentTurn = 'PLAYER';
    } elseif ($shotResult == 'TOUCHE') {
      $result['message'] = "L'ordinateur tire en $coord... Touché !";
      // L'IA rejoue (sera géré par la boucle dans le contrôleur)
    } elseif ($shotResult == 'COULE') {
      $result['message'] = "L'ordinateur tire en $coord... Coulé !";
      // L'IA rejoue
    }

    // Vérifier la victoire
    if ($this->checkVictory()) {
      $result['message'] = $this->winner . ' gagne la partie !';
    }

    $this->updateLastActivity();
    return $result;
  }

  /**
   * Vérifie si la partie est terminée
   */
  public function checkVictory(): bool
  {
    // Vérifier si l'IA a perdu
    if ($this->ai->hasLost()) {
      $this->winner = $this->player->getPlayerName();
      $this->status = self::STATUS_TERMINE;
      return true;
    }

    // Vérifier si le joueur a perdu
    if ($this->player->hasLost()) {
      $this->winner = 'Ordinateur';
      $this->status = self::STATUS_TERMINE;
      return true;
    }

    // Vérifier la limite de tours
    if ($this->turnCount >= self::MAX_TURNS) {
      // Celui qui a coulé le plus de bateaux gagne
      $playerSunk = $this->ai->countSunkShips();    // Bateaux IA coulés par joueur
      $aiSunk = $this->player->countSunkShips();    // Bateaux joueur coulés par IA

      if ($playerSunk > $aiSunk) {
        $this->winner = $this->player->getPlayerName();
      } else {
        $this->winner = 'Ordinateur';
      }
      $this->status = self::STATUS_TERMINE;
      return true;
    }

    return false;
  }

  /**
   * Met à jour le timestamp de dernière activité
   */
  private function updateLastActivity(): void
  {
    $this->lastActivity = date('Y-m-d H:i:s');
  }

  /**
   * Vérifie si la partie est abandonnée (plus d'1 heure d'inactivité)
   */
  public function isAbandoned(): bool
  {
    $lastTime = strtotime($this->lastActivity);
    $now = time();
    $diff = $now - $lastTime;

    // 3600 secondes = 1 heure
    if ($diff > 3600) {
      return true;
    }
    return false;
  }

  /**
   * Convertit l'objet Game en tableau pour la sauvegarde en session
   */
  public function toArray(): array
  {
    return [
      'playerName' => $this->player->getPlayerName(),
      'playerShips' => $this->player->getShipsStatus(),
      'playerShotsReceived' => $this->player->getShotsHistoryForDisplay(),
      'aiShips' => $this->ai->getShipsStatus(),
      'aiShotsReceived' => $this->ai->getShotsHistoryForDisplay(),
      'currentTurn' => $this->currentTurn,
      'status' => $this->status,
      'turnCount' => $this->turnCount,
      'winner' => $this->winner,
      'lastActivity' => $this->lastActivity
    ];
  }
}
