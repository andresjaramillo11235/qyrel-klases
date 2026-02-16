// assets/js/calendarioClases.js

document.querySelectorAll('.calendar-cell').forEach(function(cell) {
    cell.addEventListener('click', function() {
        var date = this.getAttribute('data-date');
        var hour = this.getAttribute('data-hour');

        // Log para verificar los valores de fecha y hora
        console.log('Fecha:', date);
        console.log('Hora:', hour);

        // Realizar una solicitud AJAX para obtener los datos del formulario
        fetch(`/clases_teoricas/create/${date}/${hour}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            // Log para verificar el estado de la respuesta
            console.log('Estado de la respuesta:', response.status);
            return response.json();
        })
        .then(data => {
            // Log para verificar los datos recibidos
            console.log('Datos recibidos:', data);

            // Asegúrate de que el modal está en el DOM
            var createClaseModalElement = document.getElementById('createClaseModal');
            if (!createClaseModalElement) {
                console.error('Modal no encontrado en el DOM.');
                return;
            }

            // Asegúrate de que los elementos del formulario existen antes de intentar manipularlos
            var programaSelect = document.getElementById('programa_id');
            var aulaSelect = document.getElementById('aula_id');
            var instructorSelect = document.getElementById('instructor_id');
            var estadoSelect = document.getElementById('estado_id');
            var fechaInput = document.getElementById('fecha');
            var horaInicioInput = document.getElementById('hora_inicio');
            var horaFinInput = document.getElementById('hora_fin');

            if (!programaSelect || !aulaSelect || !instructorSelect || !estadoSelect || !fechaInput || !horaInicioInput || !horaFinInput) {
                console.error('Elementos del formulario no encontrados en el DOM.');
                return;
            }

            programaSelect.innerHTML = '<option value="">Seleccione un programa</option>';
            data.programas.forEach(function(programa) {
                var option = document.createElement('option');
                option.value = programa.id;
                option.textContent = programa.nombre;
                programaSelect.appendChild(option);
            });

            aulaSelect.innerHTML = '<option value="">Seleccione un aula</option>';
            data.aulas.forEach(function(aula) {
                var option = document.createElement('option');
                option.value = aula.id;
                option.textContent = aula.nombre;
                aulaSelect.appendChild(option);
            });

            instructorSelect.innerHTML = '<option value="">Seleccione un instructor</option>';
            data.instructores.forEach(function(instructor) {
                var option = document.createElement('option');
                option.value = instructor.id;
                option.textContent = instructor.nombres + ' ' + instructor.apellidos;
                instructorSelect.appendChild(option);
            });

            estadoSelect.innerHTML = '<option value="">Seleccione un estado</option>';
            data.estados.forEach(function(estado) {
                var option = document.createElement('option');
                option.value = estado.id;
                option.textContent = estado.nombre;
                estadoSelect.appendChild(option);
            });

            fechaInput.value = data.fecha;
            horaInicioInput.value = data.hora_inicio;
            var hourEnd = parseInt(data.hora_inicio.split(':')[0]) + 1;
            horaFinInput.value = hourEnd < 10 ? '0' + hourEnd + ':00' : hourEnd + ':00';

            var createClaseModal = new bootstrap.Modal(createClaseModalElement);
            createClaseModal.show();

            // Log para verificar si el modal se muestra correctamente
            console.log('Modal mostrado:', createClaseModal._isShown);
        })
        .catch(error => {
            // Log para capturar cualquier error en la solicitud o procesamiento de datos
            console.error('Error al cargar los datos del formulario:', error);
        });
    });
});

document.getElementById('programa_id').addEventListener('change', function() {
    var programaId = this.value;
    fetch('/clases_teoricas/getClasesByPrograma/' + programaId)
        .then(response => response.json())
        .then(data => {
            var claseSelect = document.getElementById('clase_teorica_programa_id');
            claseSelect.innerHTML = '<option value="">Seleccione una clase</option>';
            data.forEach(function(clase) {
                var option = document.createElement('option');
                option.value = clase.id;
                option.textContent = clase.nombre_clase;
                claseSelect.appendChild(option);
            });
        });
});
