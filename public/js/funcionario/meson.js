$(document).ready(function() {
    // Asignar mesón al funcionario
    $('#form-asignar-meson').submit(function(e) {
        e.preventDefault();

        var meson_id = $('#meson').val();
        var token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '{{ route("funcionario.meson.asignar") }}',
            type: 'POST',
            data: {
                _token: token,
                meson_id: meson_id
            },
            success: function(response) {
                if (response.success) {
                    $('#meson-container').html('<div class="alert alert-success">' + response.message + '</div>');
                    $('#meson-liberado').show();
                    $('#form-asignar-meson').hide();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Ocurrió un error al asignar el mesón.');
            }
        });
    });

    // Liberar mesón
    $('#liberar-meson').click(function() {
        var meson_id = $('#meson').val();
        var token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '{{ route("funcionario.meson.liberar") }}',
            type: 'DELETE',
            data: {
                _token: token,
                meson_id: meson_id
            },
            success: function(response) {
                if (response.success) {
                    $('#meson-container').html('<div class="alert alert-success">' + response.message + '</div>');
                    $('#meson-liberado').hide();
                    $('#form-asignar-meson').show();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Ocurrió un error al liberar el mesón.');
            }
        });
    });
});
