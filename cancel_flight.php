<?php
session_start();
include 'db.php';

if (isset($_GET['id'])) {
    $flight_id = $_GET['id'];

    // Update flight status to "Cancelled"
    $sql = "UPDATE flights SET status = 'Cancelled' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $flight_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Flight cancelled successfully!'); window.location.href='manage_flight.php';</script>";
    } else {
        echo "<script>alert('Error cancelling flight!'); window.location.href='manage_flight.php';</script>";
    }
}

mysqli_close($conn);
?>
