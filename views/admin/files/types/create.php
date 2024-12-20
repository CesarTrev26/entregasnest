<?php incluirTemplate('Admin-navbar'); ?>

<main class="admin-content">
    <section class="admin-section">
        <h1>Agregar Tipo de plano</h1>

        <div class="actions-div">
            <a class="blue-btn" href="/admin/files/plantypes">Volver a tipo de planos</a>
            <a class="blue-btn" href="/admin/files/upload">Volver a subida de archivos</a>
        </div>

        <?php if (!empty($errors)): ?>
            <ul style="color: red;">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="form-div">
            <form method="post" action="/admin/files/plantypes/create">
                <div class="form-group">
                    <label for="keyword">Palabra Clave del plano:</label>
                    <input type="text" name="keyword" id="keyword" value="<?php echo htmlspecialchars($planType->keyword ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Descripción / Nombre del plano:</label>
                    <input type="text" name="description" id="description" value="<?php echo htmlspecialchars($planType->description ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="assigned_button">Botón asignado</label>
                    <select name="assigned_button" id="assigned_button" required>
                        <option value="">Seleccione el botón asignado</option>
                        <option value="Departamento">Departamento</option>
                        <option value="Estacionamiento">Estacionamiento</option>
                        <option value="Utilidad">Utilidad</option>
                        <option value="Manual">Manual del Proyecto</option>
                        <option value="Videos">Video del Proyecto</option>
                    </select>
                </div>

                <button class="gray-admin-btn" type="submit">Agregar</button>
            </form>
        </div>
    </section>
</main>
