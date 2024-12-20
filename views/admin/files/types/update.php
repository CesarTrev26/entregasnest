<?php incluirTemplate('Admin-navbar'); ?>

<main class="admin-content">
    <section class="admin-section">
        <h1>Editar Tipo de plano</h1>

        <div class="actions-div">
            <a class="blue-btn" href="/admin/files/plantypes">Volver a tipos de archivos</a>
        </div>

        <?php if (!empty($errors)): ?>
            <ul style="color: red;">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="form-div">
            <!-- Ensure the correct 'action' attribute for form submission -->
            <form method="post" action="/admin/files/plantypes/update/<?= htmlspecialchars($planType->id ?? ''); ?>">

                <div class="form-group">
                    <label for="keyword">Palabra Clave del plano:</label>
                    <input type="text" name="keyword" id="keyword" value="<?= htmlspecialchars($planType->keyword ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Descripción:</label>
                    <input type="text" name="description" id="description" value="<?= htmlspecialchars($planType->description ?? ''); ?>" required>
                </div>

                <select name="assigned_button" id="assigned_button" required>
                    <option value="">Seleccione el botón asignado</option>
                    <option value="Departamento" <?php echo $planType->assigned_button === 'Departamento' ? 'selected' : ''; ?>>Departamento</option>
                    <option value="Estacionamiento" <?php echo $planType->assigned_button === 'Estacionamiento' ? 'selected' : ''; ?>>Estacionamiento</option>
                    <option value="Utilidad" <?php echo $planType->assigned_button === 'Utilidad' ? 'selected' : ''; ?>>Utilidad</option>
                    <option value="Manual" <?php echo $planType->assigned_button === 'Manual' ? 'selected' : ''; ?>>Manual del Proyecto</option>
                    <option value="Videos" <?php echo $planType->assigned_button === 'Videos' ? 'selected' : ''; ?>>Video del Proyecto</option>
                </select>

                <button class="gray-admin-btn" type="submit">Actualizar</button>
            </form>
        </div>
    </section>
</main>
