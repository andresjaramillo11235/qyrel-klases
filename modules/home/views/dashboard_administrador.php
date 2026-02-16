<div class="row">
  <!-- Card 1 -->
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body py-4 px-2 text-center">
        <div class="avtar bg-primary rounded-circle mx-auto mb-2">
          <!-- Ícono -->
          <svg class="pc-icon text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
            <path d="M4 9a1 1 0 1 0 0 2 1 1 0 0 0 0-2zM12 9a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
            <path fill-rule="evenodd" d="M2 6h12l-1.5-4h-9L2 6zm0 1a1 1 0 0 0-1 1v4h1v1a1 1 0 1 0 2 0v-1h8v1a1 1 0 1 0 2 0v-1h1V8a1 1 0 0 0-1-1H2z" />
          </svg>
        </div>
        <a href="/clasespracticascronograma/" class="text-decoration-none">
          <h6 class="mb-0 text-muted">Clases Prácticas</h6>
        </a>
      </div>
    </div>
  </div>

  <!-- Card 2 -->
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body py-4 px-2 text-center">
        <div class="avtar bg-primary rounded-circle mx-auto mb-2">
          <svg class="pc-icon text-white" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h6.5L14 4.5zM10 1.5V5h3.5L10 1.5z" />
            <path d="M3 6h10v1H3V6zm0 2h10v1H3V8zm0 2h7v1H3v-1z" />
          </svg>
        </div>
        <a href="/matriculas/" class="text-decoration-none">
          <h6 class="mb-0 text-muted">Matrículas</h6>
        </a>
      </div>
    </div>
  </div>

  <!-- Card 3 -->
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body py-4 px-2 text-center">
        <div class="avtar bg-success rounded-circle mx-auto mb-2">
          <svg class="pc-icon text-white" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 0L0 3l8 3 6.5-2.437V9h1V3L8 0z" />
            <path d="M6.5 6.933v1.21a5.56 5.56 0 0 0-3.5 5.107c0 .414.336.75.75.75h7.5a.75.75 0 0 0 .75-.75c0-2.251-1.473-4.165-3.5-5.107v-1.21l-1 .375-1-.375z" />
          </svg>
        </div>
        <a href="/estudiantes/" class="text-decoration-none">
          <h6 class="mb-0 text-muted">Estudiantes</h6>
        </a>
      </div>
    </div>
  </div>

  <!-- Card 4 -->
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body py-4 px-2 text-center">
        <div class="avtar bg-warning rounded-circle mx-auto mb-2">
          <svg class="pc-icon text-white" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8.5 13a3.5 3.5 0 1 0 0-7H7a2 2 0 0 1 0-4h1.5a3.5 3.5 0 0 0 0-7H7a.5.5 0 0 0 0 1h1.5a2.5 2.5 0 0 1 0 5H7a3 3 0 0 0 0 6h1.5a2.5 2.5 0 1 1 0 5H7a.5.5 0 0 0 0 1h1.5z" />
          </svg>
        </div>
        <a href="/mngtAFsdx48/" class="text-decoration-none">
          <h6 class="mb-0 text-muted">Ingresos</h6>
        </a>
      </div>
    </div>
  </div>
</div>





<div class="row">
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card statistics-card-1 overflow-hidden">
      <div class="card-body">
        <img src="../assets/images/widget/img-status-4.svg" alt="img" class="img-fluid img-bg">
        <h5 class="mb-4">Total, clases programadas hoy.</h5>
        <h1 class="f-w-300 d-flex align-items-center m-b-0">
          <?php echo $totalClases ?>
        </h1>
      </div>
    </div>
  </div>

  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card statistics-card-1 overflow-hidden">
      <div class="card-body">
        <img src="../assets/images/widget/img-status-4.svg" alt="img" class="img-fluid img-bg">
        <h5 class="mb-4">Total, instructores programados hoy.</h5>
        <h1 class="f-w-300 d-flex align-items-center m-b-0">
          <?php echo $totalInstructores ?>
        </h1>
      </div>
    </div>
  </div>

  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card statistics-card-1 overflow-hidden">
      <div class="card-body">
        <img src="../assets/images/widget/img-status-4.svg" alt="img" class="img-fluid img-bg">
        <h5 class="mb-4">Total, clases programadas mes.</h5>
        <h1 class="f-w-300 d-flex align-items-center m-b-0">
          <?php echo $clasesMes ?>
        </h1>
      </div>
    </div>
  </div>
</div>


















