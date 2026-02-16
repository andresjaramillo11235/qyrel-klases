<h2><?php echo $titulo; ?></h2>
<form action="/roles/store" method="post">
    <div class="mb-3">
        <label for="name" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Descripción</label>
        <textarea class="form-control" id="description" name="description"></textarea>
    </div>
    <div class="mb-3">
        <label for="menu" class="form-label">Menu</label>
        <input type="text" class="form-control" id="menu" name="menu">
    </div>
    <button type="submit" class="btn btn-primary">Crear</button>
    <a href="/roles/" class="btn btn-secondary">Regresar al listado</a>
</form>
<br>
