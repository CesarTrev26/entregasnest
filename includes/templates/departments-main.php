<main>
    <div class="info-container">
        <div class="back-arrow">
            <svg onclick="history.back()" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" viewBox="0 0 32 32" enable-background="new 0 0 32 32" width="32px" xml:space="preserve">
                <line fill="none" stroke="#000000" stroke-width="1" stroke-miterlimit="10" x1="6" y1="16" x2="28" y2="16"/>
                <polyline fill="none" stroke="#000000" stroke-width="1" stroke-miterlimit="10" points="14,24.5 5.5,16 14,7.5 "/>
            </svg>
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
                    <div class="department-div">
                    <?php if (!empty($department['project_tower'])): ?>
                        <h2><?php echo htmlspecialchars($department['project_tower']); ?></h2>
                    <?php endif; ?>
                        <h3><?php echo htmlspecialchars($department['department_name']); ?></h3>
                        <!-- Display other department details here -->
                    </div>
                
                    <div class="department-buttons-div">
                        <div class="services-container">
                            <button type="button" class="department-button" onclick="showServicesButtons(this)">Planos de instalaciones
                                <svg id="SvgjsSvg1033" width="25" height="25" style="position: absolute;top: 10px;margin-left: 5px;" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs">
                                    <defs id="SvgjsDefs1034"></defs>
                                    <g id="SvgjsG1035">
                                        <svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 128 128" width="25" height="25">
                                            <path d="M31.89 12.25H85.67v-5H29.39a2.50091 2.50091 0 0 0-2.5 2.5V54.49h5zM113.09 25.76C113.02 25.65 95.77 7.83 95.67 7.75a2.53339 2.53339 0 0 0-1.51-.5H88.17V30.76a2.50091 2.50091 0 0 0 2.5 2.5h22.87V27.19A2.50047 2.50047 0 0 0 113.09 25.76z" fill="#000000" class="color000 svgShape"></path>
                                            <path fill="#000000" d="M30.42 71.22c-.14 0-.28 0-.4.01v3.39h.17c2.42 0 2.42-1.34 2.42-1.84C32.61 72.27 32.61 71.22 30.42 71.22zM46.03 71.15q-.495 0-.87006.03v9.63h.54c3.47 0 5.16-1.68 5.16-5.13a4.4842 4.4842 0 0 0-1.1-3.32A5.09946 5.09946 0 0 0 46.03 71.15z" class="color1188c6 svgShape"></path>
                                            <path fill="#000000" d="M80.61,92.5V59.49a2.50091,2.50091,0,0,0-2.5-2.5H16.96a2.50091,2.50091,0,0,0-2.5,2.5V92.5a2.50091,2.50091,0,0,0,2.5,2.5H78.11A2.50091,2.50091,0,0,0,80.61,92.5ZM36,77.07a8.11216,8.11216,0,0,1-5.98,2.18v4.62a1.498,1.498,0,0,1-1.5,1.5H26.5a1.498,1.498,0,0,1-1.5-1.5V68.42a1.50261,1.50261,0,0,1,1.25-1.48,25.59892,25.59892,0,0,1,4.12-.32,7.85477,7.85477,0,0,1,5.39,1.68,5.68533,5.68533,0,0,1,1.87,4.39A6.036,6.036,0,0,1,36,77.07Zm17.27,5.69c-1.75,1.75-4.5,2.68-7.96,2.68a34.13746,34.13746,0,0,1-3.83-.2,1.49648,1.49648,0,0,1-1.34-1.49V68.37a1.50433,1.50433,0,0,1,1.28-1.48,31.255,31.255,0,0,1,4.52-.34c3.25,0,5.65.77,7.35,2.35a8.68537,8.68537,0,0,1,2.68,6.71A9.73161,9.73161,0,0,1,53.27,82.76Zm16.8-12.89a1.498,1.498,0,0,1-1.5,1.5h-4.9v2.2h4.41a1.498,1.498,0,0,1,1.5,1.5v1.67a1.50443,1.50443,0,0,1-1.5,1.5H63.67v5.57a1.498,1.498,0,0,1-1.5,1.5H60.15a1.498,1.498,0,0,1-1.5-1.5V68.18a1.498,1.498,0,0,1,1.5-1.5h8.42a1.498,1.498,0,0,1,1.5,1.5Z" class="color1188c6 svgShape"></path>
                                            <path d="M108.54,115.75H31.89V97.5h-5v20.75a2.50091,2.50091,0,0,0,2.5,2.5h81.65a2.49451,2.49451,0,0,0,2.5-2.5V35.76h-5Z" fill="#000000" class="color000 svgShape"></path>
                                        </svg>
                                    </g>
                                </svg>
                            </button>
                            <div class="services-div">
                                <a class="services-button" target="_blank" >Planos de Departamento</a>
                                <a class="services-button" target="_blank" >Planos de Gas</a>
                                <a class="services-button" target="_blank" >Proveedores Sugeridos</a>
                                <a class="services-button" target="_blank" >Botón extra</a>
                                <a class="services-button" target="_blank" >Botón extra</a>
                                <a class="services-button" target="_blank" >Botón extra</a>
                            </div>
                        </div>
                        <a class="department-button" target="_blank" href="/../../build/img/<?php 
                        echo str_replace(' ', '', trim($department['project_name']))."_";  
                        if($department['project_tower'] != null) { 
                            echo str_replace(' ', '', trim($department['project_tower']))."_"; 
                        } 
                        echo str_replace(' ', '', trim($department['department_basement'])); 
                        ?>.webp">Planos de estacionamiento</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No hay departamentos asociados a este proyecto.</p>
        <?php endif; ?>
    </div>
</main>
