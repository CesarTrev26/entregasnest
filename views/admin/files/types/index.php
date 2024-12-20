<?php incluirTemplate('Admin-navbar'); ?>

<main class="admin-content">
    <section class="admin-section">
        <?php if (!empty($success)) : ?>
            <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <ul style="color: red;">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <div class="actions-div">
            <a href="/admin/files/plantypes/create">Agregar tipo de plano</a>
            <a class="blue-btn" href="/admin/files/upload">Volver a subida de archivos</a>
        </div>
        <div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Palabra Clave</th>
                        <th>Descripción / Nombre del plano</th>
                        <th>Botón Asignado</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($planTypes as $planType): ?>
                    <tr>
                        <td><?= htmlspecialchars($planType->id ?? ''); ?></td>
                        <td><?= htmlspecialchars($planType->keyword ?? ''); ?></td>
                        <td><?= htmlspecialchars($planType->description ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($planType->assigned_button); ?></td>
                        <td class="actions">
                            <a href="/admin/files/plantypes/update/<?php echo htmlspecialchars($planType->id ?? ''); ?>">Editar</a>
                            <form action="/admin/files/plantypes/delete" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($planType->id ?? ''); ?>">
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
    return confirm("¿Estás seguro de que deseas eliminar este tipo de plano?");
}
</script>
