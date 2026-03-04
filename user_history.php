<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: Please log in first.");
}

$user_id = $_SESSION['user_id'];

// Fetch user's booking history
$query = "SELECT f.flight_id, f.flight_name, f.source, f.destination, f.date, b.seats_booked, b.status 
          FROM bookings b
          INNER JOIN flights f ON b.flight_id = f.flight_id
          WHERE b.user_id = '$user_id'
          ORDER BY f.date DESC"; // Latest bookings first

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Flight History</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        .btn {
            padding: 8px 12px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .cancel-btn {
            background: red;
        }
        .cancel-btn:disabled {
            background: gray;
            cursor: not-allowed;
        }
        .back-btn {
            background: #007bff;
            display: block;
            text-align: center;
            width: 150px;
            margin: 20px auto;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>My Flight History</h2>
        
        <?php if (mysqli_num_rows($result) > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Flight Name</th>
                        <th>Source</th>
                        <th>Destination</th>
                        <th>Date</th>
                        <th>Seats Booked</th>
                        <th>Status</th>
                        <th>Cancel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['flight_name']) ?></td>
                            <td><?= htmlspecialchars($row['source']) ?></td>
                            <td><?= htmlspecialchars($row['destination']) ?></td>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td><?= htmlspecialchars($row['seats_booked']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <?php if ($row['status'] !== 'Cancelled') { ?>
                                    <button class="btn cancel-btn" onclick="cancelFlight(<?= $row['flight_id'] ?>)">Cancel</button>
                                <?php } else { ?>
                                    <button class="btn cancel-btn" disabled>Cancelled</button>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p>No bookings found.</p>
        <?php } ?>

        <a href="user_dashboard.php" class="btn back-btn">Back to Dashboard</a>
    </div>

    <script>
        function cancelFlight(flight_id) {
            if (confirm("Are you sure you want to cancel this flight?")) {
                window.location.href = "cancel_flight.php?flight_id=" + flight_id;
            }
        }
    </script>
</body>
</html>
