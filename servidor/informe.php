<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');
    require_once 'functions.php';

    getReport();

?>