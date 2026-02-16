<h2><?php echo $titulo; ?></h2>
<form action="/roles/update/<?php echo $role['id']; ?>" method="post">
    <div class="mb-3">
        <label for="name" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo $role['name']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Descripción</label>
        <textarea class="form-control" id="description" name="description"><?php echo $role['description']; ?></textarea>
    </div>
    <div class="mb-3">
        <label for="menu" class="form-label">Menu</label>
        <input type="text" class="form-control" id="menu" name="menu" value="<?php echo $role['menu']; ?>">
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="/roles/" class="btn btn-secondary">Regresar al listado</a>
</form>
<br>
