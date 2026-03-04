<?php
include 'db.php';
session_start();

if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];

    // Update booking status to "Cancelled"
    $update_query = "UPDATE bookings SET status='Cancelled' WHERE id='$booking_id'";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Booking cancelled successfully!'); window.location.href='cancel_booking.php';</script>";
    } else {
        echo "<script>alert('Error cancelling booking.'); window.location.href='cancel_booking.php';</script>";
    }
}
?>
