// ----------------------------------------------------------
// 🔹 Variables iniciales
// ----------------------------------------------------------
let filaIndex = 0;

// ----------------------------------------------------------
// 🔹 Evento agregar clase
// ----------------------------------------------------------
document.getElementById('btnAgregar').addEventListener('click', () => {

    const tbody = document.getElementById('tbodyClases');
    const fila = document.createElement('tr');
    fila.innerHTML = `
        <td><input type="time" class="form-control hora_inicio"></td>
        <td><input type="time" class="form-control hora_fin"></td>
        <td><select class="form-control programa"></select></td>
        <td><select class="form-control tema"></select></td>
        <td><select class="form-control instructor"></select></td>
        <td><select class="form-control aula"></select></td>
        <td><button class="btn btn-danger btn-sm btnEliminar">🗑</button></td>
    `;
    tbody.appendChild(fila);
    filaIndex++;
    cargarProgramas(filaIndex, fila);
    cargarInstructores(fila);
    cargarAulas(fila);

    // ----------------------------------------------------------
    // 🔹 Validación de hora fin > hora inicio
    // ----------------------------------------------------------
    const horaInicioInput = fila.querySelector('.hora_inicio');
    const horaFinInput = fila.querySelector('.hora_fin');

    horaFinInput.addEventListener('change', e => {
        const inicio = horaInicioInput.value;
        const fin = e.target.value;

        if (inicio && fin && fin <= inicio) {
            alert("⚠️ La hora fin debe ser mayor que la hora inicio.");
            e.target.value = "";
        }
    });
});

// ----------------------------------------------------------
// 🔹 Eliminar fila
// ----------------------------------------------------------
document.addEventListener('click', e => {
    if (e.target.classList.contains('btnEliminar')) {
        e.target.closest('tr').remove();
    }
});

// ----------------------------------------------------------
// 🔹 Cargar programas / temas dinámicamente
// ----------------------------------------------------------
function cargarProgramas(index, fila) {

    fetch('/Qe4R3t6yUi/')
        .then(res => res.json())
        .then(programas => {
            const selectPrograma = fila.querySelector('.programa');
            selectPrograma.innerHTML = `<option value="">--Seleccione--</option>`;
            programas.forEach(p => {
                selectPrograma.innerHTML += `<option value="${p.id}">${p.nombre}</option>`;
            });
            selectPrograma.addEventListener('change', e => {
                cargarTemas(e.target.value, fila.querySelector('.tema'));
            });
        });
}






function cargarTemas(programaId, selectTema) {
    console.log("📡 Cargando temas teóricos del programa:", programaId);

   fetch('/v2W7x9Y0Za/' + programaId)
        .then(res => res.json())
        .then(temas => {
            console.log("✅ Temas teóricos recibidos:", temas);

            selectTema.innerHTML = `<option value="">--Seleccione--</option>`;
            temas.forEach(t => {
                // 👇 aquí usamos el nombre correcto que devuelve PHP
                selectTema.innerHTML += `<option value="${t.id}">${t.nombre}</option>`;
            });
        })
        .catch(error => {
            console.error("❌ Error al traer temas teóricos:", error);
        });
}






// ----------------------------------------------------------
// 🔹 Cargar instructores (usado al crear cada fila)
// ----------------------------------------------------------
function cargarInstructores(fila) {

    fetch('/N2vH7kL9Qp/')
        .then(res => res.json())
        .then(instructores => {
            const selectInstructor = fila.querySelector('.instructor');
            selectInstructor.innerHTML = `<option value="">--Seleccione--</option>`;

            instructores.forEach(i => {
                selectInstructor.innerHTML += `<option value="${i.id}">${i.nombre}</option>`;
            });
        })
        .catch(error => {
            console.error("❌ Error al traer instructores:", error);
        });
}

// ----------------------------------------------------------
// 🔹 Cargar aulas (usado al crear cada fila)
// ----------------------------------------------------------
function cargarAulas(fila) {
    console.log("📡 Cargando aulas...");

    fetch('/L9M0n1O2Pq/')
        .then(res => res.json())
        .then(aulas => {
            console.log("✅ Aulas recibidas:", aulas);

            const selectAula = fila.querySelector('.aula');
            selectAula.innerHTML = `<option value="">--Seleccione--</option>`;

            aulas.forEach(a => {
                selectAula.innerHTML += `<option value="${a.id}">${a.nombre}</option>`;
            });
        })
        .catch(error => {
            console.error("❌ Error al traer aulas:", error);
        });
}


// ----------------------------------------------------------
// 🔹 Evento del botón Revisar clases
// ----------------------------------------------------------
document.getElementById('btnRevisar').addEventListener('click', () => {
    const fecha = document.getElementById('fecha').value;
    if (!fecha) {
        alert("⚠️ Debes seleccionar una fecha antes de revisar las clases.");
        return;
    }

    const filas = document.querySelectorAll('#tbodyClases tr');
    if (filas.length === 0) {
        alert("⚠️ No has agregado ninguna clase.");
        return;
    }

    let clases = [];
    let errores = [];

    filas.forEach((fila, index) => {
        const horaInicio = fila.querySelector('.hora_inicio').value;
        const horaFin = fila.querySelector('.hora_fin').value;
        const programa = fila.querySelector('.programa');
        const tema = fila.querySelector('.tema');
        const instructor = fila.querySelector('.instructor');
        const aula = fila.querySelector('.aula');

        // Validar campos
        if (!horaInicio || !horaFin || !programa.value || !tema.value || !instructor.value || !aula.value) {
            errores.push(`Fila ${index + 1}: Faltan campos obligatorios`);
        } else if (horaFin <= horaInicio) {
            errores.push(`Fila ${index + 1}: La hora fin debe ser mayor que la hora inicio`);
        }

        clases.push({
            hora_inicio: horaInicio,
            hora_fin: horaFin,
            programa: programa.options[programa.selectedIndex].text,
            tema: tema.options[tema.selectedIndex].text,
            instructor: instructor.options[instructor.selectedIndex].text,
            aula: aula.options[aula.selectedIndex].text
        });
    });

    // Si hay errores, mostrar y detener
    if (errores.length > 0) {
        alert("⚠️ Se encontraron errores:\n\n" + errores.join("\n"));
        return;
    }

    // Generar tabla HTML de revisión
    let tablaHTML = `
        <p><strong>Fecha:</strong> ${fecha}</p>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Hora inicio</th>
                    <th>Hora fin</th>
                    <th>Programa</th>
                    <th>Tema</th>
                    <th>Instructor</th>
                    <th>Aula</th>
                </tr>
            </thead>
            <tbody>
    `;

    clases.forEach((c, i) => {
        tablaHTML += `
            <tr>
                <td>${i + 1}</td>
                <td>${c.hora_inicio}</td>
                <td>${c.hora_fin}</td>
                <td>${c.programa}</td>
                <td>${c.tema}</td>
                <td>${c.instructor}</td>
                <td>${c.aula}</td>
            </tr>
        `;
    });

    tablaHTML += `
            </tbody>
        </table>
    `;

    document.getElementById('revisarContenido').innerHTML = tablaHTML;

    // Mostrar modal (usa Bootstrap 5)
    const modal = new bootstrap.Modal(document.getElementById('modalRevisar'));
    modal.show();
});



// ----------------------------------------------------------
// 🔹 Guardar todas las clases (final)
// ----------------------------------------------------------
document.getElementById('btnGuardar').addEventListener('click', () => {
    const fecha = document.getElementById('fecha').value;
    const filas = document.querySelectorAll('#tbodyClases tr');

    if (!fecha) {
        alert("⚠️ Debes seleccionar una fecha antes de guardar.");
        return;
    }

    if (filas.length === 0) {
        alert("⚠️ No hay clases para guardar.");
        return;
    }

    let clases = [];
    let errores = [];

    filas.forEach((fila, index) => {
        const horaInicio = fila.querySelector('.hora_inicio').value;
        const horaFin = fila.querySelector('.hora_fin').value;
        const programaId = fila.querySelector('.programa').value;
        const temaId = fila.querySelector('.tema').value;
        const instructorId = fila.querySelector('.instructor').value;
        const aulaId = fila.querySelector('.aula').value;

        if (!horaInicio || !horaFin || !programaId || !temaId || !instructorId || !aulaId) {
            errores.push(`Fila ${index + 1}: faltan datos`);
        }

        clases.push({
            hora_inicio: horaInicio,
            hora_fin: horaFin,
            programa_id: programaId,
            tema_id: temaId,
            instructor_id: instructorId,
            aula_id: aulaId
        });
    });

    if (errores.length > 0) {
        alert("⚠️ No se puede guardar:\n\n" + errores.join("\n"));
        return;
    }

    if (!confirm("¿Deseas guardar todas las clases?")) return;

    // Mostrar un spinner o bloquear botón mientras guarda
    const btnGuardar = document.getElementById('btnGuardar');
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = "💾 Guardando...";

    fetch('/u1V6w8X9Yz/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ fecha: fecha, clases: clases })
    })
    .then(res => res.json())
    .then(data => {
        console.log("📦 Respuesta del servidor:", data);

        btnGuardar.disabled = false;
        btnGuardar.innerHTML = "💾 Guardar todas";

        if (data.status === 'success') {
            // 🔹 Mostrar mensaje bonito
            alert("✅ " + data.message);

            // 🔹 Cerrar modal (Bootstrap 5)
            const modalEl = document.getElementById('modalRevisar');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            // 🔹 Limpiar tabla y fecha
            document.getElementById('tbodyClases').innerHTML = '';
            document.getElementById('fecha').value = '';

        } else {
            alert("❌ " + (data.message || "No se pudieron guardar las clases."));
        }
    })
    .catch(error => {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = "💾 Guardar todas";
        console.error("❌ Error en la solicitud:", error);
        alert("❌ Ocurrió un error al enviar los datos al servidor.");
    });
});
