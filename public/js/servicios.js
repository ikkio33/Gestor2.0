document.addEventListener('DOMContentLoaded', () => {
    // Mostrar/Ocultar materias al hacer clic en "Mostrar Materias"
    const serviceButtons = document.querySelectorAll('.btn-service');

    serviceButtons.forEach((button) => {
        button.addEventListener('click', function () {
            const serviceId = this.dataset.serviceId;
            const materiasContainer = document.querySelector(`#materias-${serviceId}`);

            if (materiasContainer) {
                materiasContainer.classList.toggle('d-none');

                this.textContent = materiasContainer.classList.contains('d-none')
                    ? 'Mostrar Materias'
                    : 'Ocultar Materias';
            }
        });
    });

    // Prevenir múltiples envíos: desactiva el botón al enviar el formulario
    const forms = document.querySelectorAll('form');

    forms.forEach((form) => {
        form.addEventListener('submit', function (e) {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Procesando...'; // Feedback visual
            }
        });
    });
});
