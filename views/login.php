<?php

//require 'includes/app.php';
//$db = connectDB();

/*$errores = [];

if($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    //echo "<pre>";
    //var_dump($_POST);
    //echo "<pre>";

    $username = mysqli_real_escape_string($db, filter_var( $_POST['username'], FILTER_VALIDATE_EMAIL));

    $password = mysqli_real_escape_string( $db, $_POST['password']);

    if(!$username) {
        $errores[] = "El email es no es válido";
    }
    if(!$password) {
        $errores[] = "La contraseña no es válida";
    }

    if(empty($errores)) {
        $query = "SELECT * FROM users WHERE email = '{$username}'";
        $resultado = mysqli_query($db, $query);
        //var_dump($resultado);

        if( $resultado -> num_rows) {
            $user = mysqli_fetch_assoc($resultado);
            //var_dump($user);
            //var_dump($password);
        
            if($password === $user['password_hash']) {
                session_start();
                $_SESSION['id'] = $user['id'];
                $_SESSION['usuario'] = $user['email']; 
                $_SESSION['phone'] = $user['phone'];
                $_SESSION['login'] = true;

                header('Location: /proyectos.php');

                var_dump($_SESSION);
            } else {
                $errores[] = "La contraseña es incorrecta";
            }

        } else {
            $errores[] = "El usuario o correo no existe"; 
        }
    }

    //echo "<pre>";
    //var_dump($errores);
    //echo "<pre>";

}*/
?>

<?php incluirTemplate('SEO'); ?>

<body>
    <main>
        <div class="first-container">
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

                <?php if($errores) { ?>
                    <div class="errors-div"> 
                        <?php foreach($errores as $error): ?>
                            <p><?php echo $error;?></p>
                        <?php endforeach;?>
                    </div>
                    <?php }?>
                <form class="login-form" method="POST" action="/">
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

<?php incluirTemplate('scripts'); ?>