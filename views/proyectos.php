<?php

// Pass variables to the template
incluirTemplate('SEO', ['userData' => $userData, 'auth' => $auth]);
?>
<body>
    
    <header></header>
    <main>
        <section class="project-section">
            <div class="user-data">
                <div>
                    <p><b>Perfil de usuario</b></p>
                    <?php if ($userData): ?>
                        <p><?php echo htmlspecialchars($userData['full_name']) ?><br> <span> / </span> <?php echo htmlspecialchars($userData['email']) ?></p>
                    <?php else: ?>
                        <p>Información del usuario no disponible</p>
                    <?php endif; ?>
                </div>
                <?php if ($auth): ?>
                    <div class="buttons-div" style="display:flex;align-items:center;column-gap: 10px;">
                        <div class="help-div">
                            <a href="/proyectos/ayuda">Crear solicitud
                                <img src="/build/img/help-white.svg" alt="">
                            </a>
                        </div>
                        <a class="logout-button" href="/cerrar-sesion">Cerrar Sesión</a>
                        
                    </div>
                <?php endif; ?>
            </div>
            <h2>Selecciona el proyecto</h2>

            <div class="project-container">
                <?php foreach ($projects_with_departments as $project) { ?>
                    <div class="project-div" onclick="showProjectButtons(this)">
                        <div class="project-logo-div">
                            <img class="project-img" width="750" height="750" src="build/img/<?php echo $project['project_name']; ?>Logo.webp" alt="<?php echo $project['project_name']; ?>_Logo">
                        </div>

                        <div class="project-buttons-div">
                            <a class="gray-btn project-button" href="/proyectos/<?php echo $project['project_name']; ?>?project_id=<?php echo $project['id']; ?>">Departamentos</a>
                            
                            <!-- Check if there are manual file paths -->
                            <?php if ($project['manual_paths']) { ?>
                                <a target="_blank" href="<?php echo $project['manual_paths']; ?>" class="gray-btn project-button">Manual</a>
                            <?php } ?>

                            <!-- Check if there are video file paths -->
                            <?php if ($project['video_paths']) { ?>
                                <button type="button" class="gray-btn project-button" onclick="showProjectVideo(event, this)">Videos</button>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="project-video-div" id="video-div" style="display:none;">
                        <div class="background-video"></div>
                        <button type="button" class="close-modal-button">Cerrar</button>
                        <video class="project-video <?php echo $project['project_name']; ?>" width="320" height="240" controls>
                            <source src="<?php echo $project['video_paths']; ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                <?php } ?>
                <?php if (empty($projects_with_departments)) { ?>
                    <div class="project-logo-div">
                        <h2>No tienes proyectos con departamentos asociados.</h2>
                    </div>
                <?php } ?>

                <!-- <div id="historyDiv" class="project-div" style="max-height: 500px; height: 0; visibility: hidden;">
                    <div class="project-logo-div">
                        <h2>Historial<br> de cliente</h2>
                    </div>
                </div>-->
            </div>


        </section>
    </main>