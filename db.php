<?php

$host = "localhost";
$user = "root";
$password = "";
$db = "inventory_management";

$data = mysqli_connect($host, $user, $password, $db);

if (!$data) {
    die("Database connection failed: " . mysqli_connect_error());
}

?>