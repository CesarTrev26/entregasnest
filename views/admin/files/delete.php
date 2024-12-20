<?php incluirTemplate('Admin-navbar'); ?>

<main class="admin-content">
    <section class="admin-section">
        <h1>Eliminar Archivos</h1>

        <!-- Display success or error message -->
        <?php if (!empty($success)) : ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php endif; ?>

        <?php if (!empty($errors)) : ?>
            <ul style="color: red;">
                <?php foreach ($errors as $error) : ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="files-list">
            <h2>Archivos Subidos</h2>
            <form action="/admin/files/delete" method="POST" id="delete-form">
                <table>
                    <thead>
                        <tr>
                            <th>Seleccionar</th>
                            <th>Nombre de Archivo</th>
                            <th>Tipo de Plan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($files as $file) : ?>
                            <tr>
                                <td>
                                    <input type="checkbox" style="width: auto;" name="files[]" value="<?php echo htmlspecialchars($file['id']); ?>">
                                </td>
                                <td><?php echo htmlspecialchars($file['file_name']); ?></td>
                                <td><?php echo htmlspecialchars($file['plan_type']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" class="delete-btn" onclick="return confirm('¿Está seguro de que desea eliminar los archivos seleccionados?');">Eliminar Seleccionados</button>
            </form>
        </div>
    </section>
</main>
