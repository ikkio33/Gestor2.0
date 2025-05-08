document.addEventListener('DOMContentLoaded', () => {
    setInterval(() => location.reload(), 1000);

    const pantalla = document.documentElement;
    if (pantalla.requestFullscreen) {
        pantalla.requestFullscreen().catch(() => {});
    } else if (pantalla.webkitRequestFullscreen) {
        pantalla.webkitRequestFullscreen();
    } else if (pantalla.msRequestFullscreen) {
        pantalla.msRequestFullscreen();
    }

    if (window.nuevoTurno) {
        const mensaje = "Turno " + window.codigoTurnoActual + ", diríjase al mesón " + window.mesonActual + ".";
        const voz = new SpeechSynthesisUtterance(mensaje);
        voz.lang = "es-CL";
        speechSynthesis.speak(voz);
    }
});
