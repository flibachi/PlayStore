<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "simple_site";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}
?>
