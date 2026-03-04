<?php
include("adminnav.php");
include 'db.php'; // Database connection

// Handle type of bookings: active or cancelled
$type = isset($_GET['type']) ? $_GET['type'] : 'active';

// Handle pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 5;
$offset = ($page - 1) * $records_per_page;

if ($type === 'cancelled') {
    $total_query = "SELECT COUNT(*) FROM bookings WHERE status = 'Cancelled'";
    $data_query = "SELECT * FROM bookings WHERE status = 'Cancelled' ORDER BY id DESC LIMIT $offset, $records_per_page";
    $heading = "Cancelled Passenger Bookings";
} else {
    $total_query = "SELECT COUNT(*) FROM bookings WHERE status != 'Cancelled'";
    $data_query = "SELECT * FROM bookings WHERE status != 'Cancelled' ORDER BY id DESC LIMIT $offset, $records_per_page";
    $heading = "Active Passenger Bookings";
}

$total_result = $conn->query($total_query);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $records_per_page);

$data_result = $conn->query($data_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Bookings</title>
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
            margin-bottom: 40px; /* Matches cargo bookings page */
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

        .toggle-btn.active, .toggle-btn:hover:not(:disabled) {
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

        .delete-btn {
            background-color: red;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
        }

        .delete-btn:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <div class="container">
       

        <div class="toggle-buttons">
            <button class="toggle-btn <?= $type === 'active' ? 'active' : '' ?>" onclick="window.location.href='admin_passenger_bookings.php?type=active'">Active Bookings</button>
            <button class="toggle-btn <?= $type === 'cancelled' ? 'active' : '' ?>" onclick="window.location.href='admin_passenger_bookings.php?type=cancelled'">Cancelled Bookings</button>
        </div>

        <?php
        if ($data_result->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>ID</th>
                        <th>Flight ID</th>
                        <th>Seat Class</th>
                        <th>Seat Numbers</th>
                        <th>Total Price (₹)</th>
                        <th>Seat Count</th>
                        <th>Status</th>";
            if ($type === 'active') echo "<th>Action</th>";
            echo "</tr>";

            while ($row = $data_result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['id']) . "</td>
                        <td>" . htmlspecialchars($row['flight_id']) . "</td>
                        <td>" . htmlspecialchars($row['seat_class']) . "</td>
                        <td>" . htmlspecialchars($row['seat_numbers']) . "</td>
                        <td>" . htmlspecialchars($row['total_price']) . "</td>
                        <td>" . htmlspecialchars($row['seat_count']) . "</td>
                        <td" . ($row['status'] === 'Cancelled' ? " style='color:red; font-weight:bold;'" : "") . ">" . htmlspecialchars($row['status']) . "</td>";
                if ($type === 'active') {
                    echo "<td><a href='admin_passenger_bookings.php?id=" . urlencode($row['id']) . "&type=active&page=$page' class='delete-btn'>Delete</a></td>";
                }
                echo "</tr>";
            }

            echo "</table>";

            // Pagination
            echo "<div class='pagination'>";
            $prev_page = $page - 1;
            $next_page = $page + 1;
            echo "<button " . ($page <= 1 ? 'disabled' : '') . " onclick=\"window.location.href='admin_passenger_bookings.php?type=$type&page=$prev_page'\">Previous</button>";
            echo "<button " . ($page >= $total_pages ? 'disabled' : '') . " onclick=\"window.location.href='admin_passenger_bookings.php?type=$type&page=$next_page'\">Next</button>";
            echo "</div>";
        } else {
            echo "<h3>No " . strtolower(htmlspecialchars($heading)) . " found.</h3>";
        }
        ?>
    </div>
</body>
</html>

<?php
// DELETE LOGIC AT THE END
if (isset($_GET['id']) && $type === 'active') {
    $id = $_GET['id'];
    $delete_query = "DELETE FROM bookings WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Booking deleted successfully.'); window.location.href='admin_passenger_bookings.php?type=active&page=$page';</script>";
    } else {
        echo "<script>alert('Error deleting booking.'); window.location.href='admin_passenger_bookings.php?type=active&page=$page';</script>";
    }
    $stmt->close();
}

$conn->close();
?>