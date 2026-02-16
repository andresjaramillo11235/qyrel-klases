<?php
require_once '../../controllers/IngresosController.php';

$id = $_GET['id'];
$ingresosController = new IngresosController();
$ingreso = $ingresosController->getIngreso($id);
?>

<h2>Detalles del Ingreso</h2>
<p><strong>ID:</strong> <?php echo $ingreso['id']; ?></p>
<p><strong>Matrícula:</strong> <?php echo $ingreso['matricula_id']; ?></p>
<p><strong>Valor:</strong> <?php echo $ingreso['valor']; ?></p>
<p><strong>Motivo:</strong> <?php echo $ingreso['motivo_ingreso_id']; ?></p>
<p><strong>Tipo:</strong> <?php echo $ingreso['tipo_ingreso_id']; ?></p>
<p><strong>Fecha:</strong> <?php echo $ingreso['fecha']; ?></p>
<p><strong>Observaciones:</strong> <?php echo $ingreso['observaciones']; ?></p>
<a href="index.php" class="btn btn-secondary">Volver</a>
