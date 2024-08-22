<?php

define('TEMPLATES_URL', __DIR__ . '/templates');

function incluirTemplate(string $nombre, $variables = []) {
    extract($variables);
    include TEMPLATES_URL . "/${nombre}.php";
}

function isAuth() {
    if(!isset($_SESSION)) {
        session_start();
    }

    if(!$_SESSION['login']) {
        header('Location: /');
    }
}

function debug($variable) {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}
