# BattleShip
Projet Etudiant qui reprend le jeu de bataille navale pour pratiquer le PHP et la POO.

### Prérequis
- Docker
- Docker Compose

### Démarrage rapide en dev

```bash
docker compose up -d
```

Le projet est accessible sur [http://localhost:8080](http://localhost:8080)

### Commandes utiles

```bash
# Démarrer le projet
docker compose up -d

# Voir les logs
docker compose logs -f

# Arrêter le projet
docker compose down

# Rebuild après modification du Dockerfile
docker compose up -d --build
```

### Notes
- Les modifications des fichiers sont visibles immédiatement (pas besoin de rebuild)
- Le projet utilise le port 8080 par défaut
- Pour changer le port, modifier `ports: - "8080:80"` dans docker-compose.yml
- Fonctionne sur Windows, macOS et Linux sans modification
