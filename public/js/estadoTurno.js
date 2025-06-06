// Función para consultar el estado del turno
async function consultarEstadoTurno(codigo) {
  try {
    const response = await fetch(`/estado-turno/${codigo}`, {
      headers: {
        'Accept': 'application/json',
      }
    });

    if (!response.ok) {
      if (response.status === 404) {
        console.error('Turno no encontrado');
        return null;
      }
      throw new Error('Error en la consulta del turno');
    }

    const data = await response.json();

    // Aquí procesas y muestras la info
    console.log('Mi turno:', data.mi_turno);
    console.log('Turno en atención:', data.en_atencion);
    console.log('Turnos delante:', data.cantidad_delante);

    return data;

  } catch (error) {
    console.error('Error al obtener el estado del turno:', error);
    return null;
  }
}
