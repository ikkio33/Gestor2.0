document.addEventListener('DOMContentLoaded', function() {
    
    console.log("JS de Funcionario cargado correctamente");
    const btnLlamarTurno = document.querySelector('.btn-llamar-turno');
    if (btnLlamarTurno) {
        btnLlamarTurno.addEventListener('click', function(event) {
            if (!confirm("¿Estás seguro de que quieres llamar este turno?")) {
                event.preventDefault();
            }
        });
    }
});
