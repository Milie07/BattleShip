# BATTLESHIP

Projet d'étude reprenant le jeu classique de la Bataille Navale. Placez vos bateaux et affrontez l'ordinateur !

---

## Technologies utilisées

- PHP 8 (POO, interfaces, services)
- HTML5
- CSS3 (responsive design)
- JavaScript ES6+ (modules, drag & drop)
- Docker / Docker Compose
- PHPUnit 10 (tests unitaires)
- Composer (autoloading PSR-4)

## Fonctionnalités

- Saisie du nom du joueur
- Grille de jeu 10x10 interactive
- Placement des bateaux via drag & drop
- Modale de positionnement des bateaux (horizontal/vertical)
- Affichage des bateaux restants (joueur et ordinateur)
- Design responsive (mobile et desktop)

## Installation

1. Cloner le dépôt :

```bash
git clone <url-du-repo>
cd BattleShip
```

2. Installer les dépendances (optionnel, pour les tests) :

```bash
composer install
```

3. Lancer le projet avec Docker :

```bash
docker compose up -d
```

4. Ouvrir [http://localhost:8080](http://localhost:8080) dans un navigateur

### Commandes utiles

```bash
# Lancer les tests
composer test
# ou
./vendor/bin/phpunit --testdox

# Voir les logs Docker
docker compose logs -f

# Arrêter le projet
docker compose down
```

## Architecture

### Structure du projet

```
BattleShip/
├── index.php                 # Point d'entrée principal
├── composer.json             # Dépendances et autoloading PSR-4
├── phpunit.xml               # Configuration des tests
├── Dockerfile
├── docker-compose.yml
│
├── api/                      # Endpoints AJAX
│   ├── fire-shot.php         # Traitement des tirs
│   └── save-ships.php        # Sauvegarde du placement
│
├── class/                    # Classes PHP (namespace App\class)
│   ├── Game.php              # Orchestrateur de la partie
│   ├── Player.php            # Joueur humain
│   ├── AiPlayer.php          # Joueur IA (hérite de Player)
│   ├── BoardGame.php         # Grille de jeu 10x10
│   ├── Ships.php             # Modèle de bateau
│   ├── Shots.php             # Enregistrement des tirs
│   │
│   ├── Interfaces/           # Contrats pour testabilité
│   │   ├── PlayerInterface.php
│   │   └── BoardInterface.php
│   │
│   └── Services/             # Couche service
│       └── GameService.php   # Logique métier extraite
│
├── tests/                    # Tests unitaires PHPUnit
│   └── Unit/
│       ├── ShipsTest.php
│       ├── PlayerTest.php
│       └── GameTest.php
│
├── assets/
│   ├── css/
│   └── js/
│
└── docs/
    └── architecture/
        └── class-diagram.md  # Diagramme de classes UML
```

### Patterns utilisés

| Pattern | Utilisation |
|---------|-------------|
| **Interface** | `PlayerInterface`, `BoardInterface` - Permettent le mocking et l'injection de dépendances |
| **Service Layer** | `GameService` - Sépare la logique métier des endpoints HTTP |
| **Héritage** | `AiPlayer extends Player` - Réutilisation du code joueur |
| **Machine à états** | `Game` gère les états PLACEMENT → EN_COURS → TERMINE |

### Flux de données

```
[Frontend JS]
     ↓ POST /class/fire-shot.php
[Endpoint AJAX]
     ↓
[GameService] ← Logique métier
     ↓
[Game] → [Player/AiPlayer] → [Ships/Shots]
     ↓
[Session PHP] ← Persistence
```

## Tests

Le projet inclut 44 tests unitaires couvrant :

- **ShipsTest** : Touches, coulés, coordonnées
- **PlayerTest** : Placement, validation, réception des tirs
- **GameTest** : États, tours, conditions de victoire

```bash
# Lancer les tests avec output détaillé
./vendor/bin/phpunit --testdox

# Résultat attendu
OK (44 tests, 87 assertions)
```

## Améliorations à prévoir

- [x] Implémentation de l'IA (AiPlayer)
- [x] Gestion des tirs et détection des touches
- [x] Logique de jeu complète (Game)
- [x] Tests unitaires PHPUnit
- [x] Architecture services
- [ ] Affichage de la Mini-grille à déplacer 
- [ ] Gestion visuelle des scores
- [ ] Gestion du timer
- [ ] Sauvegarde persistante (base de données)
- [ ] Mode multijoueur
- [ ] Animations et effets sonores
