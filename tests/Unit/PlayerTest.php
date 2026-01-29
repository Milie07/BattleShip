<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\class\Player;
use App\class\Ships;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour la classe Player
 * Teste le placement des bateaux, la réception des tirs et la gestion de l'état
 */
class PlayerTest extends TestCase
{
    private Player $player;

    protected function setUp(): void
    {
        $this->player = new Player('TestPlayer');
    }

    /**
     * Test: Le joueur est créé avec le bon nom
     */
    public function testPlayerHasCorrectName(): void
    {
        $this->assertEquals('TestPlayer', $this->player->getPlayerName());
    }

    /**
     * Test: Le joueur commence sans bateaux
     */
    public function testPlayerStartsWithNoShips(): void
    {
        $this->assertCount(0, $this->player->getShips());
        $this->assertFalse($this->player->allShipsPlaced());
    }

    /**
     * Test: Validation du nom - nom valide
     */
    public function testValidatePlayerNameWithValidName(): void
    {
        $result = Player::validatePlayerName('Jean');

        $this->assertTrue($result['valid']);
        $this->assertEquals('Jean', $result['sanitized']);
    }

    /**
     * Test: Validation du nom - nom trop court
     */
    public function testValidatePlayerNameTooShort(): void
    {
        $result = Player::validatePlayerName('A');

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('2', $result['message']);
    }

    /**
     * Test: Validation du nom - nom vide
     */
    public function testValidatePlayerNameEmpty(): void
    {
        $result = Player::validatePlayerName('');

        $this->assertFalse($result['valid']);
    }

    /**
     * Test: Validation du nom - trim les espaces
     */
    public function testValidatePlayerNameTrimsWhitespace(): void
    {
        $result = Player::validatePlayerName('  Marie  ');

        $this->assertTrue($result['valid']);
        $this->assertEquals('Marie', $result['sanitized']);
    }

    /**
     * Test: Placement d'un bateau valide
     */
    public function testPlaceShipSuccess(): void
    {
        $result = $this->player->placeShip('destroyer', 3, ['A1', 'A2', 'A3'], true);

        $this->assertTrue($result);
        $this->assertCount(1, $this->player->getShips());
    }

    /**
     * Test: Placement hors grille échoue
     */
    public function testPlaceShipOutOfBoundsFails(): void
    {
        $result = $this->player->placeShip('destroyer', 3, ['A11', 'A12', 'A13'], true);

        $this->assertFalse($result);
        $this->assertCount(0, $this->player->getShips());
    }

    /**
     * Test: Chevauchement de bateaux échoue
     */
    public function testPlaceShipOverlapFails(): void
    {
        // Placer un premier bateau
        $this->player->placeShip('destroyer1', 3, ['A1', 'A2', 'A3'], true);

        // Essayer de placer un second bateau qui chevauche
        $result = $this->player->placeShip('destroyer2', 3, ['A2', 'A3', 'A4'], true);

        $this->assertFalse($result);
        $this->assertCount(1, $this->player->getShips());
    }

    /**
     * Test: allShipsPlaced() retourne true avec 5 bateaux
     */
    public function testAllShipsPlacedWithFiveShips(): void
    {
        // Placer 5 bateaux sans chevauchement
        $this->player->placeShip('aircraft-carrier', 5, ['A1', 'A2', 'A3', 'A4', 'A5'], true);
        $this->player->placeShip('cruiser', 4, ['B1', 'B2', 'B3', 'B4'], true);
        $this->player->placeShip('destroyer1', 3, ['C1', 'C2', 'C3'], true);
        $this->player->placeShip('destroyer2', 3, ['D1', 'D2', 'D3'], true);
        $this->player->placeShip('torpedo-boat', 2, ['E1', 'E2'], true);

        $this->assertTrue($this->player->allShipsPlaced());
        $this->assertCount(5, $this->player->getShips());
    }

    /**
     * Test: receiveShot() retourne RATE si aucun bateau n'est touché
     */
    public function testReceiveShotMiss(): void
    {
        $this->player->placeShip('destroyer', 3, ['A1', 'A2', 'A3'], true);

        $result = $this->player->receiveShot('B1', 'Adversaire');

        $this->assertEquals('RATE', $result);
    }

    /**
     * Test: receiveShot() retourne TOUCHE si un bateau est touché
     */
    public function testReceiveShotHit(): void
    {
        $this->player->placeShip('destroyer', 3, ['A1', 'A2', 'A3'], true);

        $result = $this->player->receiveShot('A1', 'Adversaire');

        $this->assertEquals('TOUCHE', $result);
    }

    /**
     * Test: receiveShot() retourne COULE si le dernier segment est touché
     */
    public function testReceiveShotSinks(): void
    {
        $this->player->placeShip('torpedo-boat', 2, ['A1', 'A2'], true);

        $this->player->receiveShot('A1', 'Adversaire');
        $result = $this->player->receiveShot('A2', 'Adversaire');

        $this->assertEquals('COULE', $result);
    }

    /**
     * Test: receiveShot() retourne DEJA_TIRE pour une coordonnée déjà visée
     */
    public function testReceiveShotAlreadyFired(): void
    {
        $this->player->placeShip('destroyer', 3, ['A1', 'A2', 'A3'], true);

        $this->player->receiveShot('A1', 'Adversaire');
        $result = $this->player->receiveShot('A1', 'Adversaire');

        $this->assertEquals('DEJA_TIRE', $result);
    }

    /**
     * Test: hasAlreadyFiredAt() détecte les tirs précédents
     */
    public function testHasAlreadyFiredAt(): void
    {
        $this->player->addShotFired('B5', 'RATE');

        $this->assertTrue($this->player->hasAlreadyFiredAt('B5'));
        $this->assertFalse($this->player->hasAlreadyFiredAt('C5'));
    }

    /**
     * Test: hasLost() retourne false avec des bateaux non coulés
     */
    public function testHasLostWithShipsRemaining(): void
    {
        $this->player->placeShip('destroyer', 3, ['A1', 'A2', 'A3'], true);

        $this->assertFalse($this->player->hasLost());
    }

    /**
     * Test: hasLost() retourne true quand tous les bateaux sont coulés
     */
    public function testHasLostWhenAllShipsSunk(): void
    {
        $this->player->placeShip('torpedo-boat', 2, ['A1', 'A2'], true);

        $this->player->receiveShot('A1', 'Adversaire');
        $this->player->receiveShot('A2', 'Adversaire');

        $this->assertTrue($this->player->hasLost());
    }

    /**
     * Test: countSunkShips() compte correctement les bateaux coulés
     */
    public function testCountSunkShips(): void
    {
        $this->player->placeShip('torpedo-boat', 2, ['A1', 'A2'], true);
        $this->player->placeShip('destroyer', 3, ['B1', 'B2', 'B3'], true);

        // Couler le torpedo-boat
        $this->player->receiveShot('A1', 'Adversaire');
        $this->player->receiveShot('A2', 'Adversaire');

        $this->assertEquals(1, $this->player->countSunkShips());

        // Toucher le destroyer sans le couler
        $this->player->receiveShot('B1', 'Adversaire');

        $this->assertEquals(1, $this->player->countSunkShips());
    }
}
