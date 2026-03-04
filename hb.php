<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $flight_id = $_POST['flight_id'];
    $price_per_kg = $_POST['price_per_kg'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $weight = $_POST['weight'];

    // Fetch flight details
    $query = "SELECT * FROM cargo_flights WHERE flight_id = '$flight_id'";
    $result = mysqli_query($conn, $query);
    $flight = mysqli_fetch_assoc($result);

    if (!$flight) {
        echo "Flight not found.";
        exit();
    }

    // Calculate total price
    $total_price = $weight * $price_per_kg;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargo Booking Summary</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .summary { background: white; padding: 20px; border-radius: 10px; max-width: 600px; margin: auto; }
        .history { background: white; padding: 20px; border-radius: 10px; max-width: 600px; margin: auto; margin-top: 20px; }
        h2 { text-align: center; }
        table { width: 100%; margin-top: 10px; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        button { background: green; color: white; padding: 10px; border: none; width: 100%; margin-top: 10px; }
    </style>
</head>
<body>

    <div class="summary">
        <h2>Booking Summary</h2>
        <table>
            <tr><th>Flight ID</th><td><?= $flight['flight_id'] ?></td></tr>
            <tr><th>Source</th><td><?= $flight['source'] ?></td></tr>
            <tr><th>Destination</th><td><?= $flight['destination'] ?></td></tr>
            <tr><th>Departure Time</th><td><?= $flight['departure_time'] ?></td></tr>
            <tr><th>Arrival Time</th><td><?= $flight['arrival_time'] ?></td></tr>
            <tr><th>Total Weight</th><td><?= $weight ?> KG</td></tr>
            <tr><th>Total Amount</th><td>₹<?= number_format($total_price, 2) ?></td></tr>
        </table>

        <form method="POST" action="cargo_process.php">
            <input type="hidden" name="flight_id" value="<?= $flight_id ?>">
            <input type="hidden" name="name" value="<?= $name ?>">
            <input type="hidden" name="phone" value="<?= $phone ?>">
            <input type="hidden" name="email" value="<?= $email ?>">
            <input type="hidden" name="address" value="<?= $address ?>">
            <input type="hidden" name="weight" value="<?= $weight ?>">
            <input type="hidden" name="total_price" value="<?= $total_price ?>">
            <button type="submit">Confirm & Pay</button>
        </form>
    </div>

    <div class="history">
        <h2>Cargo Booking History</h2>
        <?php
        // Query to fetch cargo booking history
        $history_query = "SELECT 
                            flight_id, 
                            id AS booking_id, 
                            user_name, 
                            email, 
                            weight AS weight_booked,  
                            total_price, 
                            status 
                          FROM cargo_bookings 
                          ORDER BY flight_id, booking_id";

        $history_result = $conn->query($history_query);

        if ($history_result->num_rows > 0) {
            echo "<table border='1'>
                    <tr>
                        <th>Flight ID</th>
                        <th>Booking ID</th>
                        <th>Passenger Name</th>
                        <th>Email</th>
                        <th>Weight Booked (kg)</th>
                        <th>Total Price (₹)</th>
                        <th>Status</th>
                    </tr>";

            while ($row = $history_result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['flight_id']}</td>
                        <td>{$row['booking_id']}</td>
                        <td>{$row['user_name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['weight_booked']}</td>  
                        <td>{$row['total_price']}</td>
                        <td>{$row['status']}</td>
                      </tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No cargo booking history found.</p>";
        }

        $conn->close();
        ?>
    </div>

</body>
</html>