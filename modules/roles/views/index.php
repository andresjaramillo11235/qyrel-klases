<h2><?php echo $titulo; ?></h2>
<a href="/roles/create" class="btn btn-primary">Crear Rol</a>
<table class="table mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Menu</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($roles as $role): ?>
            <tr>
                <td><?php echo $role['id']; ?></td>
                <td><?php echo $role['name']; ?></td>
                <td><?php echo $role['description']; ?></td>
                <td><?php echo $role['menu']; ?></td>
                <td>
                    <a href="/roles/detail/<?php echo $role['id']; ?>" class="btn btn-info btn-sm">Detalle</a>
                    <a href="/roles/edit/<?php echo $role['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="/roles/delete/<?php echo $role['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este rol?');">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
