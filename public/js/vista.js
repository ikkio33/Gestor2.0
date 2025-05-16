document.addEventListener('DOMContentLoaded', () => {
    // Refresca la pantalla cada segundo
    setInterval(() => location.reload(), 1000);

    // Intentar poner pantalla completa
    const pantalla = document.documentElement;
    const intentarFullscreen = () => {
        if (pantalla.requestFullscreen) {
            pantalla.requestFullscreen().catch(() => {});
        } else if (pantalla.webkitRequestFullscreen) {
            pantalla.webkitRequestFullscreen();
        } else if (pantalla.msRequestFullscreen) {
            pantalla.msRequestFullscreen();
        }
    };

    // Ejecutar fullscreen al hacer clic (por seguridad del navegador)
    document.addEventListener('click', intentarFullscreen, { once: true });

    // Reproducir mensaje si hay un nuevo turno
    if (window.nuevoTurno) {
        const mensaje = "Turno " + window.codigoTurnoActual + ", diríjase al mesón " + window.mesonActual + ".";
        const voz = new SpeechSynthesisUtterance(mensaje);
        voz.lang = "es-CL";
        voz.volume = 1;
        voz.rate = 1;
        voz.pitch = 1;

        // Hablar después de una breve pausa (evita bloqueo de autoplay)
        setTimeout(() => {
            speechSynthesis.speak(voz);
        }, 500);
    }
});

