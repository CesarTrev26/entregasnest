<?php if(!isset($_SESSION)) {
    session_start();
}

$_SESSION['id'] = 1;

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Aplicación web realizada para clientes que han comprado departamentos en Nest, y que estos mismos puedan ver información acerca de sus departamentos"/>
    <meta name="keywords" content="Nest, Desarollo Inmobiliario, Monterrey, San Pedro, Nest Living, WEST, ANIDA, RISE">
    <title>NEST</title>
    <link rel="preload" href="../build/css/app.css" as="style">
    <link rel="preload" fetchpriority="high" as="image" href="../build/img/bg-img-login-darken.webp" type="image/webp">
    <link rel="stylesheet" href="../build/css/app.css">
    <link rel="preload" href="../build/js/js.js" as="script">
</head>

<?php echo $contenido; ?>






<script src="../build/js/js.js"></script>
</body>
</html>