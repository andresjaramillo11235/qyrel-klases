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

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo $routes['clases_practicas_listado_estudiante'] ?>">Clases Prácticas Programadas</a></li>
                    <li class="breadcrumb-item" aria-current="page">Calificar Clase</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header">
                <h5><i class="ph-duotone ph-star-half"></i>
                    Califica tu experiencia de conducción.
                </h5>
            </div>

            <div class="card-body">

                <form method="POST" action="<?= $routes['clases_practicas_estudiante_calificar_store'] ?>">

                    <!-- <input type="hidden" name="control_clase_practica_id" value="<?= htmlspecialchars($controlClaseId) ?>"> -->
                    <input type="hidden" name="clase_practica_id" value="<?= htmlspecialchars($clase['clase_practica_id']) ?>">

                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <p><strong>Nombre de la Clase:</strong> <?= htmlspecialchars($clase['clase_nombre']) ?></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <p><strong>Fecha:</strong> <?= htmlspecialchars($clase['fecha']) ?></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <p><strong>Horario:</strong> <?= htmlspecialchars($clase['hora_inicio']) ?> - <?= htmlspecialchars($clase['hora_fin']) ?></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Vehículo:</strong>
                                <?= htmlspecialchars(strtoupper($clase['vehiculo_placa'])) ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <p><strong>Instructor:</strong><br>
                                <?= htmlspecialchars(strtoupper($clase['instructor_nombre'])) ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Calificación (Estrellas)</label>
                                <div id="rating-container">
                                    <input type="radio" name="estudiante_calificacion" id="star5" value="5">
                                    <label for="star5" title="5 estrellas">&#9733;</label>

                                    <input type="radio" name="estudiante_calificacion" id="star4" value="4">
                                    <label for="star4" title="4 estrellas">&#9733;</label>

                                    <input type="radio" name="estudiante_calificacion" id="star3" value="3">
                                    <label for="star3" title="3 estrellas">&#9733;</label>

                                    <input type="radio" name="estudiante_calificacion" id="star2" value="2">
                                    <label for="star2" title="2 estrellas">&#9733;</label>

                                    <input type="radio" name="estudiante_calificacion" id="star1" value="1">
                                    <label for="star1" title="1 estrella">&#9733;</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Algún comentario, positivo o negativo.</label>
                                <textarea name="estudiante_observaciones" id="observaciones" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="problemas" class="form-label">Alguna falla que quieras reportar.</label>
                                <select name="problemas[]" id="problemas" class="form-select" multiple>
                                    <?php foreach ($problemas as $problema): ?>
                                        <option value="<?= htmlspecialchars($problema['id']) ?>"><?= htmlspecialchars($problema['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary">Enviar Calificación</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
