<?php

// Pass variables to the template
incluirTemplate('SEO', ['userData' => $userData, 'departments' => $departments, 'auth' => $auth]);
?>

<body id="west">

<header class="header">
    <a href="/">
        <img src="/build/img/WE2TLogo-Long.webp" alt="WE2T_Logo">
    </a>
</header>

<?php incluirTemplate('departments-main', ['userData' => $userData, 'departments' => $departments, 'auth' => $auth]); ?>

