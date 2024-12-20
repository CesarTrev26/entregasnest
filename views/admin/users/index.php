<?php incluirTemplate('Admin-navbar'); ?>

<main class="admin-content">
    <section class="admin-section">
    <?php if (!empty($success)) : ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
        <div class="actions-div">
            <a href="/admin/users/create">Crear Usuario</a>
        </div>
        <div>
        <form action="/admin/users" method="GET" style="margin-bottom: 20px;">
                <input type="text" name="search" placeholder="Buscar por nombre o email" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                <button class="gray-admin-btn" type="submit">Buscar</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Rol</th> <!-- New column for roles -->
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user->id); ?></td>
                        <td><?php echo htmlspecialchars($user->full_name); ?></td>
                        <td><?php echo htmlspecialchars($user->email); ?></td>
                        <td><?php echo htmlspecialchars($user->phone); ?></td>
                        <td>
                            <?php
                            // Find the role name by role ID (assuming roles are available)
                            $roleName = '';
                            foreach ($roles as $role) {
                                if ($role['id'] == $user->rol_id) {
                                    $roleName = htmlspecialchars($role['rol']);
                                    break;
                                }
                            }
                            echo $roleName;
                            ?>
                        </td>
                        <td class="actions">
                            <a href="/admin/users/update?id=<?php echo $user->id; ?>">Editar</a>
                            <form action="/admin/users/delete" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user->id); ?>">
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
    return confirm("¿Estás seguro de que deseas eliminar este usuario?");
}
</script>
