<?php

declare(strict_types=1);

namespace App\class\Interfaces;

/**
 * BoardInterface - Contrat pour la grille de jeu
 *
 * Définit les méthodes essentielles pour gérer une grille de bataille navale.
 * Permet de découpler la logique de jeu de l'implémentation de la grille.
 *
 * @package App\class\Interfaces
 */
interface BoardInterface
{
    /**
     * Retourne la grille complète
     *
     * @return array La grille 2D (format: ['A'][1] => valeur)
     */
    public function getGrid(): array;

    /**
     * Retourne les identifiants de lignes (A-J)
     *
     * @return array Liste des lettres de lignes
     */
    public function getRow(): array;

    /**
     * Retourne les identifiants de colonnes (1-10)
     *
     * @return array Liste des numéros de colonnes
     */
    public function getColumn(): array;

    /**
     * Retourne la taille de la grille
     *
     * @return int Dimension de la grille (10 par défaut)
     */
    public function getSize(): int;

    /**
     * Valide qu'une coordonnée existe dans la grille
     *
     * @param array $coord Coordonnée au format ['row' => 'A', 'col' => 1] ou [0 => 'A', 1 => 1]
     * @return bool true si la coordonnée est valide
     */
    public function validateCoordinates(array $coord): bool;
}
