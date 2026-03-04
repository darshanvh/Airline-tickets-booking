<?php
session_start();
include("db.php"); // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_booking'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<p style='color: red;'>Session expired. Please login again.</p>";
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $booking_id = $_POST['booking_id'];

    // Check if the booking exists and belongs to the user
    $query = "SELECT status FROM bookings WHERE id = '$booking_id' AND user_id = '$user_id'";
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        if ($row['status'] === 'paid') {
            // Update the booking status to 'Cancelled' and issue a 50% refund
            $update_query = "UPDATE bookings SET status = 'Cancelled' WHERE id = '$booking_id' AND user_id = '$user_id'";
            if (mysqli_query($conn, $update_query)) {
                echo "<script>alert('Booking cancelled successfully. 50% refund will be processed.'); window.location='cancel_booking.php';</script>";
            } else {
                echo "<script>alert('Error cancelling booking. Please try again.'); window.location='cancel_booking.php';</script>";
            }
        } else {
            echo "<script>alert('Booking is already cancelled or not eligible for cancellation.'); window.location='cancel_booking.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid booking or unauthorized access.'); window.location='cancel_booking.php';</script>";
    }
}
?>
