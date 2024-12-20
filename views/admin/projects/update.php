<?php incluirTemplate('Admin-navbar'); ?>

<main class="admin-content">
    <section class="admin-section">
        <h1>Actualizar Proyecto</h1>
        
        <?php if (!empty($errors)): ?>
            <ul style="color: red;">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="form-div">
            <form method="post" id="updateProjectForm" action="/admin/projects/update?id=<?php echo htmlspecialchars($project->id ?? ''); ?>">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($project->id ?? ''); ?>">

                <label for="project_name">Nombre del Proyecto:</label>
                <input type="text" name="project_name" id="project_name" value="<?php echo htmlspecialchars($project->project_name ?? ''); ?>" required>
                
                <label for="location">Ubicación:</label>
                <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($project->location ?? ''); ?>" required>
                
                <div class="form-group">
                    <label for="plans">Seleccione los planos:</label>
                    <div id="plans" style="font-size: 2vmin;">
                        <?php
                        // Assuming $plans is an array containing the plans fetched from the database
                        // Group plans by their 'plan_type'
                        $plan_types = []; // Array to group plans by plan_type
                        foreach ($plans as $plan) {
                            // Check if the plan_type is not 'Manual' or 'Videos'
                            if (in_array($plan['plan_type'], ['Manual', 'Videos'])) {
                                // Group the plans by their plan_type
                                $plan_types[$plan['plan_type']][] = $plan;
                            }
                        }

                        // Create radio buttons for each plan_type
                        foreach ($plan_types as $type => $type_plans) {
                            echo "<fieldset style='overflow-y:scroll;max-height:15vmin'>";
                            echo "<legend>" . htmlspecialchars($type) . "</legend>";
                            
                            // Add "None" option for unselecting
                            echo '<label style="display:flex;justify-content:flex-start;align-items:center;">';
                            echo "<input type='radio' name='plans[" . htmlspecialchars($type) . "]' value='' style='width:auto; margin-right:3px; height:15px; width: 15px;' " . (!isset($selectedPlans[$type]) ? "checked" : "") . ">";
                            echo "Ninguno (No seleccionar)";
                            echo "</label><br>";

                            foreach ($type_plans as $plan) {
                                // Determine if this plan should be checked based on previous selections
                                $checked = isset($selectedPlans[$plan['id']]) ? "checked" : "";
                                
                                echo '<label style="display:flex;justify-content:flex-start;align-items:center;">';
                                echo "<input type='radio' name='plans[" . htmlspecialchars($type) . "]' value='" . htmlspecialchars($plan['id']) . "' style='width:auto; margin-right:3px; height:15px; width: 15px;' $checked>";
                                echo htmlspecialchars($plan['file_name']);
                                echo "</label><br>";
                            }
                            echo "</fieldset>";
                        }
                        ?>
                    </div>
                </div>

                <button class="gray-admin-btn" type="submit">Actualizar Proyecto</button>
            </form>
        </div>
    </section>
</main>
