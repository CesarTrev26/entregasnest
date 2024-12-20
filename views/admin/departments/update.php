<?php incluirTemplate('Admin-navbar'); ?>

<main class="admin-content">
    <section class="admin-section">
        <h1>Actualizar Departamento</h1>

        <?php if (!empty($errors)): ?>
            <ul style="color: red;">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div style="color: green;">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>

        <div class="form-div">
            <form id="updateUserForm" method="post" action="/admin/departments/update?id=<?php echo htmlspecialchars($department->id ?? ''); ?>">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($department->id ?? ''); ?>">

                <div class="form-group">
                    <label for="project_id">Proyecto:</label>
                    <select name="project_id" id="project_id" required>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?php echo htmlspecialchars($project['id']); ?>" 
                                <?php echo $project['id'] == ($department->project_id ?? '') ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($project['project_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="project_tower">Torre del Proyecto:</label>
                    <input type="text" name="project_tower" id="project_tower" value="<?php echo htmlspecialchars($department->project_tower ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="department_name">Nombre del Departamento:</label>
                    <input type="text" name="department_name" id="department_name" value="<?php echo htmlspecialchars($department->department_name ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="department_basement">Sótano:</label>
                    <input type="text" name="department_basement" id="department_basement" value="<?php echo htmlspecialchars($department->department_basement ?? ''); ?>" required>
                </div>

                <button class="gray-admin-btn" type="submit">Actualizar Departamento</button>

                <div class="form-group" style="grid-column: 1/3;">
                    <label for="plans">Seleccione los planos</label>
                    <div id="plans" style="font-size: 2vmin;display: flex;flex-wrap: wrap;justify-content: space-between;">
                        <?php
                        // Group plans by 'plan_type'
                        $plan_types = []; // Array to group plans by plan_type
                        foreach ($plans as $plan) {
                            // Check if the plan_type is not 'Manual' or 'Videos'
                            if (!in_array($plan['plan_type'], ['Manual', 'Videos'])) {
                                // Group the plans by their plan_type
                                $plan_types[$plan['plan_type']][] = $plan;
                            }
                        } 

                        foreach ($plan_types as $type => $type_plans) {
                            echo "<fieldset style='overflow-y:scroll;max-height:20vmin'>";
                            echo "<legend>" . htmlspecialchars($type) . "</legend>";
                            
                            // "None" option for unselecting
                            echo '<label style="display:flex;justify-content:flex-start;align-items:center;">';
                            echo "<input type='radio' name='plans[" . htmlspecialchars($type) . "]' value='' 
                                " . (!array_key_exists($type, $selectedPlans) ? 'checked' : '') . 
                                " style='width:auto; margin-right:3px; height:15px; width: 15px;'>";
                            echo "Ninguno (No seleccionar)";
                            echo "</label><br>";

                            foreach ($type_plans as $plan) {
                                // Check if the current plan's file name is in selected plans
                                $isChecked = (array_search($plan['file_name'], $selectedPlans) !== false) ? 'checked' : '';
                                echo '<label style="display:flex;justify-content:flex-start;align-items:center;">';
                                echo "<input type='radio' name='plans[" . htmlspecialchars($type) . "]' value='" . htmlspecialchars($plan['id']) . "' " . $isChecked . " style='width:auto; margin-right:3px; height:15px; width: 15px;'>";
                                echo htmlspecialchars($plan['file_name']);
                                echo "</label><br>";
                            }
                            echo "</fieldset>";
                        }
                        ?>
                    </div>
                </div>
                <button class="gray-admin-btn" type="submit">Actualizar Departamento</button>
            </form>
        </div>
    </section>
</main>
