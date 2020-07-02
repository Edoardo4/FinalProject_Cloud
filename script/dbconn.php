<?php

/*$servername = "localhost";
$username = "userPresutti";
$password = "password";
$dbname = "finalproject";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}*/

//session_start();
$dsn = 'mysql:dbname=finalproject;host=localhost';
$user = 'SQLAdmin';     //Insert here db user and password 
$password = 'Admin';

try
{
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
}
catch (PDOException $e)
{
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}
