<?php $rourtes = include('../config/Routes.php'); ?>

<?php if (isset($_SESSION['success_message'])) : ?>
    <script>
        const successMessage = <?php echo json_encode($_SESSION['success_message']); ?>;
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: successMessage
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])) : ?>
    <script>
        const errorMessage = <?php echo json_encode($_SESSION['error_message']); ?>;
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
        } else {
            alert(errorMessage);
        }
    </script>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>


<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">📂 Cargue masivo de clases teóricas</h4>
        </div>
        <div class="card-body">
            <form action="<?php echo $rourtes['clases_teoricas_carga_masiva_process'] ?>" method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label for="archivo" class="form-label">Seleccione la plantilla Excel (.xlsx)</label>
                    <input type="file" name="archivo" id="archivo" class="form-control" accept=".xlsx" required>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-upload"></i> Cargar y procesar
                </button>
            </form>
        </div>
    </div>
</div>