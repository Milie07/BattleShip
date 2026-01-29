// Validation de l'input utilisateur

document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form');
  const playerInput = document.getElementById('player-name');

  if (!form || !playerInput) return;

  // Créer la div pour le message d'erreur
  const errorDiv = document.createElement('div');
  errorDiv.className = 'error-message';
  errorDiv.style.color = 'red';
  errorDiv.style.display = 'none';
  playerInput.parentNode.insertBefore(errorDiv, playerInput.nextSibling);

  /**
   * Valide le nom du joueur
   * @@param {string} name - le nom du joueur
   * @return {object} - {valid: boolean, message: string}
   */
  function validatePlayerName(name) {
    const validName = name.trim();
    if (validName.length < 2) {
      return { 
        valid: false, 
        message: 'Le nom doit contenir au moins 2 caractères.' 
      };
    }
    
    if (validName.length > 20) {
      return { 
        valid: false, 
        message: 'Le nom ne doit pas dépasser 20 caractères.' 
      };
    }
    // Regex : lettres, chiffres, espaces, tirets uniquement
    const validPattern = /^[\p{L}\p{N}\p{Extended_Pictographic}\s\-]+$/u;
    if (!validPattern.test(validName)) {
      return { 
        valid: false, 
        message: 'Le nom contient des caractères non autorisés.' 
      };
    }
    return {
      valid: true,
      message: ""
    };
  }  

  // Validation à la soumission du formulaire
  form.addEventListener('submit', (e) => {
    const result = validatePlayerName(playerInput.value);
    if (!result.valid) {
      e.preventDefault();
      errorDiv.textContent = result.message;
      errorDiv.style.display = 'block';
      playerInput.classList.add('invalid');
      playerInput.focus();
    }
  });
});