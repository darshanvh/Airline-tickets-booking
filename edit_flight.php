<?php
session_start();
include 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id'])) {
    echo "<script>alert('Invalid Flight ID'); window.location.href='manage_flight.php';</script>";
    exit;
}

$flight_id = $_GET['id'];

// Fetch flight details
$sql = "SELECT * FROM flights WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $flight_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$flight = mysqli_fetch_assoc($result);

if (!$flight) {
    echo "<script>alert('Flight not found'); window.location.href='manage_flight.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $flight_id = mysqli_real_escape_string($conn, $_POST['flight_id']);
    $flight_name = mysqli_real_escape_string($conn, $_POST['flight_name']);
    $source = mysqli_real_escape_string($conn, $_POST['source']);
    $destination = mysqli_real_escape_string($conn, $_POST['destination']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $departure_time = mysqli_real_escape_string($conn, $_POST['departure_time']);
    $arrival_time = mysqli_real_escape_string($conn, $_POST['arrival_time']);
    $economy_seats = mysqli_real_escape_string($conn, $_POST['economy_seats']);
    $business_seats = mysqli_real_escape_string($conn, $_POST['business_seats']);
    $economy_price = mysqli_real_escape_string($conn, $_POST['economy_price']);
    $business_price = mysqli_real_escape_string($conn, $_POST['business_price']);

    $update_sql = "UPDATE flights SET 
                    flight_name = ?, source = ?, destination = ?, date = ?, departure_time = ?, arrival_time = ?, 
                    economy_seats = ?, business_seats = ?, economy_price = ?, business_price = ?
                    WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "sssssssdddi", 
        $flight_name, $source, $destination, $date, $departure_time, $arrival_time, 
        $economy_seats, $business_seats, $economy_price, $business_price, $flight_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Flight details updated successfully!'); window.location.href='manage_flight.php';</script>";
    } else {
        echo "<script>alert('Error updating flight: " . mysqli_error($conn) . "');</script>";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Flight</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            min-height: 100vh;
        }

        /* Main content padding */
        .content-wrapper {
            padding: 20px;
            width: 100%;
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            margin-top: 20px; /* Add some top margin */
        }

        .form-container {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        label {
            margin-bottom: 5px;
            color: #555;
            font-size: 14px;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="time"],
        input[type="hidden"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        input[type="submit"] {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            grid-column: span 2;
        }

        input[type="submit"]:hover {
            background-color: #1a252f;
        }
    </style>
</head>
<body>
    <?php include 'adminnav.php'; ?>
    <!-- Content Box for Edit Flight -->
    <div class="content-wrapper">
        <div class="form-container">
            <h2>Edit Flight Details</h2>
            <form method="POST" action="">
                <div class="form-grid">
                    <input type="hidden" name="flight_id" value="<?php echo htmlspecialchars($flight['id']); ?>">
                    <div class="form-group">
                        <label for="flight_name">Flight Name</label>
                        <input type="text" name="flight_name" id="flight_name" value="<?php echo htmlspecialchars($flight['flight_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="source">Source</label>
                        <input type="text" name="source" id="source" value="<?php echo htmlspecialchars($flight['source']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="destination">Destination</label>
                        <input type="text" name="destination" id="destination" value="<?php echo htmlspecialchars($flight['destination']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($flight['date']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="departure_time">Departure Time</label>
                        <input type="time" name="departure_time" id="departure_time" value="<?php echo htmlspecialchars($flight['departure_time']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="arrival_time">Arrival Time</label>
                        <input type="time" name="arrival_time" id="arrival_time" value="<?php echo htmlspecialchars($flight['arrival_time']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="economy_seats">Economy Seats</label>
                        <input type="number" name="economy_seats" id="economy_seats" value="<?php echo htmlspecialchars($flight['economy_seats']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="business_seats">Business Seats</label>
                        <input type="number" name="business_seats" id="business_seats" value="<?php echo htmlspecialchars($flight['business_seats']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="economy_price">Economy Price (₹)</label>
                        <input type="number" step="0.01" name="economy_price" id="economy_price" value="<?php echo htmlspecialchars($flight['economy_price']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="business_price">Business Price (₹)</label>
                        <input type="number" step="0.01" name="business_price" id="business_price" value="<?php echo htmlspecialchars($flight['business_price']); ?>" required>
                    </div>
                    <input type="submit" value="Update Flight">
                </div>
            </form>
        </div>
    </div>
</body>
</html>