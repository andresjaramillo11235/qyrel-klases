<h1 class="my-4">Asignar Permiso a Rol</h1>
<form action="/rolepermissionsstore/" method="POST">
    <div class="form-group">
        <label for="role_id">Roles:</label>
        <select class="form-control" name="role_id" id="role_id">
            <?php foreach ($roles as $role) : ?>
                <option value="<?php echo $role['id']; ?>"><?php echo $role['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="permission_id">Permisos:</label>
        <select class="form-control" name="permission_id" id="permission_id">
            <?php foreach ($permissions as $permission) : ?>
                <option value="<?php echo $permission['id']; ?>"><?php echo $permission['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Asignar Permiso</button>
</form>