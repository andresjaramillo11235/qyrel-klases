// Función para mostrar las clases del día en el calendario
function mostrarClasesDelDia() {
    var fechaActual = $('#fecha_actual').val();
    if (fechaActual) {
        $.ajax({
            url: '/instructores/clases_del_dia/',
            method: 'POST',
            data: { fecha_actual: fechaActual },
            success: function(data) {
                var clases = JSON.parse(data);
                var clasesDelDia = $('#clasesDelDia');
                clasesDelDia.empty();
                clases.forEach(function(clase) {
                    var card = `
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">${clase.nombre}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">${clase.fecha} - ${clase.hora_inicio} a ${clase.hora_fin}</h6>
                                <p class="card-text">${clase.descripcion}</p>
                                <p class="card-text"><strong>Estudiante:</strong> ${clase.estudiante_nombres} ${clase.estudiante_apellidos}</p>
                                <p class="card-text"><strong>Vehículo:</strong> ${clase.vehiculo_placa}</p>
                                <p class="card-text"><strong>Lugar de Recogida:</strong> ${clase.lugar}</p>
                                <a href="#" class="card-link" data-bs-toggle="modal" data-bs-target="#modalDetalleClase" data-clase-id="${clase.id}">Ver Detalles</a>
                            </div>
                        </div>
                    `;
                    clasesDelDia.append(card);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener las clases del día:', status, error);
                alert('Error al obtener las clases del día.');
            }
        });
    } else {
        alert('Por favor, seleccione una fecha.');
    }
}

$('#btnMostrarCalendario').on('click', mostrarClasesDelDia);
