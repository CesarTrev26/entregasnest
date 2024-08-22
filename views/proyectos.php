
<body>
    
    <header></header>
    <main>
        <section class="project-section">
            <h2>Selecciona el proyecto</h2>

            <div class="project-container">
                <?php foreach ($projects_with_departments as $project) { ?>
                    
                    <div class="project-div" onclick="showProjectButtons(this)">
                        
                        <div class="project-logo-div">
                            <img class="project-img" width="750" height="750" src="build/img/<?php echo $project['project_name']; ?>Logo.webp" alt="<?php echo $project['project_name']; ?>_Logo">
                        </div>

                        <div class="project-buttons-div">
                            <!-- <div class="divisor-div">
                                <span class="gray-line"></span>
                            </div> -->
                            <a class="gray-btn project-button" href="/proyectos/<?php echo $project['project_name']; ?>?project_id=<?php echo $project['id']; ?>">Departamentos</a>
                            <!-- <div class="divisor-div">
                                <span class="gray-line"></span>
                            </div> -->
                            <a target="_blank" href="build/pdf/manual_<?php echo $project['project_name']; ?>.pdf" class="gray-btn project-button">Manual</a>
                            <!-- <div class="divisor-div">
                                <span class="gray-line"></span>
                            </div> -->
                            <button type="button" class="gray-btn project-button" onclick="showProjectVideo(event, this)">Videos</button>
                        </div>
                        
                    </div>
                    <div class="project-video-div" id="video-div">
                        <button type="button" class="close-modal-button">Cerrar</button>
                        <video class="project-video <?php echo $project['project_name']; ?>" width="320" height="240" controls>
                        <source src="build/video/WE2T_video.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                        </video>
                    </div>
                <?php } ?>
                <?php if (empty($projects_with_departments)) { ?>
                    <div class="project-logo-div">
                        <h2>No tienes proyectos con departamentos asociados.</h2>
                    </div>
                <?php } ?>
                
                <div id="historyDiv" class="project-div" style="max-height: 500px; height: 0; visibility: hidden;">
                    <div class="project-logo-div">
                        <h2>Historial<br> de cliente</h2>
                    </div>
                </div>

            </div>
        </section>
    </main>