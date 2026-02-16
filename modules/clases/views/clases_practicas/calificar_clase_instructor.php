<?php include '../shared/utils/AjustarImagen.php' ?>
<?php $routes = include '../config/Routes.php'; ?>

<style>
  #rating-container {
    direction: rtl;
    display: inline-flex;
    justify-content: center;
    gap: 5px;
  }

  #rating-container input[type="radio"] {
    display: none;
  }

  #rating-container label {
    font-size: 30px;
    color: #ccc;
    /* Color gris por defecto para las estrellas no seleccionadas */
    cursor: pointer;
  }

  #rating-container input[type="radio"]:checked~label,
  #rating-container label:hover,
  #rating-container label:hover~label {
    color: #4da6ff;
    /* Azul cielo */
  }
</style>

<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="/home/"><i class="ti ti-home"></i> Inicio</a></li>
          <li class="breadcrumb-item" aria-current="page"><a href="<?php echo $routes['clases_practicas_listado_instructor'] ?>"> Clases prácticas</a></li>
          <li class="breadcrumb-item" aria-current="page"> Gestionar la clase</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5><i class="ph-duotone ph-info"></i>
          Información de la clase.
        </h5>
      </div>

      <div class="card-body">
        <input type="hidden" name="clase_practica_id" value="<?= htmlspecialchars($clase['clase_practica_id']) ?>">

        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <p><strong>Nombre de la Clase:</strong><br> <?= htmlspecialchars($clase['clase_nombre']) ?></p>
            </div>
          </div>

          <div class="col-md-6">
            <div class="mb-3">
              <p><strong>Fecha:</strong><br> <?= htmlspecialchars($clase['fecha']) ?></p>
              <p><strong>Horario:</strong><br> <?= date('H:i', strtotime($clase['hora_inicio'])) ?> - <?= date('H:i', strtotime($clase['hora_fin'])) ?>
              </p>
            </div>
          </div>

          <div class="col-md-6">
            <div class="mb-3">
              <strong>Vehículo:</strong><br>
              <?= htmlspecialchars(strtoupper($clase['vehiculo_placa'])) ?>
            </div>
          </div>

          <div class="col-md-6">
            <p><strong>Estudiante:</strong><br>
              <?= htmlspecialchars(strtoupper($clase['estudiante_nombre'])) ?>
              <br>
              <?php
              // Determinar la ruta de la foto del estudiante (si no tiene, usar la imagen por defecto)
              $estudiantePhotoPath = !empty($clase['estudiante_foto'])
                ? "../files/fotos_estudiantes/{$clase['estudiante_foto']}"
                : "../files/fotos_estudiantes/img-defecto-estudiante.webp";

              // Ajustar el tamaño de la imagen
              list($width, $height) = ajustarImagen($estudiantePhotoPath, 200, 200);
              ?>

              <img src="<?= htmlspecialchars($estudiantePhotoPath) ?>"
                alt="Foto Estudiante"
                style="width: <?= $width ?>px; height: <?= $height ?>px;"
                class="rounded">
            </p>
          </div>
        </div>
      </div>
    </div>






    <?php
    if (!function_exists('e')) {
      function e($v)
      {
        return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
      }
    }
    if (!function_exists('renderStars')) {
      function renderStars($val)
      {
        if ($val === null || $val === '') return '<span class="text-muted">—</span>';
        $n = max(0, min(5, (int)$val));
        $s = '';
        for ($i = 1; $i <= 5; $i++) {
          $s .= '<i class="ti ' . ($i <= $n ? 'ti-star-filled text-warning' : 'ti-star text-muted') . '"></i>';
        }
        return $s;
      }
    }
    ?>

    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Historial de clases del estudiante</h6>
        <div class="small text-muted">
          Total: <strong><?= (int)($totalClases ?? 0) ?></strong>
          &nbsp;|&nbsp; Finalizadas: <strong><?= (int)($finalizadas ?? 0) ?></strong>
          &nbsp;|&nbsp; Prom. instr.: <strong><?= $promInstructor !== null ? e($promInstructor) : '—' ?></strong>
        </div>
      </div>
      <div class="card-body">
        <?php if (empty($historialClases)): ?>
          <div class="alert alert-info mb-0">No hay clases registradas para esta matrícula.</div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm table-striped align-middle">
              <thead>
                <tr>
                  <th style="white-space:nowrap;">Fecha</th>
                  <th style="white-space:nowrap;">Horario</th>
                  <th>Clase</th>
                  <th>Instructor</th>
                  <th style="white-space:nowrap;">Calificación (Instr.)</th>
                  <th style="min-width:240px;">Observaciones (Instr.)</th>
                  <th>Vehículo</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($historialClases as $h): ?>
                  <tr>
                    <td><?= e($h['fecha'] ?? '') ?></td>
                    <td>
                      <?php
                      $hi = $h['hora_inicio_real'] ?: $h['hora_inicio'];
                      $hf = $h['hora_fin_real']  ?: $h['hora_fin'];
                      echo e(substr((string)$hi, 0, 5) . ' - ' . substr((string)$hf, 0, 5));
                      ?>
                    </td>
                    <td><?= e($h['clase_nombre'] ?? '') ?></td>
                    <td><?= e($h['instructor_nombre'] ?? '') ?></td>


                    <td>
                      <?php
                      $ci = isset($h['instructor_calificacion']) ? (int)$h['instructor_calificacion'] : null;
                      if ($ci >= 1 && $ci <= 5) {
                        echo e($ci) . ' / 5';
                      } else {
                        echo '<span class="text-muted">Sin calificar</span>';
                      }
                      ?>
                    </td>


                    <td>
                      <?php
                      $rawObs = (string)($h['instructor_observaciones'] ?? '');
                      $trim   = trim($rawObs);
                      if ($trim === '') {
                        echo '<span class="text-muted">Sin observaciones</span>';
                      } else {
                        // Muestra resumido y deja el completo en tooltip
                        $short = function_exists('mb_strimwidth')
                          ? mb_strimwidth($trim, 0, 120, '…', 'UTF-8')
                          : (strlen($trim) > 120 ? substr($trim, 0, 117) . '…' : $trim);
                        echo '<span title="' . e($trim) . '">' . e($short) . '</span>';
                      }
                      ?>
                    </td>




                    <td><?= e($h['vehiculo_placa'] ?? '') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>










    <div class="card">
      <div class="card-header">
        <h5><i class="ph-duotone ph-star-half"></i>
          Gestionar la clase
        </h5>
      </div>
      <div class="card-body">
        <form method="POST" action="<?= $routes['clases_practicas_instructor_calificar_store'] ?>" id="formularioClase">
          <input type="hidden" name="clase_practica_id" value="<?= htmlspecialchars($clase['clase_practica_id']) ?>">

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <div class="form-check form-switch mb-3">
                  <input class="form-check-input" type="checkbox" id="switchFinalizarClase" name="finalizar_clase" value="1">
                  <label class="form-check-label" for="switchFinalizarClase">
                    <strong id="labelFinalizarClase">Finalizar la clase</strong>
                  </label>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Calificar al estudiante(Estrellas)</label><br>
                <div id="rating-container">
                  <input type="radio" name="instructor_calificacion" id="star5" value="5">
                  <label for="star5" title="5 estrellas">&#9733;</label>

                  <input type="radio" name="instructor_calificacion" id="star4" value="4">
                  <label for="star4" title="4 estrellas">&#9733;</label>

                  <input type="radio" name="instructor_calificacion" id="star3" value="3">
                  <label for="star3" title="3 estrellas">&#9733;</label>

                  <input type="radio" name="instructor_calificacion" id="star2" value="2">
                  <label for="star2" title="2 estrellas">&#9733;</label>

                  <input type="radio" name="instructor_calificacion" id="star1" value="1">
                  <label for="star1" title="1 estrella">&#9733;</label>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="observaciones" class="form-label">Algún comentario, positivo o negativo.</label>
                <textarea name="instructor_observaciones" id="observaciones" class="form-control" rows="3"></textarea>
              </div>
            </div>
            <div class="col-md-12 text-end">
              <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("formularioClase");
    const switchFinalizarClase = document.getElementById("switchFinalizarClase");
    const estrellas = document.querySelectorAll('input[name="instructor_calificacion"]');
    const observaciones = document.getElementById("observaciones");
    const btnEnviar = document.getElementById("btnEnviarFormulario");

    form.addEventListener("submit", function(event) {
      let calificacionSeleccionada = false;

      estrellas.forEach((estrella) => {
        if (estrella.checked) {
          calificacionSeleccionada = true;
        }
      });

      const observacionesTexto = observaciones.value.trim();

      // Validar condiciones
      if (!switchFinalizarClase.checked || !calificacionSeleccionada || observacionesTexto === "") {
        event.preventDefault();

        let mensaje = "Debes finalizar la clase, calificar al estudiante y agregar observaciones antes de enviar.";

        if (switchFinalizarClase.checked && calificacionSeleccionada && observacionesTexto === "") {
          mensaje = "Debes agregar observaciones antes de enviar.";
        } else if (!switchFinalizarClase.checked || !calificacionSeleccionada) {
          mensaje = "Debes finalizar la clase y calificar al estudiante antes de enviar.";
        }

        Swal.fire({
          icon: "warning",
          title: "Atención",
          text: mensaje,
          confirmButtonColor: "#3085d6",
          confirmButtonText: "Entendido"
        });
      }
    });
  });
</script>