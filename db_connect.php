<?php
// db_connect.php

$server   = "localhost";
$username = "root";
$password = "CHANGEMETOYOURSQLSERVERPASS";
$dbname   = "helping_hand";

$mysqli = new mysqli($server, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}
?>
