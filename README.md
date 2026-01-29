# BATTLESHIP

Projet d'étude reprenant le jeu classique de la Bataille Navale pour pratiquer essentiellement PHP et mettre en place des composants métiers. 

---

## Technologies utilisées

- PHP 8.4 (POO, interfaces, services)
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
- Affichage des croix pour les tirs réussis et ratés
- Affichage des tours avec commentaires
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

### Notes
- Les modifications des fichiers sont visibles immédiatement (pas besoin de rebuild)
- Le projet utilise le port 8080 par défaut
- Pour changer le port, modifier `ports: - "8080:80"` dans docker-compose.yml
- Fonctionne sur Windows, macOS et Linux sans modification

## Architecture du projet
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
│   │   ├──styles_desktop.css
│   │   ├──styles_mobile.css
│   │   ├──styles_tablet.css
│   │   ├──variables.css
│   │   ├──styles.css
│   ├── js/
│   │    ├── models/ 
│   │    │  ├──styles_desktop.css
│   │    │  ├──styles_mobile.css
│   │    │  ├──styles_tablet.css
│   │    │  ├──variables.css
│   │    └──script.js
│   │   
│   └── img/
│        └──fond.jpg


## Déploiement

### Conteneurisation avec Docker
- Utilisation de l'image officielle PHP avec Serveur Apache intégré
- Copie du Code source dans le container
- Modification du port Apache pour Fly.io
- Optimisation avec .dockerignore ou j'ai exclu les fichiers inutiles pour réduire la taille de l'image

### Développement local
```bash
# Démarrer le conteneur Docker
docker-compose up -d

# Eteindre le conteneur Docker
docker-compose down

# Vérifier que tout fonctionne
```

### Tests

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
### Versionning sur Git
S'assurer que la branche est à jour :
```
git status
```
Et si ce n'est pas le cas, les modifications sont à commiter.
```
git add <fichier à commiter>
git commit -m "message"
git push origin main
```

### Configuration de Fly.io
1. Création du fichier fly.toml pour la configuration du déploiement
2. Authentification en ligne de commande à l'application fly.io
```
fly auth login
```
3. Création et déploiement de l'application sur fly.io
```
fly apps create battleship-milie # battleship était déjà pris
fly deploy
```
4. Vérification 
```
fly status
fly logs
```

## Améliorations à prévoir

- [x] Implémentation de l'IA (AiPlayer)
- [x] Gestion des tirs et détection des touches
- [x] Logique de jeu complète (Game)
- [x] Tests unitaires PHPUnit
- [x] Architecture services
- [ ] Affichage global à améliorer
- [ ] Gestion visuelle des scores
- [ ] Gestion du timer
- [ ] Mode multijoueur
- [ ] Animations et effets sonores
