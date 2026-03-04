<?php
$host = "localhost";  // Your database host (default: localhost)
$user = "root";       // Database username (default: root)
$pass = "";           // Database password (default: empty)
$dbname = "air17";    // Your database name

// Create connection
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
