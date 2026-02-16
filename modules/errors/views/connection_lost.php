<?php if (isset($_SESSION['db_error'])) : ?>
  <div class="alert alert-danger">
    <?= $_SESSION['db_error']; ?>
    <?php unset($_SESSION['db_error']); ?>
  </div>
<?php endif; ?>








<div class="auth-main v1">
  <div class="auth-wrapper">
    <div class="auth-form">
      <div class="error-card">
        <div class="card-body">
          <div class="error-image-block">
            <img class="img-fluid" src="../assets/images/pages/img-connection-lost.png" alt="img">
          </div>
          <div class="text-center">
            <h1 class="mt-2">Ups! Algo salió mal</h1>
            <p class="mt-2 mb-4 text-muted f-20"Parece que hemos encontrado un problema mientras intentábamos cargar esta página. Esto puede deberse a una conexión interrumpida, un problema temporal o un error en el sistema.¿Qué puedes hacer?

Verifica tu conexión a internet.
Intenta recargar la página.
Si el problema persiste, puedes volver a la [página de inicio] o contactar con el soporte técnico.
Nos disculpamos por las molestias y trabajaremos para solucionar el inconveniente lo más pronto posible.

</p>
            <a class="btn btn-primary d-inline-flex align-items-center mb-3" href="../dashboard/index.html"><i class="ph-duotone ph-house me-2"></i> Back to Home</a>
          </div>
        </div>
      </div>
    </div>
   
  </div>
</div>