<h2><?php echo $titulo; ?></h2>
<div class="mb-3">
    <label class="form-label">ID:</label>
    <p class="form-control"><?php echo $role['id']; ?></p>
</div>
<div class="mb-3">
    <label class="form-label">Nombre:</label>
    <p class="form-control"><?php echo $role['name']; ?></p>
</div>
<div class="mb-3">
    <label class="form-label">Descripción:</label>
    <p class="form-control"><?php echo $role['description']; ?></p>
</div>
<div class="mb-3">
    <label class="form-label">Menu:</label>
    <p class="form-control"><?php echo $role['menu']; ?></p>
</div>
<a href="/roles/" class="btn btn-secondary">Regresar al listado</a>
<br>
