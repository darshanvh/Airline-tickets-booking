<?php
include("adminnav.php");
include("db.php");

// Pagination setup
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$cargo_page = isset($_GET['cargo_page']) ? (int)$_GET['cargo_page'] : 1;

$start = ($page - 1) * $limit;
$cargo_start = ($cargo_page - 1) * $limit;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Flights Info</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            
            background-color: #f4f4f4;
        }

        h2 {
            text-align: center;
        }

        .container {
            width: 90%;
            margin: auto;
        }

        .toggle-buttons {
            text-align: center;
            margin-bottom: 40px;
        }

        .toggle-btn {
            padding: 10px 20px;
            margin: 20px 20px;
            background-color: #2c3e50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }

        .toggle-btn:hover {
            background-color: #34495e;
        }

        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .pagination button {
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #2c3e50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .pagination button:disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
        }

        .pagination button:hover:not(:disabled) {
            background-color: #34495e;
        }

        .no-data {
            color: red;
            text-align: center;
            margin: 20px 0;
        }

        a {
            color: #2c3e50;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function showSection(sectionId) {
            document.getElementById("passengerSection").style.display = (sectionId === 'passenger') ? "block" : "none";
            document.getElementById("cargoSection").style.display = (sectionId === 'cargo') ? "block" : "none";
        }

        window.onload = function () {
            showSection('passenger'); // Default section
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="toggle-buttons">
            <button class="toggle-btn" onclick="showSection('passenger')">Passenger Flights</button>
            <button class="toggle-btn" onclick="showSection('cargo')">Cargo Flights</button>
        </div>

        <!-- Passenger Flights -->
        <div id="passengerSection">
           
            <?php
            $total_passenger_query = "SELECT COUNT(*) AS total FROM flights WHERE status='Active'";
            $total_passenger_result = mysqli_query($conn, $total_passenger_query);
            $total_passenger = mysqli_fetch_assoc($total_passenger_result)['total'];
            $total_pages = ceil($total_passenger / $limit);

            $passenger_query = "SELECT * FROM flights WHERE status='Active' LIMIT $start, $limit";
            $passenger_result = mysqli_query($conn, $passenger_query); // Fixed: Use $passenger_query

            if (mysqli_num_rows($passenger_result) > 0) {
                echo "<table>
                        <tr>
                            <th>Flight ID</th>
                            <th>Flight Name</th>
                            <th>Source</th>
                            <th>Destination</th>
                            <th>Date</th>
                            <th>Available Seats</th>
                            <th>Actions</th>
                        </tr>";

                while ($row = mysqli_fetch_assoc($passenger_result)) {
                    // Safeguard against missing keys
                    $economy_seats = isset($row['economy_seats']) ? (int)$row['economy_seats'] : 0;
                    $business_seats = isset($row['business_seats']) ? (int)$row['business_seats'] : 0;
                    $available = $economy_seats + $business_seats;
                    $flight_id = isset($row['flight_id']) ? htmlspecialchars($row['flight_id']) : 'N/A';
                    $flight_name = isset($row['flight_name']) ? htmlspecialchars($row['flight_name']) : 'N/A';
                    $source = isset($row['source']) ? htmlspecialchars($row['source']) : 'N/A';
                    $destination = isset($row['destination']) ? htmlspecialchars($row['destination']) : 'N/A';
                    $date = isset($row['date']) ? htmlspecialchars($row['date']) : 'N/A';
                    $id = isset($row['id']) ? urlencode($row['id']) : '';

                    echo "<tr>
                            <td>$flight_id</td>
                            <td>$flight_name</td>
                            <td>$source</td>
                            <td>$destination</td>
                            <td>$date</td>
                            <td>$available</td>
                            <td><a href='flight_details.php?id=$id'>View Details</a></td>
                        </tr>";
                }
                echo "</table>";

                echo "<div class='pagination'>";
                echo "<button " . ($page <= 1 ? 'disabled' : '') . " onclick=\"window.location.href='?page=" . ($page - 1) . "&cargo_page=$cargo_page'\">Previous</button>";
                echo "<button " . ($page >= $total_pages ? 'disabled' : '') . " onclick=\"window.location.href='?page=" . ($page + 1) . "&cargo_page=$cargo_page'\">Next</button>";
                echo "</div>";
            } else {
                echo "<p class='no-data'>No available passenger flights.</p>";
            }
            ?>
        </div>

        <!-- Cargo Flights -->
        <div id="cargoSection" style="display: none;">
            
            <?php
            $total_cargo_query = "SELECT COUNT(*) AS total FROM cargo_flights WHERE status='Active' AND available_weight > 0";
            $total_cargo_result = mysqli_query($conn, $total_cargo_query);
            $total_cargo = mysqli_fetch_assoc($total_cargo_result)['total'];
            $cargo_total_pages = ceil($total_cargo / $limit);

            $cargo_query = "SELECT * FROM cargo_flights WHERE status='Active' AND available_weight > 0 LIMIT $cargo_start, $limit";
            $cargo_result = mysqli_query($conn, $cargo_query);

            if (mysqli_num_rows($cargo_result) > 0) {
                echo "<table>
                        <tr>
                            <th>Flight ID</th>
                            <th>Flight Name</th>
                            <th>Source</th>
                            <th>Destination</th>
                            <th>Date</th>
                            <th>Available Cargo Weight (kg)</th>
                            <th>Actions</th>
                        </tr>";

                while ($row = mysqli_fetch_assoc($cargo_result)) {
                    // Safeguard against missing keys
                    $flight_id = isset($row['flight_id']) ? htmlspecialchars($row['flight_id']) : 'N/A';
                    $flight_name = isset($row['flight_name']) ? htmlspecialchars($row['flight_name']) : 'N/A';
                    $source = isset($row['source']) ? htmlspecialchars($row['source']) : 'N/A';
                    $destination = isset($row['destination']) ? htmlspecialchars($row['destination']) : 'N/A';
                    $date = isset($row['date']) ? htmlspecialchars($row['date']) : 'N/A';
                    $available_weight = isset($row['available_weight']) ? htmlspecialchars($row['available_weight']) : '0';
                    $id = isset($row['id']) ? urlencode($row['id']) : '';

                    echo "<tr>
                            <td>$flight_id</td>
                            <td>$flight_name</td>
                            <td>$source</td>
                            <td>$destination</td>
                            <td>$date</td>
                            <td>$available_weight kg</td>
                            <td><a href='cargo_details.php?id=$id'>View Details</a></td>
                        </tr>";
                }
                echo "</table>";

                echo "<div class='pagination'>";
                echo "<button " . ($cargo_page <= 1 ? 'disabled' : '') . " onclick=\"window.location.href='?page=$page&cargo_page=" . ($cargo_page - 1) . "'\">Previous</button>";
                echo "<button " . ($cargo_page >= $cargo_total_pages ? 'disabled' : '') . " onclick=\"window.location.href='?page=$page&cargo_page=" . ($cargo_page + 1) . "'\">Next</button>";
                echo "</div>";
            } else {
                echo "<p class='no-data'>No available cargo flights.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>