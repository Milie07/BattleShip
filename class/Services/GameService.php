<?php

declare(strict_types=1);

namespace App\class\Services;

use App\class\Game;
use App\class\Player;
use App\class\Ships;

/**
 * GameService - Service métier pour la gestion des parties
 *
 * Encapsule la logique métier du jeu de bataille navale.
 * Sépare les règles du jeu de la gestion HTTP/session.
 *
 * @package App\class\Services
 */
class GameService
{
    /**
     * Pattern de validation des coordonnées (A1-J10)
     */
    private const COORDINATE_PATTERN = '/^[A-J](10|[1-9])$/';

    /**
     * Valide le format d'une coordonnée
     *
     * @param string $coordinate La coordonnée à valider (ex: "A1", "J10")
     * @return bool true si le format est valide
     */
    public function validateCoordinate(string $coordinate): bool
    {
        $normalized = strtoupper(trim($coordinate));
        return (bool) preg_match(self::COORDINATE_PATTERN, $normalized);
    }

    /**
     * Normalise une coordonnée (majuscules, trim)
     *
     * @param string $coordinate La coordonnée brute
     * @return string La coordonnée normalisée
     */
    public function normalizeCoordinate(string $coordinate): string
    {
        return strtoupper(trim($coordinate));
    }

    /**
     * Traite un tir du joueur et éventuellement la réponse de l'IA
     *
     * @param Game $game L'instance de jeu en cours
     * @param string $coordinate La coordonnée visée par le joueur
     * @return array Résultat structuré avec playerShot, aiShot, gameOver, winner
     * @throws \InvalidArgumentException Si la coordonnée est invalide ou déjà tirée
     */
    public function processPlayerShot(Game $game, string $coordinate): array
    {
        $coordinate = $this->normalizeCoordinate($coordinate);

        // Validation de la coordonnée
        if (!$this->validateCoordinate($coordinate)) {
            throw new \InvalidArgumentException('Coordonnée invalide');
        }

        $player = $game->getPlayer();
        $ai = $game->getAi();

        // Vérifier si déjà tiré
        if ($player->hasAlreadyFiredAt($coordinate)) {
            throw new \InvalidArgumentException('Vous avez déjà tiré sur cette case');
        }

        // Exécuter le tir du joueur
        $playerResult = $game->playerTurn($coordinate);

        $response = [
            'success' => true,
            'playerShot' => [
                'coordinate' => $coordinate,
                'result' => $playerResult['result'],
                'shipType' => $playerResult['shipType'],
                'shipCoordinates' => $playerResult['shipCoordinates']
            ],
            'aiShot' => null,
            'gameOver' => false,
            'winner' => null
        ];

        // Vérifier victoire du joueur
        if ($ai->hasLost()) {
            $response['gameOver'] = true;
            $response['winner'] = 'player';
            return $response;
        }

        // Tour de l'IA si le joueur a raté
        // L'IA joue en boucle tant qu'elle touche (comme le joueur)
        if ($game->getCurrentTurn() === 'AI') {
            $aiShots = [];

            do {
                $aiResult = $game->aiTurn();

                $aiShots[] = [
                    'coordinate' => $aiResult['coord'],
                    'result' => $aiResult['result'],
                    'shipType' => $aiResult['shipType'],
                    'shipCoordinates' => $aiResult['shipCoordinates']
                ];

                // Vérifier victoire de l'IA après chaque tir
                if ($player->hasLost()) {
                    $response['gameOver'] = true;
                    $response['winner'] = 'ai';
                    break;
                }

                // Continuer tant que l'IA touche et que c'est son tour
            } while ($aiResult['result'] !== 'RATE' && $game->getCurrentTurn() === 'AI');

            // Retourne tous les tirs de l'IA
            $response['aiShots'] = $aiShots;
            // Garde la compatibilité avec aiShot (premier tir uniquement)
            $response['aiShot'] = $aiShots[0] ?? null;
        }

        return $response;
    }

    /**
     * Initialise une nouvelle partie avec les bateaux du joueur
     *
     * @param string $playerName Nom du joueur
     * @param array $shipsData Données des bateaux depuis le frontend
     *        Format: [['type' => 'destroyer', 'size' => 3, 'coordinates' => ['A1', 'A2', 'A3'], 'orientation' => true], ...]
     * @return Game L'instance de jeu initialisée
     * @throws \InvalidArgumentException Si les données sont invalides
     */
    public function initializeGame(string $playerName, array $shipsData): Game
    {
        // Valider le nom du joueur
        $validation = Player::validatePlayerName($playerName);
        if (!$validation['valid']) {
            throw new \InvalidArgumentException($validation['message']);
        }

        // Créer la partie
        $game = new Game($validation['sanitized']);
        $player = $game->getPlayer();

        // Placer les bateaux du joueur
        foreach ($shipsData as $shipData) {
            $success = $player->placeShip(
                $shipData['type'],
                $shipData['size'],
                $shipData['coordinates'],
                $shipData['orientation'] ?? true
            );

            if (!$success) {
                throw new \InvalidArgumentException(
                    "Impossible de placer le bateau {$shipData['type']}"
                );
            }
        }

        // Vérifier que tous les bateaux sont placés
        if (!$player->allShipsPlaced()) {
            throw new \InvalidArgumentException('Tous les bateaux doivent être placés');
        }

        // Démarrer la partie (place les bateaux de l'IA)
        $game->startGame();

        return $game;
    }

    /**
     * Récupère l'état actuel du jeu pour l'affichage
     *
     * @param Game $game L'instance de jeu
     * @return array État du jeu formaté pour le frontend
     */
    public function getGameState(Game $game): array
    {
        $player = $game->getPlayer();
        $ai = $game->getAi();

        return [
            'status' => $game->getStatus(),
            'currentTurn' => $game->getCurrentTurn(),
            'turnCount' => $game->getTurnCount(),
            'player' => [
                'name' => $player->getPlayerName(),
                'shipsStatus' => $player->getShipsStatus(),
                'shotsReceived' => $player->getShotsHistoryForDisplay(),
                'sunkShips' => $player->countSunkShips()
            ],
            'ai' => [
                'shipsStatus' => $ai->getShipsStatus(),
                'shotsReceived' => $ai->getShotsHistoryForDisplay(),
                'sunkShips' => $ai->countSunkShips()
            ],
            'winner' => $game->getWinner()
        ];
    }

    /**
     * Vérifie si une partie est terminée
     *
     * @param Game $game L'instance de jeu
     * @return bool true si la partie est terminée
     */
    public function isGameOver(Game $game): bool
    {
        return $game->getStatus() === Game::STATUS_TERMINE;
    }
}
