<h4>Dashboard de Matrículas</h4>

<div class="row">
    <!-- Matrículas del Día -->
    <div class="col-md-6">
        <div class="card p-3">
            <h6>Matrículas del Día</h6>
            <?php
                $dia = array_filter($resumen, fn($r) => $r['tipo'] === 'dia');
                $dia = reset($dia) ?: ['total_matriculas' => 0, 'total_valor' => 0];
            ?>
            <h4 class="text-success"><?= $dia['total_matriculas'] ?> matrículas</h4>
            <p>Valor total: $<?= number_format((float)($dia['total_valor'] ?? 0), 0, ',', '.') ?></p>
        </div>
    </div>

    <!-- Matrículas del Mes -->
    <div class="col-md-6">
        <div class="card p-3">
            <h6>Matrículas del Mes</h6>
            <?php
                $mes = array_filter($resumen, fn($r) => $r['tipo'] === 'mes');
                $mes = reset($mes) ?: ['total_matriculas' => 0, 'total_valor' => 0];
            ?>
            <h4 class="text-success"><?= $mes['total_matriculas'] ?> matrículas</h4>
            <p>Valor total: $<?= number_format((float)($mes['total_valor'] ?? 0), 0, ',', '.') ?></p>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <i class="ti ti-bar-chart"></i> Matrículas por Programa (Mes actual)
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Tabla resumen -->
            <div class="col-md-6">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Programa</th>
                            <th>Cantidad</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matriculasPorPrograma as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['programa_nombre']) ?></td>
                                <td><?= htmlspecialchars($item['total_matriculas']) ?></td>
                                <td>$<?= number_format((float)$item['total_valor'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Gráfico -->
            <div class="col-md-6">
                <canvas id="matriculasPorProgramaChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Cargar Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const dataPorPrograma = <?= json_encode($matriculasPorPrograma) ?>;
    console.log("📊 Datos para el gráfico:", dataPorPrograma);

    const labels = <?= json_encode(array_column($matriculasPorPrograma, 'programa_nombre')) ?>;
    const data = <?= json_encode(array_column($matriculasPorPrograma, 'total_matriculas')) ?>;

    const ctx = document.getElementById('matriculasPorProgramaChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Cantidad',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
</script>
