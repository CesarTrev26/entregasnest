
<?php

// Pass variables to the template
incluirTemplate('SEO', ['userData' => $userData, 'departments' => $departments, 'auth' => $auth]);
?>

<body id="anida">

<header class="header anida">
    <a href="/">
        <img src="/build/img/AnidaLogo-Long.webp" alt="ANIDA_Logo">
    </a>
</header>

<?php incluirTemplate('departments-main', ['userData' => $userData, 'departments' => $departments, 'auth' => $auth]); ?>
