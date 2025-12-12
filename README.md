# BattleShip
 Student project which takes up the game of Battleship

# Conteneurisation
`docker build -t battleship .`
`docker run -d -p 80:80 -v "c:/Users/Nico/projets/BattleShip:/var/www/html" --name battleship-game battleship`