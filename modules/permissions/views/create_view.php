<h1 class="mt-5">Crear Permiso</h1>
<form action="/createpermission/" method="POST">
    <div class="form-group">
        <label for="permission_name">Nombre del Permiso:</label>
        <input type="text" id="permission_name" name="permission_name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="permission_description">Descripción del Permiso:</label>
        <input type="text" id="permission_description" name="permission_description" class="form-control" required>
    </div>
    <br>
    <button type="submit" class="btn btn-primary">Crear Permiso</button>
</form>