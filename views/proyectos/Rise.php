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

<footer>
    <a class="nest-footer"><img src="../build/img/NestLogo.webp" alt=""></a>
    <a class="phone-footer">
        <svg id="SvgjsSvg1077" width="3.5vw" height="3.5vw" style="margin-right: 0px;" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs"><defs id="SvgjsDefs1078"></defs><g id="SvgjsG1079"><svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 100 100" viewBox="0 0 200 50" width="3.5vw" height="3.5vw"><switch><g><path d="M5273.1,2400.1v-2c0-2.8-5-4-9.7-4s-9.7,1.3-9.7,4v2c0,1.8,0.7,3.6,2,4.9l5,4.9c0.3,0.3,0.4,0.6,0.4,1v6.4				c0,0.4,0.2,0.7,0.6,0.8l2.9,0.9c0.5,0.1,1-0.2,1-0.8v-7.2c0-0.4,0.2-0.7,0.4-1l5.1-5C5272.4,2403.7,5273.1,2401.9,5273.1,2400.1z
            M5263.4,2400c-4.8,0-7.4-1.3-7.5-1.8v0c0.1-0.5,2.7-1.8,7.5-1.8c4.8,0,7.3,1.3,7.5,1.8C5270.7,2398.7,5268.2,2400,5263.4,2400z" fill="#76777A" class="color000 svgShape"></path><path d="M5268.4 2410.3c-.6 0-1 .4-1 1 0 .6.4 1 1 1h4.3c.6 0 1-.4 1-1 0-.6-.4-1-1-1H5268.4zM5272.7 2413.7h-4.3c-.6 0-1 .4-1 1 0 .6.4 1 1 1h4.3c.6 0 1-.4 1-1C5273.7 2414.1 5273.3 2413.7 5272.7 2413.7zM5272.7 2417h-4.3c-.6 0-1 .4-1 1 0 .6.4 1 1 1h4.3c.6 0 1-.4 1-1C5273.7 2417.5 5273.3 2417 5272.7 2417zM94 71.5l-19.4-10c-2.7-1.4-6.1-.7-8 1.7l-7.1 9.1c-6.9-3.7-12.2-7.3-18.3-13.4-6.5-6.5-10-11.9-13.6-18.6l9-7c2.4-1.9 3.2-5.3 1.7-8L28.4 6c-1.8-3.4-6.1-4.5-9.2-2.4L6.8 11.8C4 13.7 2.4 17 2.6 20.4c.2 3.5.7 7.9 1.5 11 3.6 13.8 12.2 28.1 24.3 40.2C40.5 83.8 54.8 92.4 68.6 96c3.1.8 7.5 1.2 11.1 1.5 3.5.2 6.8-1.5 8.7-4.4l8-12.5C98.5 77.4 97.3 73.2 94 71.5z" fill="#76777A" class="color000 svgShape"></path></g></switch></svg></g>
        </svg>
        <p>81 3405 5331</p>
    </a>
    <a class ="gava-footer"><img src="../build/img/GavaCapitalLogo.webp" alt=""></a>
</footer>