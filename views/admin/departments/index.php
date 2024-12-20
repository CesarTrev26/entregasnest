<?php incluirTemplate('Admin-navbar'); ?>

<main class="admin-content">
    <section class="admin-section">
        <?php if (!empty($success)) : ?>
            <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <div class="actions-div">
            <a href="/admin/departments/create">Crear Departamento</a>
            <a href="/admin/files/upload">Subir Archivo</a>
            <a class="red-btn" href="/admin/files/delete">Borrar Archivo</a>
        </div>
        <div>
            <form action="/admin/departments" method="GET" style="margin-bottom: 20px;">
                <input type="text" name="search" placeholder="Buscar por nombre" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                <button class="gray-admin-btn" type="submit">Buscar</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Proyecto</th>
                        <th>Torre del Proyecto</th> <!-- Added column for project_tower -->
                        <th>Nombre del Departamento</th>
                        <th>Sótano</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departments as $department): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($department['id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($department['project_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($department['project_tower'] ?? ''); ?></td> <!-- Added project_tower -->
                        <td><?php echo htmlspecialchars($department['department_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($department['department_basement'] ?? ''); ?></td>
                        <td class="actions">
                            <a href="/admin/departments/update?id=<?php echo htmlspecialchars($department['id'] ?? ''); ?>">Editar</a>
                            <form action="/admin/departments/delete" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($department['id'] ?? ''); ?>">
                                <button type="submit" onclick="return confirmDeletion()">Borrar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
function confirmDeletion() {
    return confirm("¿Estás seguro de que deseas eliminar este departamento?");
}
</script>
