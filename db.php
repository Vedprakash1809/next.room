<?php
$hostname = "localhost";
$username = "root";
$password = "Ved@1234";
$database = "dbcontact";

$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
