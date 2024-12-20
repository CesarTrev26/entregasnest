<?php incluirTemplate('SEO'); ?>

<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script async>
$(document).ready(function() {
    $('#loginForm').on('submit', function(event) {
        event.preventDefault();

        // Create a FormData object from the form
        const formData = new FormData(this);

        fetch('/login/ajax', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirectURL;
            } else if (data.errors) {
                displayErrors(data.errors);
            }
        })
        .catch(error => console.error('Fetch error:', error));
    });

    function displayErrors(errors) {
        const errorContainer = $('.errors-div');
        errorContainer.empty();

        if (Array.isArray(errors) && errors.length > 0) {
            errors.forEach(error => {
                errorContainer.append(`<p>${error}</p>`);
            });
            errorContainer.show();
        } else {
            console.warn('No hay errores para mostrar');
        }
    }
});
    </script>
</head>
<body>
    <main>
        <div class="first-container">
            <div class="nest-logo">
                <img src="/build/img/Nest (1).webp" alt="">
            </div>

            <div class="login-div">
                
                <h1><span>Bienvenido</span><br>a una nueva forma<br> de habitar</h1>
                <button type="button" class="white-btn login-btn" aria-label="Iniciar sesión">Iniciar sesión</button>
            </div>        
        </div>

        <div class="login-popup-container">
            <button class="close-btn" aria-label="Cerrar">
                <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g id="Menu / Close_MD">
                <path id="Vector" d="M18 18L12 12M12 12L6 6M12 12L18 6M12 12L6 18" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                </svg>
            </button>
            <div class="login-popup-div">
                <h2>Acceso</h2>

                
                    <div class="errors-div"> 
                        <?php foreach($errores as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>

                <form id="loginForm" class="login-form" method="POST" action="/login/ajax">
                    <div class="input-div">
                        <input type="email" name="email" id="email" placeholder="Email">
                        <label for="email">Email</label>
                    </div>
                    <div class="input-div">
                        <input type="password" name="password" id="password" placeholder="Contraseña">
                        <label for="password">Contraseña</label>
                    </div>
                    <div class="submit-div">
                        <button class="gray-btn" type="submit" aria-label="Entrar">Entrar<span class="arrow"></span></button>    
                    </div>
                </form>
            </div>
        </div>
    </main>
