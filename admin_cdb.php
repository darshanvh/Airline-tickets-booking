<?php
include 'db.php';
include 'adminnav.php';


if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='login.php';</script>";
    exit();
}

if (!isset($_GET['booking_id'])) {
    echo "<script>alert('Invalid request!'); window.location.href='my_cargo_bookings.php';</script>";
    exit();
}

$booking_id = (int)$_GET['booking_id'];

$query = "SELECT cb.id AS booking_id, cb.flight_id, cb.user_name, cb.email, cb.weight AS weight_booked, 
                 cb.total_price, cb.status, cb.aadhar_number, 
                 cf.source, cf.destination, cf.date, cf.departure_time, cf.arrival_time
          FROM cargo_bookings cb
          JOIN cargo_flights cf ON cb.flight_id = cf.flight_id
          WHERE cb.id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $booking_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    echo "<script>alert('Booking not found!'); window.location.href='my_cargo_account.php';</script>";
    exit();
}

mysqli_stmt_close($stmt);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargo Booking Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            
            color: #333;
        }

        .container {
            max-width: 500px;
            margin: 10px auto;
            padding: 25px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            background: linear-gradient(to right, #0b4d75, #2ba6d2);
            color: white;
            padding: 12px;
            text-align: center;
            font-size: 20px;
            border-radius: 8px 8px 0 0;
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        td:first-child {
            font-weight: bold;
            width: 40%;
        }

        .status {
            font-weight: bold;
            color: <?= $booking['status'] === 'Cancelled' ? 'red' : 'green' ?>;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #0b4d75;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
            font-size: 14px;
        }

        .back-btn:hover {
            background-color: #2ba6d2;
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
                padding: 20px;
            }

            td {
                padding: 8px;
                font-size: 13px;
            }

            .back-btn {
                padding: 8px 16px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cargo Booking Details</h2>
        <table>
            <tr><td>Booking ID:</td><td><?= $booking['booking_id'] ?></td></tr>
            <tr><td>Flight ID:</td><td><?= $booking['flight_id'] ?></td></tr>
            <tr><td>Passenger Name:</td><td><?= $booking['user_name'] ?></td></tr>
            <tr><td>Email:</td><td><?= $booking['email'] ?></td></tr>
            <tr><td>Aadhar Number:</td><td><?= $booking['aadhar_number'] ?></td></tr>
            <tr><td>Source:</td><td><?= $booking['source'] ?></td></tr>
            <tr><td>Destination:</td><td><?= $booking['destination'] ?></td></tr>
            <tr><td>Date:</td><td><?= $booking['date'] ?></td></tr>
            <tr><td>Departure Time:</td><td><?= $booking['departure_time'] ?></td></tr>
            <tr><td>Arrival Time:</td><td><?= $booking['arrival_time'] ?></td></tr>
            <tr><td>Weight Booked (kg):</td><td><?= $booking['weight_booked'] ?></td></tr>
            <tr><td>Total Price (₹):</td><td><?= $booking['total_price'] ?></td></tr>
            <tr><td>Status:</td><td class="status"><?= $booking['status'] ?></td></tr>
        </table>

    </div>
</body>
</html>
