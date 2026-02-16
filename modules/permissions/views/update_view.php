<h1 class="mt-5">Editar Permiso</h1>
<form action="/updatepermission/" method="POST">
    <input type="hidden" name="id" value="<?php echo $permission['id']; ?>">
    <div class="form-group">
        <label for="permission_name">Nombre del Permiso:</label>
        <input type="text" id="permission_name" name="permission_name" class="form-control" value="<?php echo $permission['name']; ?>" required>
    </div>
    <div class="form-group">
        <label for="permission_description">Descripción del Permiso:</label>
        <input type="text" id="permission_description" name="permission_description" class="form-control" value="<?php echo $permission['description']; ?>" required>
    </div>
    <br>
    <button type="submit" class="btn btn-primary">Actualizar Permiso</button>
</form>