<?php
use Model\Projects;
?>


<main>
    <div class="info-container">
        <div class="project-nav">
            <div class="back-arrow">
                <svg onclick="history.back()" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" viewBox="0 0 32 32" enable-background="new 0 0 32 32" width="32px" xml:space="preserve">
                    <line fill="none" stroke="#000000" stroke-width="1" stroke-miterlimit="10" x1="6" y1="16" x2="28" y2="16"/>
                    <polyline fill="none" stroke="#000000" stroke-width="1" stroke-miterlimit="10" points="14,24.5 5.5,16 14,7.5 "/>
                </svg>
            </div>
            <div class="help-div">
                <a href="/proyectos/ayuda">Crear solicitud
                    <img src="/build/img/help.svg" alt="">
                </a>
            </div>
        </div>
        
        <div class="user-data">
            <div>
                <p><b>Perfil de usuario</b></p>
                <?php if ($userData): ?>
                    <p><?php echo htmlspecialchars($userData['full_name']) ?> <br> <span>/</span> <?php echo htmlspecialchars($userData['email']) ?></p>
                <?php else: ?>
                    <p>Información del usuario no disponible</p>
                <?php endif; ?>
            </div>
            <?php if ($auth): ?>
                <div>
                    <a class="logout-button" href="/cerrar-sesion">Cerrar Sesión</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Display department details -->
        <?php if (!empty($departments)): ?>
            <div class="departments-container">
                <?php foreach ($departments as $department): ?>
                    <?php 
                        $plans = Projects::fetchDepartmentPlans($department['id']); 
                        $plansByButton = [
                            'Departamento' => [],
                            'Estacionamiento' => [],
                        ];

                        // Group plans by assigned_button
                        foreach ($plans as $plan) {
                            $button = $plan['assigned_button'] ?? 'Unknown';
                            if (isset($plansByButton[$button])) {
                                $plansByButton[$button][] = $plan;
                            }
                        }
                    ?>
                    <div class="department-div">
                        <?php if (!empty($department['project_tower'])): ?>
                            <h2><?php echo htmlspecialchars($department['project_tower']); ?></h2>
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($department['department_name']); ?></h3>
                    </div>

                    <div class="department-buttons-div">
                        <!-- Departamento Button -->
                        <div class="button-container">
                            <button type="button" class="department-button" onclick="showFileButtons(this)">Información de tu departamento</button>
                            <div class="button-div">
                                <?php if (!empty($plansByButton['Departamento'])): ?>
                                    <?php foreach ($plansByButton['Departamento'] as $plan): ?>
                                        <a class="file-button file-color-btn" href="<?php echo htmlspecialchars($plan['file_path']); ?>" target="_blank">
                                            <?php echo htmlspecialchars($plan['plan_type']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No hay planos disponibles para este departamento.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Estacionamiento Button -->
                        <div class="button-container">
                            <button type="button" class="department-button" onclick="showFileButtons(this)">Estacionamiento</button>
                            <div class="button-div">
                                <?php if (!empty($plansByButton['Estacionamiento'])): ?>
                                    <?php foreach ($plansByButton['Estacionamiento'] as $plan): ?>
                                        <a class="file-button file-color-btn" href="<?php echo htmlspecialchars($plan['file_path']); ?>" target="_blank">
                                            <?php echo htmlspecialchars($plan['plan_type']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No hay planos disponibles para este departamento.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Utility Button -->
                <div class="department-buttons-div utility">
                    <div class="button-container">
                        <button type="button" class="department-button" onclick="showFileButtons(this)">Documentos de utilidad</button>
                        <div class="button-div">
                            <?php 
                            $utilityPlans = [];
                            foreach ($departments as $department) {
                                $plans = Projects::fetchDepartmentPlans($department['id']);
                                foreach ($plans as $plan) {
                                    if ($plan['assigned_button'] === 'Utilidad') {
                                        $utilityPlans[] = $plan;
                                    }
                                }
                            }
                            ?>

                            <?php if (!empty($utilityPlans)): ?>
                                <?php foreach ($utilityPlans as $plan): ?>
                                    <a class="file-button utility-button" href="<?php echo htmlspecialchars($plan['file_path']); ?>" target="_blank">
                                        <?php echo htmlspecialchars($plan['plan_type']); ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No hay documentos de utilidad disponibles.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p>No hay departamentos asociados a este proyecto.</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    <a class="nest-footer"><img src="../build/img/NestLogo.webp" alt=""></a>
    <div class="help-div">
        <a href="/proyectos/ayuda">Crear solicitud
            <img src="/build/img/help.svg" alt="">
        </a>
    </div>
</footer>