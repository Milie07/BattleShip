document.addEventListener("DOMContentLoaded", () => {
  let draggedShip = null;

  // Sélectionne tous les bateaux draggables
  const ships = document.querySelectorAll(".ships-modal .ship");

  ships.forEach((ship) => {
    ship.addEventListener("dragstart", (e) => {
      draggedShip = e.target;
      draggedShip.classList.add("dragging");
    });

    ship.addEventListener("dragend", () => {
      draggedShip.classList.remove("dragging");
    });
  });

  // Sélectionne toutes les cellules de la grille modale uniquement
  const cells = document.querySelectorAll("#modalGrid .valid-drop");

  cells.forEach((cell) => {
    cell.addEventListener("dragover", (e) => {
      e.preventDefault();
    });

    cell.addEventListener("drop", (e) => {
      e.preventDefault();
      e.stopPropagation();

      // Vérifie si le bateau est déjà placé
      if (!draggedShip || draggedShip.classList.contains("placed")) {
        return;
      }

      // Convertit en Array pour éviter les problèmes de NodeList live
      const shipCells = Array.from(draggedShip.querySelectorAll(".ship-cell"));
      const startCoord = cell.dataset.coord;
      const orientation = draggedShip.dataset.orientation;
      const size = parseInt(draggedShip.dataset.size);
      const shipType = draggedShip.dataset.name;

      console.log("Drop sur:", startCoord);
      console.log(
        "Bateau:",
        shipType,
        "Taille:",
        size,
        "Orientation:",
        orientation,
      );
      console.log("Ship cells trouvées:", shipCells.length);

      // Récupère les td cibles
      const targetTds = getTargetCells(startCoord, size, orientation);

      console.log("Target TDs:", targetTds);

      if (targetTds && canPlace(targetTds)) {
        console.log("Placement valide, on place les cellules");

        // Place chaque ship-cell dans le td correspondant
        shipCells.forEach((shipCell, index) => {
          shipCell.classList.add(shipType);

          // Ajoute les classes pour le border-radius
          if (index === 0) {
            shipCell.classList.add("first-cell");
          }
          if (index === shipCells.length - 1) {
            shipCell.classList.add("last-cell");
          }

          // Ajoute l'orientation pour le CSS
          shipCell.dataset.orientation = orientation;

          targetTds[index].appendChild(shipCell);
          targetTds[index].classList.add("occupied");
        });

        // Marque le bateau comme placé (opacité réduite)
        draggedShip.classList.add("placed");
        draggedShip.setAttribute("draggable", "false");
      } else {
        console.log("Placement invalide");
      }
    });
  });

  // Récupère les cellules cibles selon la position de départ, taille et orientation
  function getTargetCells(startCoord, size, orientation) {
    const row = startCoord[0];
    const col = parseInt(startCoord.slice(1));
    const cells = [];

    for (let i = 0; i < size; i++) {
      let targetRow = row;
      let targetCol = col;

      if (orientation === "horizontal") {
        targetCol = col + i;
        if (targetCol > 10) return null;
      } else {
        targetRow = String.fromCharCode(row.charCodeAt(0) + i);
        if (targetRow > "J") return null;
      }

      const targetCell = document.querySelector(
        `#modalGrid [data-coord="${targetRow}${targetCol}"]`,
      );

      if (!targetCell) return null;
      cells.push(targetCell);
    }

    return cells;
  }

  // Vérifie si toutes les cellules sont libres
  function canPlace(cells) {
    return cells.every((cell) => !cell.classList.contains("occupied"));
  }

  // Récupère le type de bateau d'une cellule
  function getShipType(cell) {
    const types = [
      "aircraft-carrier",
      "cruiser",
      "destroyer1",
      "destroyer2",
      "torpedo-boat",
    ];
    return types.find((type) => cell.classList.contains(type));
  }

  // Rotation des bateaux placés sur la grille
  let rotateInitialized = false;
  function rotateShip() {
    if (rotateInitialized) return;
    rotateInitialized = true;

    const modalGrid = document.querySelector("#modalGrid");
    if (!modalGrid) {
      console.log("modalGrid non trouvé");
      return;
    }
    console.log("rotateShip initialisé");

    modalGrid.addEventListener("click", (e) => {
      console.log("Click sur grille, target:", e.target);

      // Vérifie si on a cliqué sur un ship-cell ou un td contenant un ship-cell
      let clickedCell = null;

      // Cas 1: clic direct sur un ship-cell
      if (e.target.classList.contains("ship-cell")) {
        clickedCell = e.target;
      }
      // Cas 2: clic sur le td qui contient un ship-cell
      else {
        const clickedTd = e.target.closest("td");
        if (clickedTd) {
          clickedCell = clickedTd.querySelector(".ship-cell");
        }
      }

      console.log("clickedCell:", clickedCell);
      if (!clickedCell) return;

      console.log("Clic sur ship-cell:", clickedCell);

      const shipType = getShipType(clickedCell);
      if (!shipType) return;

      // Récupère toutes les cellules de ce bateau et les trie par leur index
      const shipCells = Array.from(
        document.querySelectorAll(`#modalGrid td .ship-cell.${shipType}`),
      ).sort(
        (a, b) => parseInt(a.dataset.cellIndex) - parseInt(b.dataset.cellIndex),
      );

      console.log(
        "Ship cells triées par index:",
        shipCells.map((c) => c.dataset.cellIndex),
      );

      if (shipCells.length === 0) return;

      // Récupère l'orientation actuelle et la nouvelle
      const currentOrientation = shipCells[0].dataset.orientation;
      const newOrientation =
        currentOrientation === "horizontal" ? "vertical" : "horizontal";

      // Récupère la coordonnée de la première cellule (point de pivot)
      const firstTd = shipCells[0].closest("td");
      const startCoord = firstTd.dataset.coord;
      const size = shipCells.length;
      console.log("firstTd", firstTd);
      // Calcule les nouvelles positions
      const newTargetTds = getTargetCells(startCoord, size, newOrientation);

      if (!newTargetTds) {
        console.log("Rotation impossible : dépasse la grille");
        return;
      }

      // Vérifie si les nouvelles cellules sont libres (en ignorant les cellules du bateau actuel)
      const currentTds = shipCells.map((cell) => cell.closest("td"));
      const canRotate = newTargetTds.every((td) => {
        return !td.classList.contains("occupied") || currentTds.includes(td);
      });

      if (!canRotate) {
        console.log("Rotation impossible : cellules occupées");
        return;
      }

      console.log(
        "Positions actuelles:",
        currentTds.map((td) => td.dataset.coord),
      );
      console.log(
        "Nouvelles positions:",
        newTargetTds.map((td) => td.dataset.coord),
      );

      // Libère les anciennes cellules
      currentTds.forEach((td) => {
        td.classList.remove("occupied");
      });

      // Déplace les ship-cells vers les nouvelles positions
      shipCells.forEach((shipCell, index) => {
        console.log(
          `Déplacement cellule ${index} vers ${newTargetTds[index].dataset.coord}`,
        );

        // Met à jour l'orientation
        shipCell.dataset.orientation = newOrientation;

        // Met à jour les classes first-cell et last-cell
        shipCell.classList.remove("first-cell", "last-cell");
        if (index === 0) {
          shipCell.classList.add("first-cell");
        }
        if (index === shipCells.length - 1) {
          shipCell.classList.add("last-cell");
        }

        // Retire d'abord l'élément de son parent actuel
        shipCell.parentNode.removeChild(shipCell);

        // Puis l'ajoute au nouveau td
        newTargetTds[index].appendChild(shipCell);
        newTargetTds[index].classList.add("occupied");
      });

      console.log(`Bateau ${shipType} pivoté en ${newOrientation}`);
    });
  }

  // Initialise la rotation
  rotateShip();

  // ============================================
  // SAUVEGARDE DES BATEAUX
  // ============================================

  // Récupère les coordonnées de tous les bateaux placés sur la grille
  function getPlacedShipsData() {
    const shipTypes = [
      "aircraft-carrier",
      "cruiser",
      "destroyer1",
      "destroyer2",
      "torpedo-boat",
    ];
    const shipSizes = {
      "aircraft-carrier": 5,
      "cruiser": 4,
      "destroyer1": 3,
      "destroyer2": 3,
      "torpedo-boat": 2,
    };
    const shipsData = [];

    shipTypes.forEach((type) => {
      // Récupère toutes les cellules de ce bateau
      const shipCells = document.querySelectorAll(
        `#modalGrid td .ship-cell.${type}`,
      );

      if (shipCells.length > 0) {
        const coordinates = [];
        let orientation = "horizontal";

        // Récupère les coordonnées de chaque cellule
        shipCells.forEach((cell) => {
          const td = cell.closest("td");
          if (td) {
            coordinates.push(td.dataset.coord);
            orientation = cell.dataset.orientation || "horizontal";
          }
        });

        // Ajoute le bateau aux données
        shipsData.push({
          type: type,
          size: shipSizes[type],
          coordinates: coordinates,
          orientation: orientation,
        });
      }
    });

    return shipsData;
  }

  // Vérifie si tous les bateaux sont placés (5 bateaux)
  function allShipsPlaced() {
    const placedShips = document.querySelectorAll(".ships-modal .ship.placed");
    return placedShips.length === 5;
  }

  // Gestion du clic sur le bouton Sauvegarder
  const saveButton = document.querySelector(".save-button button");
  if (saveButton) {
    saveButton.addEventListener("click", (e) => {
      e.preventDefault();

      // Vérifie que tous les bateaux sont placés
      if (!allShipsPlaced()) {
        alert("Tu dois placer tous tes bateaux avant de sauvegarder !");
        return;
      }

      // Récupère les données des bateaux
      const shipsData = getPlacedShipsData();
      console.log("Bateaux à sauvegarder :", shipsData);

      // Envoie les données au serveur PHP
      fetch("./api/save-ships.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ ships: shipsData }),
      })
        .then((response) => response.json())
        .then((data) => {
          console.log("Réponse du serveur :", data);

          if (data.success) {
            // Ferme la modale
            const modal = document.querySelector(".modal");
            if (modal) {
              modal.classList.remove("active");
            }

            // Recharge la page pour afficher la mini-grille
            window.location.reload();
          } else {
            alert("Erreur lors de la sauvegarde : " + data.message);
          }
        })
        .catch((error) => {
          console.error("Erreur :", error);
          alert("Erreur de connexion au serveur");
        });
    });
  }
});
