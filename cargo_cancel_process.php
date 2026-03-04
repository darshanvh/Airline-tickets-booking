<?php
include 'db.php'; // Database connection

if (isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];

    // Check if the booking exists
    $check_query = "SELECT * FROM cargo_bookings WHERE id = ?";
    $stmt_check = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt_check, "i", $booking_id);
    mysqli_stmt_execute($stmt_check);
    $result = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result) > 0) {
        // Update status to "Cancelled"
        $update_query = "UPDATE cargo_bookings SET status = 'Cancelled' WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt_update, "i", $booking_id);

        if (mysqli_stmt_execute($stmt_update)) {
            echo "<script>alert('50% amount will be returned. Your booking is cancelled.'); window.location='cargo_cancel.php';</script>";
        } else {
            echo "<script>alert('Error cancelling booking.'); window.location='cargo_cancel.php';</script>";
        }
        mysqli_stmt_close($stmt_update);
    } else {
        echo "<script>alert('Error: Booking not found.'); window.location='cargo_cancel.php';</script>";
    }

    mysqli_stmt_close($stmt_check);
}

header("Location: cargo_cancel.php");
exit();
?>
