<?php

// Pass variables to the template
incluirTemplate('SEO', ['userData' => $userData, 'departments' => $departments, 'auth' => $auth]);
?>

<body id="rise">

<header class="header">
    <a href="/">
        <img src="/build/img/RiseLogo-Long.webp" alt="RISE_Logo">
    </a>
</header>

<?php incluirTemplate('departments-main', ['userData' => $userData, 'departments' => $departments, 'auth' => $auth]); ?>

