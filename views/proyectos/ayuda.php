<!DOCTYPE html>
<html lang="en" style="scroll-behavior: smooth;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda</title>
</head>
<body class="help">
    <div class="first-section">
        <p>nest.living</p>

        <div class="text-div">
            <h2>¿Tienes algún detalle <br> con tu departamento?</h2>

            <a class="gray-btn" href="#helpForm">Haz un reporte</a>
        </div>
    </div>
    
    <div class="form-section">
        <div class="back-arrow">
            <svg onclick="history.back()" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" viewBox="0 0 32 32" enable-background="new 0 0 32 32" width="32px" xml:space="preserve">
                <line fill="none" stroke="#000000" stroke-width="1" stroke-miterlimit="10" x1="6" y1="16" x2="28" y2="16"/>
                <polyline fill="none" stroke="#000000" stroke-width="1" stroke-miterlimit="10" points="14,24.5 5.5,16 14,7.5 "/>
            </svg>
        </div>
        <div class="errors-div help-errors"> 
            <?php foreach($errores as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
        <div id="loadingIndicator" style="display: none;">
            <p>Cargando...</p>
        </div>
        <form id="helpForm" method="POST" action="/proyectos/ayuda" enctype="multipart/form-data">
            <div class="info-div">
                <!-- Name Field -->
                <div class="input-div">
                    <input type="text" name="Name" id="Name" placeholder=" ">
                    <label for="Name">Nombre</label>
                </div>
                
                <!-- Email Field -->
                <div class="input-div">
                    <input type="email" name="Email" id="Email" placeholder=" ">
                    <label for="Email">Correo electrónico</label>
                </div>
                
                <!-- Project Dropdown -->
                <div class="input-div">
                    <select name="Project" id="projectID" required>
                        <option value="">Seleccionar Proyecto</option>
                    </select>
                </div>
                <input type="hidden" id="projectName" name="projectName">

                <!-- Department and Tower Fields (Grouped) -->
                <div class="double-input">

                    <!-- Tower Dropdown -->
                    <div class="input-div">
                        <select name="Tower" id="towerID" style="opacity:0;" required>
                            <option value="">Seleccionar Torre</option>
                        </select>
                    </div>

                    <!-- Department Dropdown -->
                    <div class="input-div">
                        <select name="Department" id="departmentID" style="opacity:0;" required>
                            <option value="">Seleccionar Departamento</option>
                        </select>
                    </div>
                </div>

            </div>

            <!-- Evidence Section -->
            <div class="evidence-div">
                <p>Sube tu evidencia</p>
                
                <!-- File Upload Field -->
                <div class="input-div file-div">
                    <input type="file" id="file" name="file" accept="image/*,.pdf" multiple hidden/>
                    <label for="file" class="file-label">Imagen/Video <img src="/build/img/upload-svg.svg" alt=""></label>
                    <span id="file-name">Sin Archivos seleccionados</span> <!-- Optional: shows selected file name(s) -->
                </div>
                
                <!-- Message Field -->
                <div class="input-div">
                    <textarea name="Message" id="Message" placeholder=" "></textarea>
                    <label for="Message">Mensaje</label>
                </div>

                <!-- Submit Button -->
                <button class="gray-btn" type="submit">Enviar</button>
            </div>
        </form>
    </div>

    <div id="thanks-div" class="thanks-div">
        <div>
            <h2>Solicitud enviada con éxito</h2>
            <p>¡Gracias por tu reporte!</p>
        </div>
        <div class="icons-container">
            <div class="icons-div">
                <div>
                    <img src="/build/img/help-mail.webp" alt="">
                </div>
                <p>Recibirás confirmación<br><span>al correo proporcionado</span></p>
            </div>
            <div class="icons-div">
                <div>
                    <img src="/build/img/help-phone.webp" alt="">
                </div>
                <p>Nos contactaremos pronto</p>
            </div>
        </div>
    </div>

    <footer></footer>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const loadingIndicator = document.getElementById('loadingIndicator'); // Reference to the loading indicator
        
        // Dynamically get the userId from your session
        const userId = <?php echo $_SESSION['id']; ?>;

        // Fetch projects and populate the project dropdown
        fetch(`/proyectos/api/getProjectsForUser/${userId}`)
            .then(response => response.json())
            .then(data => {
                const projectSelect = document.getElementById('projectID');
                projectSelect.innerHTML = '<option value="">Seleccionar Proyecto</option>'; // Reset options
                
                if (data.projects && data.projects.length > 0) {
                    data.projects.forEach(project => {
                        const option = document.createElement('option');
                        option.value = project.id;
                        option.textContent = project.project_name;
                        projectSelect.appendChild(option);
                    });
                } else {
                    projectSelect.innerHTML = '<option value="">No hay proyectos con departamentos</option>';
                }
            })
            
            .catch(error => console.error('Error fetching projects:', error));

            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            const debouncedPopulateTowers = debounce(populateTowers, 300);
            const debouncedPopulateDepartments = debounce(populateDepartments, 300);

        // Event listener for the project select change
        document.getElementById('projectID').addEventListener('change', function() {
            const projectId = this.value;
            const towerSelect = document.getElementById('towerID');
            const departmentSelect = document.getElementById('departmentID');
            if (projectId) {
                showLoadingIndicator();
                debouncedPopulateTowers(userId, projectId);
                towerSelect.style.opacity = "1";
                departmentSelect.style.opacity = "0";
            } else {
                towerSelect.style.opacity = "0";
                departmentSelect.style.opacity = "0";
                resetTowersAndDepartments();
            }
        });

        // Event listener for the tower select change
        document.getElementById('towerID').addEventListener('change', function() {
            const towerId = this.value;
            const projectId = document.getElementById('projectID').value;
            const departmentSelect = document.getElementById('departmentID');
            if (towerId && projectId) {
                showLoadingIndicator();
                populateDepartments(userId, projectId, towerId);
                departmentSelect.style.opacity = "1";
            } else {
                departmentSelect.style.opacity = "0";
                resetDepartments();
            }
        });

        // Function to populate the towers based on selected project and user
        function populateTowers(userId, projectId) {
            fetch(`/proyectos/api/getTowersForProject/${userId}/${projectId}`)
            .then(response => response.json())
            .then(data => {
                console.log("Towers data:", data);

                const towerSelect = document.getElementById('towerID');
                const projectId = document.getElementById('projectID')
                towerSelect.innerHTML = '<option value="">Seleccionar Torre</option>'; // Reset options
                if(projectId.value == 2) {
                    towerSelect.innerHTML = '<option value="">Rise</option>';
                }

                if (data.towers && data.towers.length > 0) {
                    data.towers.forEach(tower => {
                        if (tower && projectId.value != 2) {
                            const option = document.createElement('option');
                            option.value = tower;  // Use the tower name as the value
                            option.textContent = tower;  // Use the tower name as the display text
                            towerSelect.appendChild(option);
                        }
                    });
                } else {
                    towerSelect.innerHTML = '<option value="">No hay torres disponibles</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching towers:', error);
                alert('Error al cargar las torres. Inténtelo de nuevo más tarde.');
            })
            .finally(() => {
                hideLoadingIndicator();
            });
        }

        // Function to populate the departments based on selected tower
        function populateDepartments(userId, projectId, towerId) {
            fetch(`/proyectos/api/getDepartmentsForTower/${userId}/${projectId}/${towerId}`)
            .then(response => response.json())
            .then(data => {
                const departmentSelect = document.getElementById('departmentID');
                departmentSelect.innerHTML = '<option value="">Seleccionar Departamento</option>'; // Reset options

                if (data.departments && data.departments.length > 0) {
                    data.departments.forEach(department => {
                        const option = document.createElement('option');
                        option.value = department.department_name;  // Set the department name as the value
                        option.textContent = department.department_name;  // Display the department name
                        departmentSelect.appendChild(option);
                    });
                } else {
                    departmentSelect.innerHTML = '<option value="">No hay departamentos disponibles</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching departments:', error);
            })
            .finally(() => {
                hideLoadingIndicator();
            });
        }

        // Function to show the loading indicator
        function showLoadingIndicator() {
            loadingIndicator.style.display = 'block';
        }

        // Function to hide the loading indicator
        function hideLoadingIndicator() {
            loadingIndicator.style.display = 'none';
        }

        // Function to reset towers and departments if no project is selected
        function resetTowersAndDepartments() {
            document.getElementById('towerID').innerHTML = '<option value="">Seleccionar Torre</option>';
            document.getElementById('departmentID').innerHTML = '<option value="">Seleccionar Departamento</option>';
        }

        // Function to reset departments if no tower is selected
        function resetDepartments() {
            document.getElementById('departmentID').innerHTML = '<option value="">Seleccionar Departamento</option>';
        }
    });
</script>

    <script>
   document.addEventListener('DOMContentLoaded', function() {
    const fileNameDisplay = document.getElementById('file-name');
    const submitButton = document.querySelector('button[type="submit"]'); // Select the submit button
    
    document.getElementById('file').addEventListener('change', function() {
        const files = this.files;
        const names = Array.from(files).map(file => file.name).join(', '); 
        fileNameDisplay.textContent = names || 'Sin Archivos seleccionados';
    });
    
    const form = document.getElementById('helpForm');
    const thanksDiv = document.getElementById('thanks-div');
    
    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        
        // Change button text and disable it
        submitButton.textContent = 'Enviando...';
        submitButton.disabled = true;

        // Get the selected project name (text content)
        const projectSelect = document.getElementById('projectID');
        const selectedProjectOption = projectSelect.options[projectSelect.selectedIndex];
        const projectName = selectedProjectOption.textContent;

        // Get the selected department name (text content)
        const departmentSelect = document.getElementById('departmentID');
        const selectedDepartmentOption = departmentSelect.options[departmentSelect.selectedIndex];
        const departmentName = selectedDepartmentOption.textContent;

        // Create a new FormData object
        const formData = new FormData(form);

        // Add the project name and department name to the FormData object
        formData.append('projectName', projectName);
        formData.append('departmentName', departmentName);

        // Send the data via fetch
        fetch('/proyectos/ayuda', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                thanksDiv.style.display = "block";
                form.reset();
                fileNameDisplay.textContent = 'Sin Archivos seleccionados';
                thanksDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                displayErrors(data.errors);
            } else if(data.errors) {
                displayErrors(data.errors);
            }
        })
        .catch(() => {
            alert("Ocurrió un error enviando tu mensaje.");
        })
        .finally(() => {
            // Reset button text and enable it again after submission completes
            submitButton.textContent = 'Enviar';
            submitButton.disabled = false;
        });
    });

    function displayErrors(errors) {
        const errorContainer = document.querySelector('.errors-div');
        errorContainer.innerHTML = ''; // Clear previous errors

        if (Array.isArray(errors) && errors.length > 0) {
            errors.forEach(error => {
                const errorElement = document.createElement('p');
                errorElement.textContent = error;
                errorContainer.appendChild(errorElement);
            });
            errorContainer.style.display = 'flex';
        } else {
            console.warn('No hay errores para mostrar');
        }
    }
});
</script>
</body>
</html>