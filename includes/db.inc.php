<?php
$dbServername = "192.168.0.20";
$dbUsername = "root";
$dbPassword = "";
$dbName = "dmh_registrationsystem";

$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);

if ( mysqli_connect_errno() ) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}