document.addEventListener('DOMContentLoaded', () => {
  const modal = document.querySelector(".modal");
  const toggleTriggers = document.querySelectorAll(".toggle_modal");
  
  // Ouvrir automatiquement la modale si un nom a été soumis
  if(document.body.dataset.openModal === 'true'){
    modal.classList.add("active")
  }

  toggleTriggers.forEach(trigger => trigger.addEventListener("click", toggleModal))

  function toggleModal(e){
    e.preventDefault()
    modal.classList.toggle("active")
  }

  const modalContainer = document.querySelector('.modal_content')
  if(modalContainer){
    modalContainer.addEventListener("click", handleModalClick)
  }

  function handleModalClick(e){
    e.stopPropagation()
  }

  const btnClose = document.querySelector('.close-modal-btn')
  btnClose.addEventListener("click", () => {
    document.body.dataset.openModal = 'false';
    modal.classList.remove("active")
  });
});