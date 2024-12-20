<?php incluirTemplate('SEO'); ?>

<?php incluirTemplate('Admin-navbar'); ?>

<main class="admin-content">
    <section class="admin-section">
        <h1>Crear Nuevo Usuario</h1>

        <?php if (!empty($errors)) : ?>
            <ul style="color: red;">
                <?php foreach ($errors as $error) : ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <div class="form-div">
            <form id="createUserForm" action="/admin/users/create" method="POST">
                <div>
                    <label for="full_name">Nombre Completo:</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>

                <div>
                <!-- Rol dropdown -->
                    <label for="rol_id">Rol:</label>
                    <select name="rol_id" id="rol_id">
                        <option value="">Selecciona un rol</option>
                        <?php if (!empty($roles)): ?>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?php echo htmlspecialchars($rol['id']); ?>" 
                                        <?php echo (isset($user->rol_id) && $user->rol_id == $rol['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($rol['rol']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div>
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div>
                    <label for="phone">Teléfono:</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                
                <div>
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button class="gray-admin-btn" type="submit">Crear</button>
            </form>
        </div>
    </section

    <?php incluirTemplate('scripts'); ?>