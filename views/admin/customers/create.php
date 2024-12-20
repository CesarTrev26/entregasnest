<?php incluirTemplate('Admin-navbar'); ?>

<main class="admin-content">
    <section class="admin-section">
        <h1>Crear Asignación</h1>
        
        <?php if (!empty($errors)): ?>
            <ul style="color: red;">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="form-div">
            <form method="POST" action="/admin/customers/create">
                <label for="user_id">Usuario:</label>
                <select name="user_id" id="user_id" required>
                    <option value="">Seleccionar Usuario</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['id']); ?>">
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="project_id">Proyecto:</label>
                <select name="project_id" id="project_id" required>
                    <option value="">Seleccionar Proyecto</option>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?php echo htmlspecialchars($project['id']); ?>">
                            <?php echo htmlspecialchars($project['project_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="tower_id">Torre:</label>
                <select name="tower_id" id="tower_id">
                    <option value="">Seleccionar Torre</option>
                </select>

                <label for="department_id">Departamento:</label>
                <select name="department_id" id="department_id">
                    <option value="">Seleccionar Departamento</option>
                </select>

                <button class="gray-admin-btn" type="submit">Crear Proyecto</button>
            </form>
        </div>
    </section>
</main>


<script>
    // This function will handle the preselection of the tower if it's set in the $_POST data
    document.addEventListener('DOMContentLoaded', function() {
        const towerSelect = document.getElementById('tower_id');
        const preSelectedTower = "<?php echo isset($_POST['tower_id']) ? $_POST['tower_id'] : ''; ?>"; // Fetch the preselected value

        // If there's a preselected tower, set it
        if (preSelectedTower) {
            towerSelect.value = preSelectedTower; // Set the preselected tower if it exists
        }
    });

    document.getElementById('project_id').addEventListener('change', function() {
        const projectId = this.value;
        const towerSelect = document.getElementById('tower_id');
        towerSelect.innerHTML = '<option value="">Seleccionar Torre</option>'; // Reset tower dropdown

        if (projectId) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `/admin/customers/getTowers?project_id=${projectId}`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        const towers = response.towers;
                        
                        // Populate tower dropdown options
                        towers.forEach(tower => {
                            towerSelect.innerHTML += `<option value="${tower.tower_name}">${tower.tower_name}</option>`;
                        });

                        // If there's a preselected tower, make sure it is still selected after the fetch
                        const preSelectedTower = "<?php echo isset($_POST['tower_id']) ? $_POST['tower_id'] : ''; ?>";
                        if (preSelectedTower) {
                            towerSelect.value = preSelectedTower;
                        }

                    } catch (e) {
                        console.error('Failed to parse JSON response:', e);
                    }
                } else {
                    console.error('Request failed. Status:', xhr.status);
                }
            };
            xhr.send();
        }
    });

    document.getElementById('tower_id').addEventListener('change', function() {
        const towerName = this.options[this.selectedIndex].text; // Get the selected tower name
        const departmentSelect = document.getElementById('department_id');
        departmentSelect.innerHTML = '<option value="">Seleccionar Departamento</option>'; // Reset department dropdown

        if (towerName) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `/admin/customers/getDepartments?tower_name=${encodeURIComponent(towerName)}`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            console.error('Error:', response.error);
                        } else {
                            const departments = response.departments;
                            console.log(departments); // Log departments for debugging
                            departments.forEach(department => {
                                departmentSelect.innerHTML += `<option value="${department.id}">${department.department_name}</option>`;
                            });
                        }
                    } catch (e) {
                        console.error('Failed to parse JSON response:', e);
                    }
                } else {
                    console.error('Request failed. Status:', xhr.status);
                }
            };
            xhr.send();
        }
    });
</script>

