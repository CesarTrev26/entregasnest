<?php incluirTemplate('SEO'); ?>

<?php incluirTemplate('Admin-navbar'); ?>

<!-- views/admin/users/update.php -->
<main class="admin-content">
    <section class="admin-section">
        <h1>Editar Usuario</h1>
        <div class="form-div">
            <form id="updateUserForm" action="/admin/users/update" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user->id); ?>">
                <div>
                    <label for="full_name">Nombre Completo:</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user->full_name); ?>" required>
                </div>
                
                <div>
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user->email); ?>" required>
                </div>
                
                <div>
                    <label for="phone">Teléfono:</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user->phone); ?>" required>
                </div>

                <div>
                    <label for="password">Nueva Contraseña:</label>
                    <input type="password" id="password" name="password">
                    <small>Deja este campo vacío si no deseas cambiar la contraseña.</small>
                </div>

                <div>
                    <label for="rol_id">Rol:</label>
                    <select name="rol_id" id="rol_id">
                        <option value="">Seleccionar rol</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo htmlspecialchars($role['id']); ?>" 
                                    <?php echo ($user->rol_id == $role['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($role['rol']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="gray-admin-btn" type="submit">Actualizar</button>
            </form>
        </div>
        
    </section>
</main>

