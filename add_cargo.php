<?php
session_start();
include("db.php"); // Database connection
include("adminnav.php");

// Ensure admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $flight_name = mysqli_real_escape_string($conn, $_POST['flight_name']);
    $flight_id = mysqli_real_escape_string($conn, $_POST['flight_id']);
    $source = mysqli_real_escape_string($conn, $_POST['source']);
    $destination = mysqli_real_escape_string($conn, $_POST['destination']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $departure_time = mysqli_real_escape_string($conn, $_POST['departure_time']);
    $arrival_time = mysqli_real_escape_string($conn, $_POST['arrival_time']);
    $price_per_kg = mysqli_real_escape_string($conn, $_POST['price_per_kg']);
    $total_weight = mysqli_real_escape_string($conn, $_POST['total_weight']);

    // Initially, available weight = total weight
    $available_weight = $total_weight;

    // Check for duplicate flight_id
    $check_query = "SELECT * FROM cargo_flights WHERE flight_id = '$flight_id'";
    $result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Flight ID already exists. Please use a different ID.'); window.location='add_cargo.php';</script>";
        exit();
    }

    $query = "INSERT INTO cargo_flights (flight_name, flight_id, source, destination, date, departure_time, arrival_time, price_per_kg, total_weight, available_weight, status)
              VALUES ('$flight_name', '$flight_id', '$source', '$destination', '$date', '$departure_time', '$arrival_time', '$price_per_kg', '$total_weight', '$available_weight', 'Active')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Cargo flight added successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Cargo Flight</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            background-color: #f4f4f4;
            min-height: 100vh;
        }

        /* Main content padding */
        .content-wrapper {
            padding: 20px;
            width: 80%;
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
            margin-left: 150px; /* Matches Add Flight form position */
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
        input[type="time"] {
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
    <!-- Content Box for Add Cargo Flight -->
    <div class="content-wrapper">
        <div class="form-container">
            <h2>Add Cargo Flight</h2>
            <form method="POST" action="add_cargo.php">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="flight_name">Flight Name</label>
                        <input type="text" name="flight_name" id="flight_name" required>
                    </div>
                    <div class="form-group">
                        <label for="flight_id">Flight ID</label>
                        <input type="text" name="flight_id" id="flight_id" required>
                    </div>
                    <div class="form-group">
                        <label for="source">Source</label>
                        <input type="text" name="source" id="source" required>
                    </div>
                    <div class="form-group">
                        <label for="destination">Destination</label>
                        <input type="text" name="destination" id="destination" required>
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" name="date" id="date" required>
                    </div>
                    <div class="form-group">
                        <label for="departure_time">Departure Time</label>
                        <input type="time" name="departure_time" id="departure_time" required>
                    </div>
                    <div class="form-group">
                        <label for="arrival_time">Arrival Time</label>
                        <input type="time" name="arrival_time" id="arrival_time" required>
                    </div>
                    <div class="form-group">
                        <label for="price_per_kg">Price per KG</label>
                        <input type="number" name="price_per_kg" id="price_per_kg" required>
                    </div>
                    <div class="form-group">
                        <label for="total_weight">Total Weight (KG)</label>
                        <input type="number" name="total_weight" id="total_weight" required>
                    </div>
                    <input type="submit" value="Add Cargo Flight">
                </div>
            </form>
        </div>
    </div>
</body>
</html>