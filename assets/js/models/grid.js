/**
 * Gestion de la grille de jeu et des tirs
 */

document.addEventListener("DOMContentLoaded", () => {
  // Sélectionne uniquement les cellules de la grille principale (grille de l'IA)
  const aiGridCells = document.querySelectorAll("#mainGrid td.cell");
  const statusDisplay =
    document.querySelector(".game-status") || createStatusDisplay();

  let isPlayerTurn = true;

  /**
   * Crée un élément pour afficher le statut du jeu
   */
  function createStatusDisplay() {
    const status = document.createElement("div");
    status.className = "game-status";
    const mainGrid = document.querySelector(".main-grid, .game-board, table");
    if (mainGrid && mainGrid.parentNode) {
      mainGrid.parentNode.insertBefore(status, mainGrid);
    }
    return status;
  }

  /**
   * Met à jour l'affichage du statut
   */
  function updateStatus(message, type = "info") {
    if (statusDisplay) {
      statusDisplay.textContent = message;
      statusDisplay.className = `game-status ${type}`;
    }
  }

  /**
   * Envoie un tir au serveur
   */
  async function fireShot(coordinate, cell) {
    if (!isPlayerTurn) {
      updateStatus("Attendez votre tour...", "warning");
      return;
    }
    // Vérifie si la case a déjà été ciblée
    if (cell.classList.contains("hit") || cell.classList.contains("miss")) {
      updateStatus("Vous avez déjà tiré sur cette case !", "warning");
      return;
    }

    isPlayerTurn = false;
    updateStatus("Tir en cours...", "info");

    try {
      const response = await fetch("api/fire-shot.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ coordinate: coordinate }),
      });

      const data = await response.json();

      if (!data.success) {
        updateStatus(data.error, "error");
        isPlayerTurn = true;
        return;
      }

      // Affiche le résultat du tir du joueur avec la couleur du bateau
      applyShot(cell, data.playerShot.result, data.playerShot.shipType);

      // Si le bateau est coulé, marquer toutes ses cases
      if (data.playerShot.result === "COULE" && data.playerShot.shipCoordinates) {
        markSunkShip(data.playerShot.shipCoordinates, data.playerShot.shipType);
      }

      if (data.playerShot.result === "RATE") {
        updateStatus(`${coordinate} : Raté !`, "miss");
      } else if (data.playerShot.result === "TOUCHE") {
        updateStatus(`${coordinate} : Touché !`, "hit");
      } else if (data.playerShot.result === "COULE") {
        updateStatus(`${coordinate} : Coulé !`, "sunk");
      }

      // Vérifie si la partie est terminée
      if (data.gameOver) {
        handleGameOver(data.winner);
        return;
      }

      // Affiche les tirs de l'IA après un délai (si le joueur a raté)
      // L'IA peut tirer plusieurs fois si elle touche
      if (data.aiShots && data.aiShots.length > 0) {
        displayAiShots(data.aiShots, data.gameOver, data.winner);
      } else {
        // Le joueur a touché, il rejoue immédiatement
        isPlayerTurn = true;
        updateStatus("Touché ! Rejouez !", "hit");
      }
    } catch (error) {
      console.error("Erreur:", error);
      updateStatus("Erreur de connexion", "error");
      isPlayerTurn = true;
    }
  }

  /**
   * Applique visuellement le résultat d'un tir
   * @param {HTMLElement} cell - La cellule ciblée
   * @param {string} result - RATE, TOUCHE ou COULE
   * @param {string|null} shipType - Le type de bateau touché (pour la couleur)
   */
  function applyShot(cell, result, shipType = null) {
    cell.classList.remove("cell");
    if (result === "RATE") {
      cell.classList.add("miss");
    } else {
      cell.classList.add("hit");
      // Ajoute la classe du type de bateau pour afficher sa couleur
      if (shipType) {
        cell.classList.add(shipType);
      }
      if (result === "COULE") {
        cell.classList.add("sunk");
      }
    }
  }

  /**
   * Marque toutes les cases d'un bateau coulé
   * @param {array} coordinates - Les coordonnées du bateau
   * @param {string} shipType - Le type de bateau
   */
  function markSunkShip(coordinates, shipType) {
    coordinates.forEach((coord) => {
      const cell = document.querySelector(`#mainGrid td[data-coord="${coord}"]`);
      if (cell) {
        cell.classList.add("sunk", shipType);
      }
    });
  }

  /**
   * Affiche tous les tirs de l'IA avec un délai entre chaque
   * @param {array} aiShots - Tableau des tirs de l'IA
   * @param {boolean} gameOver - Si la partie est terminée
   * @param {string} winner - Le gagnant si gameOver
   */
  function displayAiShots(aiShots, gameOver, winner) {
    let index = 0;
    const delay = 800; // Délai entre chaque tir (ms)

    function showNextShot() {
      if (index >= aiShots.length) {
        // Tous les tirs ont été affichés
        if (gameOver) {
          handleGameOver(winner);
        } else {
          isPlayerTurn = true;
          updateStatus("À votre tour !", "info");
        }
        return;
      }

      const aiShot = aiShots[index];
      const playerCell = document.querySelector(
        `.mini-grid td[data-coord="${aiShot.coordinate}"]`,
      );

      if (playerCell) {
        applyShot(playerCell, aiShot.result, aiShot.shipType);

        // Si le bateau est coulé, marquer toutes ses cases sur la mini-grid
        if (aiShot.result === "COULE" && aiShot.shipCoordinates) {
          aiShot.shipCoordinates.forEach((coord) => {
            const cell = document.querySelector(
              `.mini-grid td[data-coord="${coord}"]`,
            );
            if (cell) {
              cell.classList.add("sunk");
            }
          });
        }
      }

      const resultText =
        aiShot.result === "RATE"
          ? "raté"
          : aiShot.result === "COULE"
            ? "coulé"
            : "touché";
      updateStatus(
        `L'IA tire sur ${aiShot.coordinate} : ${resultText} !`,
        aiShot.result.toLowerCase(),
      );

      index++;

      // Afficher le prochain tir après un délai
      if (index < aiShots.length) {
        setTimeout(showNextShot, delay);
      } else {
        // Dernier tir, attendre un peu puis rendre la main au joueur
        setTimeout(() => {
          if (gameOver) {
            handleGameOver(winner);
          } else {
            isPlayerTurn = true;
            updateStatus("À votre tour !", "info");
          }
        }, delay);
      }
    }

    // Commencer à afficher les tirs après un petit délai
    setTimeout(showNextShot, 500);
  }

  /**
   * Gère la fin de partie
   */
  function handleGameOver(winner) {
    isPlayerTurn = false;
    if (winner === "player") {
      updateStatus("🎉 Félicitations ! Vous avez gagné !", "victory");
    } else {
      updateStatus("💀 Défaite... L'IA a coulé tous vos bateaux.", "defeat");
    }
  }

  // Ajoute les listeners sur chaque cellule de la grille adverse
  aiGridCells.forEach((cell) => {
    const coord = cell.getAttribute("data-coord");
    if (coord) {
      cell.addEventListener("click", () => fireShot(coord, cell));
      cell.style.cursor = "pointer";
    }
  });
});
