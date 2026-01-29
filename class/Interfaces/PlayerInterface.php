<?php

declare(strict_types=1);

namespace App\class\Interfaces;

/**
 * PlayerInterface - Contrat pour les joueurs du jeu de bataille navale
 *
 * Définit les méthodes essentielles qu'un joueur (humain ou IA) doit implémenter.
 * Permet l'injection de dépendances et facilite les tests unitaires avec des mocks.
 *
 * @package App\class\Interfaces
 */
interface PlayerInterface
{
    /**
     * Retourne le nom du joueur
     *
     * @return string Le nom du joueur
     */
    public function getPlayerName(): string;

    /**
     * Place un bateau sur la grille du joueur
     *
     * @param string $type Type du bateau (ex: 'destroyer', 'cruiser')
     * @param int $size Taille du bateau (nombre de cases)
     * @param array $coordinates Liste des coordonnées occupées (ex: ['A1', 'A2', 'A3'])
     * @param bool $orientation true = horizontal, false = vertical
     * @return bool true si le placement a réussi, false sinon
     */
    public function placeShip(string $type, int $size, array $coordinates, bool $orientation = true): bool;

    /**
     * Reçoit un tir de l'adversaire
     *
     * @param string $coord Coordonnée du tir (ex: 'A1', 'B5')
     * @param string $shooterName Nom de celui qui tire
     * @return array Résultat avec 'result' (RATE, TOUCHE, COULE), 'shipType' et 'shipCoordinates'
     */
    public function receiveShot(string $coord, string $shooterName): array;

    /**
     * Vérifie si tous les bateaux du joueur sont coulés
     *
     * @return bool true si le joueur a perdu (tous bateaux coulés)
     */
    public function hasLost(): bool;

    /**
     * Vérifie si tous les bateaux sont placés (5 bateaux)
     *
     * @return bool true si les 5 bateaux sont placés
     */
    public function allShipsPlaced(): bool;

    /**
     * Vérifie si le joueur a déjà tiré à cette coordonnée
     *
     * @param string $coord Coordonnée à vérifier
     * @return bool true si déjà tiré à cette position
     */
    public function hasAlreadyFiredAt(string $coord): bool;

    /**
     * Enregistre un tir effectué par ce joueur
     *
     * @param string $coord Coordonnée visée
     * @param string $result Résultat du tir ('RATE', 'TOUCHE', 'COULE')
     */
    public function addShotFired(string $coord, string $result): void;

    /**
     * Compte le nombre de bateaux coulés
     *
     * @return int Nombre de bateaux coulés
     */
    public function countSunkShips(): int;

    /**
     * Retourne l'historique des tirs reçus pour affichage
     *
     * @return array Format: ['A1' => 'TOUCHE', 'B2' => 'RATE', ...]
     */
    public function getShotsHistoryForDisplay(): array;

    /**
     * Retourne l'état de tous les bateaux
     *
     * @return array Liste des états des bateaux
     */
    public function getShipsStatus(): array;
}
