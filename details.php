<?php 
session_start();
include("db.php");
include("adminnav.php");

if (!isset($_GET['booking_id'])) {
    echo "<p style='color: red;'>❗ Invalid request.</p>";
    exit();
}

$booking_id = $_GET['booking_id'];

$query = "SELECT b.id, f.flight_name, f.id AS flight_id, f.source, f.destination, f.date, f.departure_time, 
                 u.username, u.phone, b.seat_numbers, b.total_price, b.seat_type,
                 b.aadhar_number, b.age, b.name
          FROM bookings b 
          JOIN flights f ON b.flight_id = f.id 
          JOIN users u ON b.user_id = u.id 
          WHERE b.id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $booking_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "<p style='color: red;'>❗ Booking not found.</p>";
    exit();
}

$booking = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Boarding Pass</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;

            color: #333;
        }

        .boarding-pass {
            max-width: 500px;
            margin: 20px auto;
            padding: 25px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(to right, #0b4d75, #2ba6d2);
            color: white;
            padding: 12px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border-radius: 8px 8px 0 0;
            margin-top: 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        .row:last-child {
            border-bottom: none;
        }

        .bold {
            font-weight: bold;
            color: #000;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            background-color: #0b4d75;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background-color: #2ba6d2;
        }

        @media (max-width: 768px) {
            .boarding-pass {
                width: 90%;
                padding: 20px;
            }

            .row {
                flex-direction: column;
                align-items: flex-start;
            }

            .row span {
                margin: 3px 0;
            }
        }
    </style>
</head>
<body>
    <div class="boarding-pass">
        <div class="header">✈️ Sky Airlines - Boarding Pass</div>
        <div class="content">
            <div class="row"><span class="bold">Booking ID:</span><span><?= htmlspecialchars($booking['id']); ?></span></div>
            <div class="row"><span class="bold">Passenger Name:</span><span><?= htmlspecialchars($booking['name']); ?></span></div>
            <div class="row"><span class="bold">Aadhar Number:</span><span><?= htmlspecialchars($booking['aadhar_number']); ?></span></div>
            <div class="row"><span class="bold">Age:</span><span><?= htmlspecialchars($booking['age']); ?></span></div>
            <div class="row"><span class="bold">Phone No:</span><span><?= htmlspecialchars($booking['phone']); ?></span></div>
            <div class="row"><span class="bold">Flight:</span><span><?= htmlspecialchars($booking['flight_name']); ?> (<?= htmlspecialchars($booking['flight_id']); ?>)</span></div>
            <div class="row"><span class="bold">From:</span><span><?= htmlspecialchars($booking['source']); ?></span></div>
            <div class="row"><span class="bold">To:</span><span><?= htmlspecialchars($booking['destination']); ?></span></div>
            <div class="row"><span class="bold">Date:</span><span><?= htmlspecialchars($booking['date']); ?></span></div>
            <div class="row"><span class="bold">Departure:</span><span><?= htmlspecialchars($booking['departure_time']); ?></span></div>
            <div class="row"><span class="bold">Seat:</span><span><?= htmlspecialchars($booking['seat_numbers']); ?></span></div>
            <div class="row"><span class="bold">Seat Type:</span><span><?= htmlspecialchars($booking['seat_type']); ?></span></div>
            <div class="row"><span class="bold">Total Price:</span><span>₹<?= htmlspecialchars($booking['total_price']); ?></span></div>
        </div>
        <div class="footer">
          
        </div>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?>
