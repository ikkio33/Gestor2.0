$(document).ready(function () {
    // Asignar mesón al funcionario
    $('#form-asignar-meson').submit(function (e) {
        e.preventDefault();
        const $btn = $(this).find('button[type=submit]');
        $btn.prop('disabled', true);

        const meson_id = $('#meson').val();
        const token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: window.rutasMeson.asignar,
            type: 'POST',
            data: {
                _token: token,
                meson_id: meson_id
            },
            success: function (response) {
                if (response.success || response.message) {
                    $('#meson-container').html('<div class="alert alert-success">' + (response.message || 'Mesón asignado.') + '</div>');
                    $('#meson-liberado').show();
                    $('#form-asignar-meson').hide();
                } else {
                    alert(response.message || 'Error al asignar el mesón.');
                }
            },
            error: function () {
                alert('Ocurrió un error al asignar el mesón.');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    // Liberar mesón
    $('#liberar-meson').click(function () {
        const $btn = $(this);
        $btn.prop('disabled', true);

        const token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: window.rutasMeson.liberar,
            type: 'POST', // Método correcto según rutas
            data: {
                _token: token
            },
            success: function (response) {
                if (response.success || response.message) {
                    $('#meson-container').html('<div class="alert alert-success">' + (response.message || 'Mesón liberado.') + '</div>');
                    $('#meson-liberado').hide();
                    $('#form-asignar-meson').show();
                } else {
                    alert(response.message || 'Error al liberar el mesón.');
                }
            },
            error: function () {
                alert('Error liberando mesón, revisa consola.');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });
});
