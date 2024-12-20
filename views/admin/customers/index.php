<?php incluirTemplate('Admin-navbar'); ?>

<main class="admin-content">
    <section class="admin-section">
        <?php if (!empty($success)) : ?>
            <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <div class="actions-div">
            <a href="/admin/customers/create">Crear Asignación</a>
        </div>
        <div>
            <form action="/admin/customers" method="GET" style="margin-bottom: 20px;">
                <input type="text" name="search" placeholder="Buscar por nombre" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                <button class="gray-admin-btn" type="submit">Buscar</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Usuario</th>
                        <th>Nombre del Proyecto</th>
                        <th>Nombre del Departamento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customer_projects as $customer_project): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($customer_project['id']); ?></td>
                        <td><?php echo htmlspecialchars($customer_project['full_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($customer_project['project_name']); ?></td>
                        <td><?php echo htmlspecialchars($customer_project['department_name'] ?? ''); ?></td> <!-- Updated to show department_name -->
                        <td class="actions">
                            <a href="/admin/customers/update?id=<?php echo htmlspecialchars($customer_project['id']); ?>">Editar</a>
                            <form action="/admin/customers/delete" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($customer_project['id']); ?>">
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
    return confirm("¿Estás seguro de que deseas eliminar este registro?");
}
</script>
