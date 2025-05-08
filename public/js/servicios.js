document.addEventListener('DOMContentLoaded', () => {
  const serviceButtons = document.querySelectorAll('.btn-service');
  
  serviceButtons.forEach((button) => {
      button.addEventListener('click', function() {
          const serviceId = this.dataset.serviceId;
          const materiasContainer = document.querySelector(`#materias-${serviceId}`);

         
          if (materiasContainer) {
              materiasContainer.classList.toggle('d-none');
              
              
              if (materiasContainer.classList.contains('d-none')) {
                  this.textContent = 'Mostrar Materias';
              } else {
                  this.textContent = 'Ocultar Materias';
              }
          }
      });
  });
});
