<?php
include("adminnav.php");
include 'db.php'; // Database connection

// DELETE FUNCTIONALITY
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM cargo_bookings WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Cargo booking deleted successfully!'); window.location='admin_cargo_bookings.php?view=" . (isset($_GET['view']) ? $_GET['view'] : 'confirmed') . "&page=" . (isset($_GET['page']) ? $_GET['page'] : '1') . "';</script>";
    } else {
        echo "<script>alert('Error deleting cargo booking.'); window.location='admin_cargo_bookings.php?view=" . (isset($_GET['view']) ? $_GET['view'] : 'confirmed') . "&page=" . (isset($_GET['page']) ? $_GET['page'] : '1') . "';</script>";
    }

    $stmt->close();
}

// Determine which bookings to show (confirmed or canceled)
$view = isset($_GET['view']) && in_array($_GET['view'], ['confirmed', 'canceled']) ? $_GET['view'] : 'confirmed';

// Pagination settings
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total records for the selected view
$count_query = "SELECT COUNT(*) AS total FROM cargo_bookings WHERE status = ?";
$stmt = $conn->prepare($count_query);
$status = $view === 'confirmed' ? 'Confirmed' : 'Cancelled';
$stmt->bind_param("s", $status);
$stmt->execute();
$count_result = $stmt->get_result();
$count_row = $count_result->fetch_assoc();
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $limit);
$stmt->close();

// Fetch bookings for the selected view
$query = "SELECT * FROM cargo_bookings WHERE status = ? ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sii", $status, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargo Bookings</title>
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
            margin-bottom: 40px; /* Increased from 20px to move buttons down */
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
            <button class="toggle-btn <?php echo $view === 'confirmed' ? 'active' : ''; ?>" onclick="window.location.href='admin_cargo_bookings.php?view=confirmed'">Confirmed Cargo Bookings</button>
            <button class="toggle-btn <?php echo $view === 'canceled' ? 'active' : ''; ?>" onclick="window.location.href='admin_cargo_bookings.php?view=canceled'">Canceled Cargo Bookings</button>
        </div>

        <?php
        if ($result->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>ID</th>
                        <th>User Name</th>
                        <th>Flight ID</th>
                        <th>Weight (kg)</th>
                        <th>Total Price (₹)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['id']) . "</td>
                        <td>" . htmlspecialchars($row['user_name']) . "</td>
                        <td>" . htmlspecialchars($row['flight_id']) . "</td>
                        <td>" . htmlspecialchars($row['weight']) . "</td>
                        <td>" . htmlspecialchars($row['total_price']) . "</td>
                        <td>" . htmlspecialchars($row['status']) . "</td>
                        <td><a href='admin_cargo_bookings.php?delete_id=" . urlencode($row['id']) . "&view=$view&page=$page' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this booking?\");'>Delete</a></td>
                      </tr>";
            }

            echo "</table>";
        } else {
            echo "<h3>No " . ($view === 'confirmed' ? 'confirmed' : 'canceled') . " bookings found.</h3>";
        }
        ?>

        <div class="pagination">
            <button <?php echo $page <= 1 ? 'disabled' : ''; ?> onclick="window.location.href='admin_cargo_bookings.php?view=<?php echo $view; ?>&page=<?php echo $page - 1; ?>'">Previous</button>
            <button <?php echo $page >= $total_pages ? 'disabled' : ''; ?> onclick="window.location.href='admin_cargo_bookings.php?view=<?php echo $view; ?>&page=<?php echo $page + 1; ?>'">Next</button>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>