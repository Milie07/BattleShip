// Gérer le click dans les cellules
document.addEventListener('DOMContentLoaded', () => {
  let cells = document.querySelectorAll('td.cell');
    cells.forEach(cell => {
      let coord = cell.getAttribute('data-coord');
      cell.addEventListener('click', () => {
        console.log('cliqué', coord)
      })
    });
});






