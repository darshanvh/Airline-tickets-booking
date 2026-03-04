<?php
include 'adminnav.php';
include 'db.php'; // Include database connection

// Check if flight_id is provided
if (!isset($_GET['flight_id']) || empty($_GET['flight_id'])) {
    echo "<div class='container'><h2>Invalid Flight ID.</h2></div>";
    exit;
}

$flight_id = intval($_GET['flight_id']); // Get the flight ID securely

// Pagination setup
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Count total records for pagination
$total_query = "SELECT COUNT(*) AS total FROM cargo_bookings WHERE flight_id = ?";
$total_stmt = $conn->prepare($total_query);
$total_stmt->bind_param("i", $flight_id);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);
$total_stmt->close();

// Query to fetch cargo bookings for the selected flight ID
$query = "SELECT 
            cb.id AS booking_id, 
            cb.user_name, 
            cb.email, 
            cb.weight, 
            cb.total_price, 
            cb.status 
          FROM cargo_bookings cb 
          WHERE cb.flight_id = ? 
          ORDER BY cb.booking_id 
          LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $flight_id, $limit, $start);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargo Passengers for Flight ID: <?php echo htmlspecialchars($flight_id); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            
            background-color: #f4f4f4;
        }

        .container {
            width: 90%;
            margin: auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
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
            font-size: 15px;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        td {
            background-color: #fdfdfd;
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

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .no-data {
            color: red;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cargo Passengers for Flight ID: <?php echo htmlspecialchars($flight_id); ?></h2>

        <?php if ($result->num_rows > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Passenger Name</th>
                        <th>Email</th>
                        <th>Weight Booked (kg)</th>
                        <th>Total Price (₹)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['weight']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_price']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><a href="admin_cdb.php?booking_id=<?php echo urlencode($row['booking_id']); ?>">View Details</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="pagination">
                <button <?php echo $page <= 1 ? 'disabled' : ''; ?> onclick="window.location.href='cargo_passenger_list.php?flight_id=<?php echo urlencode($flight_id); ?>&page=<?php echo $page - 1; ?>'">Previous</button>
                <button <?php echo $page >= $total_pages ? 'disabled' : ''; ?> onclick="window.location.href='cargo_passenger_list.php?flight_id=<?php echo urlencode($flight_id); ?>&page=<?php echo $page + 1; ?>'">Next</button>
            </div>
        <?php } else { ?>
            <p class="no-data">No cargo passengers found for Flight ID: <?php echo htmlspecialchars($flight_id); ?></p>
        <?php } ?>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>