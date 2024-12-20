<?php incluirTemplate('Admin-navbar'); ?>

<?php
// Debug output for troubleshooting
/*var_dump($users);
var_dump($projects);
var_dump($departments);
var_dump($customer_project);*/
?>

<main class="admin-content">
    <section class="admin-section">
        <h1>Actualizar Asignación</h1>
        
        <?php if (!empty($errors)): ?>
            <ul style="color: red;">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <div class="form-div">
            <form id="updateUserForm" method="POST" action="">
                <div>
                    <label for="user_id">Usuario:</label>
                    <select id="user_id" name="user_id">
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo htmlspecialchars($user['id']); ?>" 
                                    <?php echo ($customer_project->user_id == $user['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($user['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="project_id">Proyecto:</label>
                    <select id="project_id" name="project_id">
                        <?php foreach ($projects as $project): ?>
                            <option value="<?php echo htmlspecialchars($project['id']); ?>" 
                                    <?php echo ($customer_project->project_id == $project['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($project['project_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="tower_name">Torre:</label>
                    <select id="tower_name" name="tower_name">
                        <!-- Populate with towers based on the selected project -->
                    </select>
                </div>
                
                <div>
                    <label for="department_id">Departmentos:</label>
                    <select id="department_id" name="department_id">
                        <option value="">Seleccionar Departamento</option>
                        <!-- Populate with departments based on the selected project and tower -->
                    </select>
                </div>
                
                <button class="gray-admin-btn" type="submit" value="Update">Actualizar Registro</button>
            </form>
        </div>
    </section>
</main>

<script>
    // Event listener to update towers dropdown based on selected project
    document.getElementById('project_id').addEventListener('change', function() {
        const projectId = this.value;
        const towerSelect = document.getElementById('tower_name');
        
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `/admin/customers/getTowers?project_id=${projectId}`, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    const towers = response.towers;
                    towerSelect.innerHTML = '<option value="">Seleccionar Torre</option>';
                    towers.forEach(tower => {
                        towerSelect.innerHTML += `<option value="${tower.tower_name}">${tower.tower_name}</option>`;
                    });
                    // Trigger change event to populate departments based on new project and tower
                    towerSelect.dispatchEvent(new Event('change'));
                } catch (e) {
                    console.error('Failed to parse JSON response:', e);
                }
            } else {
                console.error('Request failed. Returned status of ' + xhr.status);
            }
        };
        xhr.send();
    });
    
    // Event listener to update departments dropdown based on selected tower
    document.getElementById('tower_name').addEventListener('change', function() {
        const projectId = document.getElementById('project_id').value;
        const towerName = this.value;
        const departmentSelect = document.getElementById('department_id');
        
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `/admin/customers/getDepartments?tower_name=${encodeURIComponent(towerName)}&project_id=${projectId}`, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    const departments = response.departments;
                    departmentSelect.innerHTML = '<option value="">Seleccionar Departamento</option>';
                    departments.forEach(department => {
                        departmentSelect.innerHTML += `<option value="${department.id}">${department.department_name}</option>`;
                    });
                } catch (e) {
                    console.error('Failed to parse JSON response:', e);
                }
            } else {
                console.error('Request failed. Returned status of ' + xhr.status);
            }
        };
        xhr.send();
    });
    
    // Trigger change event to populate towers and departments based on pre-selected values
    document.addEventListener('DOMContentLoaded', function() {
        const projectId = document.getElementById('project_id').value;
        if (projectId) {
            document.getElementById('project_id').dispatchEvent(new Event('change'));
        }
        const towerName = document.getElementById('tower_name').value;
        if (towerName) {
            document.getElementById('tower_name').dispatchEvent(new Event('change'));
        }
    });
</script>
