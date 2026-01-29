<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\class\Game;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour la classe Game
 * Teste la gestion des états, des tours et des conditions de victoire
 */
class GameTest extends TestCase
{
    private Game $game;

    protected function setUp(): void
    {
        $this->game = new Game('TestPlayer');
    }

    /**
     * Test: Le jeu démarre en mode PLACEMENT
     */
    public function testGameStartsInPlacementStatus(): void
    {
        $this->assertEquals(Game::STATUS_PLACEMENT, $this->game->getStatus());
    }

    /**
     * Test: Le premier tour est au joueur
     */
    public function testPlayerGoesFirst(): void
    {
        $this->assertEquals('PLAYER', $this->game->getCurrentTurn());
    }

    /**
     * Test: Le compteur de tours commence à 0
     */
    public function testTurnCountStartsAtZero(): void
    {
        $this->assertEquals(0, $this->game->getTurnCount());
    }

    /**
     * Test: Pas de gagnant au début
     */
    public function testNoWinnerAtStart(): void
    {
        $this->assertEquals('', $this->game->getWinner());
    }

    /**
     * Test: startGame() échoue si les bateaux ne sont pas placés
     */
    public function testStartGameFailsWithoutShips(): void
    {
        $result = $this->game->startGame();

        $this->assertFalse($result);
        $this->assertEquals(Game::STATUS_PLACEMENT, $this->game->getStatus());
    }

    /**
     * Test: startGame() réussit avec tous les bateaux placés
     */
    public function testStartGameSucceedsWithShips(): void
    {
        $this->placeAllPlayerShips();

        $result = $this->game->startGame();

        $this->assertTrue($result);
        $this->assertEquals(Game::STATUS_EN_COURS, $this->game->getStatus());
    }

    /**
     * Test: L'IA place ses bateaux au démarrage
     */
    public function testAiPlacesShipsOnStart(): void
    {
        $this->placeAllPlayerShips();
        $this->game->startGame();

        $ai = $this->game->getAi();
        $this->assertTrue($ai->allShipsPlaced());
        $this->assertCount(5, $ai->getShips());
    }

    /**
     * Test: playerTurn() échoue si ce n'est pas le tour du joueur
     */
    public function testPlayerTurnFailsWhenNotPlayerTurn(): void
    {
        $this->placeAllPlayerShips();
        $this->game->startGame();

        // Simuler un tir raté pour passer le tour à l'IA
        // On doit trouver une case vide de l'IA
        $result = $this->simulatePlayerMiss();

        // Maintenant c'est le tour de l'IA
        $this->assertEquals('AI', $this->game->getCurrentTurn());

        // Le joueur ne devrait pas pouvoir tirer
        $turnResult = $this->game->playerTurn('J10');
        $this->assertFalse($turnResult['success']);
    }

    /**
     * Test: playerTurn() échoue si la partie n'est pas en cours
     */
    public function testPlayerTurnFailsWhenGameNotInProgress(): void
    {
        // Sans démarrer la partie
        $result = $this->game->playerTurn('A1');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('pas en cours', $result['message']);
    }

    /**
     * Test: playerTurn() empêche de tirer deux fois au même endroit
     */
    public function testPlayerCannotFireSameSpotTwice(): void
    {
        $this->placeAllPlayerShips();
        $this->game->startGame();

        // Premier tir - on tire sur une case où on sait qu'il y a un bateau IA
        $ai = $this->game->getAi();
        $ships = $ai->getShips();
        $targetCoord = $ships[0]->getCoordShips()[0];

        $firstResult = $this->game->playerTurn($targetCoord);
        $this->assertTrue($firstResult['success']);

        // Après un TOUCHE, c'est encore le tour du joueur
        $this->assertEquals('PLAYER', $this->game->getCurrentTurn());

        // Deuxième tir au même endroit
        $result = $this->game->playerTurn($targetCoord);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('déjà tiré', $result['message']);
    }

    /**
     * Test: Le tour passe à l'IA après un tir raté
     */
    public function testTurnChangesToAiAfterMiss(): void
    {
        $this->placeAllPlayerShips();
        $this->game->startGame();

        // Trouver une case vide et tirer
        $result = $this->simulatePlayerMiss();

        $this->assertEquals('AI', $this->game->getCurrentTurn());
    }

    /**
     * Test: Le joueur rejoue après un TOUCHE
     */
    public function testPlayerReplaysAfterHit(): void
    {
        $this->placeAllPlayerShips();
        $this->game->startGame();

        // Trouver une case avec un bateau IA et tirer
        $result = $this->simulatePlayerHit();

        if ($result !== null && $result['result'] === 'TOUCHE') {
            $this->assertEquals('PLAYER', $this->game->getCurrentTurn());
        }
    }

    /**
     * Test: aiTurn() fonctionne correctement
     */
    public function testAiTurnWorks(): void
    {
        $this->placeAllPlayerShips();
        $this->game->startGame();

        // Passer le tour à l'IA
        $this->simulatePlayerMiss();

        $result = $this->game->aiTurn();

        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['coord']);
        $this->assertContains($result['result'], ['RATE', 'TOUCHE', 'COULE']);
    }

    /**
     * Test: checkVictory() détecte la victoire du joueur
     */
    public function testCheckVictoryDetectsPlayerWin(): void
    {
        $this->placeAllPlayerShips();
        $this->game->startGame();

        // Couler tous les bateaux de l'IA (simulé)
        $ai = $this->game->getAi();
        $ships = $ai->getShips();

        foreach ($ships as $ship) {
            foreach ($ship->getCoordShips() as $coord) {
                $ai->receiveShot($coord, 'TestPlayer');
            }
        }

        $this->assertTrue($this->game->checkVictory());
        $this->assertEquals('TestPlayer', $this->game->getWinner());
        $this->assertEquals(Game::STATUS_TERMINE, $this->game->getStatus());
    }

    /**
     * Test: checkVictory() détecte la défaite du joueur
     */
    public function testCheckVictoryDetectsPlayerLoss(): void
    {
        $this->placeAllPlayerShips();
        $this->game->startGame();

        // Couler tous les bateaux du joueur (simulé)
        $player = $this->game->getPlayer();
        $ships = $player->getShips();

        foreach ($ships as $ship) {
            foreach ($ship->getCoordShips() as $coord) {
                $player->receiveShot($coord, 'Ordinateur');
            }
        }

        $this->assertTrue($this->game->checkVictory());
        $this->assertEquals('Ordinateur', $this->game->getWinner());
    }

    /**
     * Test: isAbandoned() retourne false pour une partie récente
     */
    public function testIsAbandonedReturnsFalseForRecentGame(): void
    {
        $this->assertFalse($this->game->isAbandoned());
    }

    /**
     * Test: toArray() retourne un tableau avec les bonnes clés
     */
    public function testToArrayReturnsCorrectStructure(): void
    {
        $array = $this->game->toArray();

        $this->assertArrayHasKey('playerName', $array);
        $this->assertArrayHasKey('currentTurn', $array);
        $this->assertArrayHasKey('status', $array);
        $this->assertArrayHasKey('turnCount', $array);
        $this->assertArrayHasKey('winner', $array);
        $this->assertEquals('TestPlayer', $array['playerName']);
    }

    /**
     * Helper: Place tous les bateaux du joueur
     */
    private function placeAllPlayerShips(): void
    {
        $player = $this->game->getPlayer();
        $player->placeShip('aircraft-carrier', 5, ['A1', 'A2', 'A3', 'A4', 'A5'], true);
        $player->placeShip('cruiser', 4, ['B1', 'B2', 'B3', 'B4'], true);
        $player->placeShip('destroyer1', 3, ['C1', 'C2', 'C3'], true);
        $player->placeShip('destroyer2', 3, ['D1', 'D2', 'D3'], true);
        $player->placeShip('torpedo-boat', 2, ['E1', 'E2'], true);
    }

    /**
     * Helper: Simule un tir raté du joueur
     */
    private function simulatePlayerMiss(): ?array
    {
        // Tirer dans une zone peu probable d'avoir un bateau
        $coords = ['J10', 'J9', 'J8', 'I10', 'I9', 'I8'];

        foreach ($coords as $coord) {
            if (!$this->game->getPlayer()->hasAlreadyFiredAt($coord)) {
                $result = $this->game->playerTurn($coord);
                if ($result['success'] && $result['result'] === 'RATE') {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * Helper: Simule un tir réussi du joueur
     */
    private function simulatePlayerHit(): ?array
    {
        $ai = $this->game->getAi();
        $ships = $ai->getShips();

        if (count($ships) > 0) {
            $coords = $ships[0]->getCoordShips();
            foreach ($coords as $coord) {
                if (!$this->game->getPlayer()->hasAlreadyFiredAt($coord)) {
                    return $this->game->playerTurn($coord);
                }
            }
        }

        return null;
    }
}
