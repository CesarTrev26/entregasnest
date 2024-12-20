<?php

use Model\ActiveRecord;
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
require 'funciones.php';
require 'config/database.php';


//Connect to DB
$db = connectDB();


// Start session at the beginning of the app
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ActiveRecord::setDB($db);
