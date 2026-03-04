<?php
include 'db.php'; // Include database connection

if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    // Query to fetch cargo booking details along with cargo flight details
    $query = "SELECT 
                cb.*, 
                cf.flight_name, 
                cf.source, 
                cf.destination, 
                cf.date, 
                cf.departure_time, 
                cf.arrival_time, 
                cf.price_per_kg 
              FROM cargo_bookings cb
              LEFT JOIN cargo_flights cf ON cb.flight_id = cf.flight_id
              WHERE cb.id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo "<h2>Booking Details for Booking ID: {$row['id']}</h2>";
        echo "<table border='1'>
                <tr><th>Booking ID</th><td>{$row['id']}</td></tr>
                <tr><th>Flight ID</th><td>{$row['flight_id']}</td></tr>
                <tr><th>Flight Name</th><td>{$row['flight_name']}</td></tr>
                <tr><th>Source</th><td>{$row['source']}</td></tr>
                <tr><th>Destination</th><td>{$row['destination']}</td></tr>
                <tr><th>Date</th><td>{$row['date']}</td></tr>
                <tr><th>Departure Time</th><td>{$row['departure_time']}</td></tr>
                <tr><th>Arrival Time</th><td>{$row['arrival_time']}</td></tr>
                <tr><th>Price Per KG (₹)</th><td>{$row['price_per_kg']}</td></tr>
                <tr><th>Passenger Name</th><td>{$row['user_name']}</td></tr>
                <tr><th>Email</th><td>{$row['email']}</td></tr>
                <tr><th>Phone</th><td>{$row['phone']}</td></tr>
                <tr><th>Weight Booked (kg)</th><td>{$row['weight']}</td></tr>
                <tr><th>Total Price (₹)</th><td>{$row['total_price']}</td></tr>
                <tr><th>Status</th><td>{$row['status']}</td></tr>
               
              </table>";
    } else {
        echo "<h2>Booking not found.</h2>";
    }

    $stmt->close();
} else {
    echo "<h2>Invalid request.</h2>";
}

$conn->close();
?>
