<?php
use Model\PlanTypesCRUD;

// Fetch all plan types
$planTypes = PlanTypesCRUD::all();

// Convert objects to an associative array for JavaScript
$planTypeMappings = [];
foreach ($planTypes as $planType) {
    $planTypeMappings[$planType->keyword] = $planType->description;
}
?>

<?php incluirTemplate('Admin-navbar'); ?>

<main class="admin-content">
    <section class="admin-section">
        <h1>Subir Archivo</h1>

        <div class="actions-div">
            <a href="/admin/files/plantypes">Agregar tipo de archivo</a>
        </div>

        <div id="status-messages">
            <p id="success-message" style="color: green; display: none;"></p>
            <ul id="error-messages" style="color: red; display: none;"></ul>
            <ul id="info-messages" style="color: blue; display: none;"></ul>
        </div>

        <div class="form-div">
            <!-- Multi-file upload form -->
            <form id="fileUploadForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="file">Seleccione Archivos</label>
                    <input style="width: 65vmin;" type="file" name="files[]" id="file" multiple accept=".pdf,.doc,.docx,.png,.jpg,.jpeg,.webp,.mp4" required>
                </div>
                <button type="button" onclick="uploadFiles()">Subir Archivos</button>
            </form>

            <!-- Progress container for each file -->
            <div id="progressContainer"></div>
        </div>

    </section>
</main>


<script>
    const planTypeMappings = <?php echo json_encode($planTypeMappings); ?>;

    function getPlanType(filename) {
        // Convert filename to lowercase for case-insensitive comparison
        const filenameLower = filename.toLowerCase();

        // Normalize the filename by splitting camelCase words and removing non-alphanumeric characters
        const normalizedFilename = filenameLower
            .replace(/([a-z])([A-Z])/g, '$1 $2') // Add space between lowercase and uppercase letters
            .replace(/[\W_]+/g, ' ') // Replace non-alphanumeric characters (except spaces) with spaces
            .trim();

        // Loop through each plan type mapping, sorting the keywords to check longer ones first
        const sortedKeywords = Object.entries(planTypeMappings)
            .sort((a, b) => b[0].length - a[0].length); // Sort by keyword length (longer first)

        // Check each sorted keyword
        for (const [keyword, type] of sortedKeywords) {
            // Normalize keyword by removing extra spaces and converting to lowercase
            const normalizedKeyword = keyword.toLowerCase().replace(/\s+/g, ' ').trim();

            // Check if the normalized keyword appears anywhere in the normalized filename
            if (normalizedFilename.includes(normalizedKeyword)) {
                return type; // Return the matched type
            }
        }

        return 'Desconocido'; // Default return value if no match is found
    }

    function uploadFiles() {
    const files = document.getElementById('file').files;
    const progressContainer = document.getElementById('progressContainer');
    progressContainer.innerHTML = ''; // Clear previous progress bars

    Array.from(files).forEach((file, index) => {
        const formData = new FormData();
        const planType = getPlanType(file.name);
        formData.append('files[]', file); // Use 'files[]' here to match the server's expected input
        formData.append('plan_type', planType);

        const progressDiv = document.createElement('div');
        progressDiv.innerHTML = `<div class="file-div"><p>${file.name} (${planType}):</p> <progress id="progress${index}" max="100" value="0"></progress></div> <span id="status${index}"></span>`;
        progressContainer.appendChild(progressDiv);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/admin/files/upload', true);

        // Track upload progress
        xhr.upload.addEventListener('progress', (event) => {
            if (event.lengthComputable) {
                const percentComplete = (event.loaded / event.total) * 100;
                document.getElementById(`progress${index}`).value = percentComplete;
            }
        });

        // Process the server response when the upload completes
        xhr.onload = function() {
            try {
                const response = JSON.parse(xhr.responseText);
                let message = ''; // To store the final message for the status

                // Handle success messages
                if (response.responses) {
                    response.responses.forEach(resp => {
                        if (resp.success) {
                            message += `<p style="color: green;">${resp.success}</p>`;
                        }
                        if (resp.info) {
                            message += `<p style="color: blue;">${resp.info}</p>`;
                        }
                    });
                }

                // Handle errors
                if (response.errors) {
                    response.errors.forEach(err => {
                        message += `<p style="color: red;">${err}</p>`;
                    });
                }

                // Display the final message
                document.getElementById(`status${index}`).innerHTML = message;
            } catch (e) {
                console.error("Failed to parse server response:", xhr.responseText);
                document.getElementById(`status${index}`).innerHTML = "<p style='color: red;'>Error al interpretar la respuesta del servidor</p>";
            }
        };

        // Handle network errors
        xhr.onerror = function () {
            document.getElementById(`status${index}`).innerHTML = "<p style='color: red;'>Error de red durante la subida</p>";
            console.error(`Error de red durante la subida ${file.name}`);
        };

        xhr.send(formData);
    });
}
</script>