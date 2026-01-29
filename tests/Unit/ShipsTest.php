<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\class\Ships;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour la classe Ships
 * Teste la logique de gestion des bateaux (touches, coulés, coordonnées)
 */
class ShipsTest extends TestCase
{
    private Ships $ship;

    protected function setUp(): void
    {
        // Créer un bateau de test avant chaque test
        $this->ship = new Ships(
            'destroyer',
            3,
            ['A1', 'A2', 'A3'],
            true // horizontal
        );
    }

    /**
     * Test: Un bateau nouvellement créé n'est pas coulé
     */
    public function testNewShipIsNotSunk(): void
    {
        $this->assertFalse($this->ship->isSunk());
    }

    /**
     * Test: Un bateau est coulé quand le nombre de touches égale sa taille
     */
    public function testShipIsSunkWhenHitsEqualSize(): void
    {
        // Toucher le bateau 3 fois (sa taille)
        $this->ship->hit();
        $this->ship->hit();
        $result = $this->ship->hit();

        $this->assertTrue($this->ship->isSunk());
        $this->assertEquals('COULE', $result);
    }

    /**
     * Test: hit() retourne TOUCHE tant que le bateau n'est pas coulé
     */
    public function testHitReturnsToucheWhenNotSunk(): void
    {
        $result = $this->ship->hit();

        $this->assertEquals('TOUCHE', $result);
        $this->assertFalse($this->ship->isSunk());
    }

    /**
     * Test: hit() retourne COULE quand le dernier tir coule le bateau
     */
    public function testHitReturnsCouleOnFinalHit(): void
    {
        $this->ship->hit(); // 1ère touche
        $this->ship->hit(); // 2ème touche
        $result = $this->ship->hit(); // 3ème touche = coulé

        $this->assertEquals('COULE', $result);
    }

    /**
     * Test: hasCoordinate() retourne true pour une coordonnée du bateau
     */
    public function testHasCoordinateReturnsTrueForValidCoord(): void
    {
        $this->assertTrue($this->ship->hasCoordinate('A1'));
        $this->assertTrue($this->ship->hasCoordinate('A2'));
        $this->assertTrue($this->ship->hasCoordinate('A3'));
    }

    /**
     * Test: hasCoordinate() retourne false pour une coordonnée hors du bateau
     */
    public function testHasCoordinateReturnsFalseForInvalidCoord(): void
    {
        $this->assertFalse($this->ship->hasCoordinate('B1'));
        $this->assertFalse($this->ship->hasCoordinate('A4'));
        $this->assertFalse($this->ship->hasCoordinate('Z9'));
    }

    /**
     * Test: Les getters retournent les bonnes valeurs
     */
    public function testGettersReturnCorrectValues(): void
    {
        $this->assertEquals('destroyer', $this->ship->getType());
        $this->assertEquals(3, $this->ship->getSize());
        $this->assertEquals(['A1', 'A2', 'A3'], $this->ship->getCoordShips());
        $this->assertTrue($this->ship->getOrientation());
        $this->assertEquals(0, $this->ship->getHits());
    }

    /**
     * Test: Le compteur de touches s'incrémente correctement
     */
    public function testHitsCounterIncrementsCorrectly(): void
    {
        $this->assertEquals(0, $this->ship->getHits());

        $this->ship->hit();
        $this->assertEquals(1, $this->ship->getHits());

        $this->ship->hit();
        $this->assertEquals(2, $this->ship->getHits());
    }

    /**
     * Test: Bateau vertical avec ses coordonnées
     */
    public function testVerticalShipCoordinates(): void
    {
        $verticalShip = new Ships(
            'cruiser',
            4,
            ['A1', 'B1', 'C1', 'D1'],
            false // vertical
        );

        $this->assertFalse($verticalShip->getOrientation());
        $this->assertTrue($verticalShip->hasCoordinate('A1'));
        $this->assertTrue($verticalShip->hasCoordinate('D1'));
        $this->assertFalse($verticalShip->hasCoordinate('A2'));
    }
}
