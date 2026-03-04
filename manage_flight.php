<?php
session_start();
include 'db.php';
include 'adminnav.php'; 

// Ensure admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Pagination setup
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Handle flight cancellation if requested (retain original URL structure from the second document)
// Original code used cancel_flight.php, so we'll keep that structure

// Count total records for pagination
$total_query = "SELECT COUNT(*) AS total FROM flights";
$total_result = mysqli_query($conn, $total_query);
$total_rows = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch flights for current page
$sql = "SELECT * FROM flights ORDER BY date ASC LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);

// Get the current script name for pagination URLs
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Flights</title>
    <style>
        * { box-sizing: border-box; }

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
            margin: 60px auto;
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

        .pagination a {
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #2c3e50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
        }

        .pagination .disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
            pointer-events: none;
        }

        .pagination a:hover:not(.disabled) {
            background-color: #34495e;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            color: white;
            border-radius: 4px;
            font-size: 14px;
            margin: 2px;
            display: inline-block;
        }

        .cancel-btn { background-color: #dc3545; }
        .cancel-btn:hover { background-color: #c82333; }
        .edit-btn { background-color: #fd7e14; }
        .edit-btn:hover { background-color: #e06b12; }
        .passenger-btn { background-color: #007bff; }
        .passenger-btn:hover { background-color: #0056b3; }
        .disabled-btn {
            background-color: #95a5a6 !important;
            cursor: not-allowed;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            tr {
                margin-bottom: 15px;
            }
            td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                width: 45%;
                padding-left: 10px;
                font-weight: bold;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        

        <?php if (mysqli_num_rows($result) > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Flight ID</th>
                        <th>Flight Name</th>
                        <th>Source</th>
                        <th>Destination</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td data-label="Flight ID"><?php echo htmlspecialchars($row['id']); ?></td>
                            <td data-label="Flight Name"><?php echo htmlspecialchars($row['flight_name']); ?></td>
                            <td data-label="Source"><?php echo htmlspecialchars($row['source']); ?></td>
                            <td data-label="Destination"><?php echo htmlspecialchars($row['destination']); ?></td>
                            <td data-label="Date"><?php echo htmlspecialchars($row['date']); ?></td>
                            <td data-label="Status" style="color: <?php echo ($row['status'] == 'Cancelled') ? 'red' : 'green'; ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </td>
                            <td data-label="Actions">
                                <?php if ($row['status'] == 'Cancelled') { ?>
                                    <span class="btn disabled-btn">Cancel</span>
                                    <span class="btn disabled-btn">Edit</span>
                                <?php } else { ?>
                                    <a href="cancel_flight.php?id=<?php echo urlencode($row['id']); ?>&page=<?php echo $page; ?>" class="btn cancel-btn" onclick="return confirm('Are you sure you want to cancel this flight?');">Cancel</a>
                                    <a href="edit_flight.php?id=<?php echo urlencode($row['id']); ?>" class="btn edit-btn">Edit</a>
                                <?php } ?>
                                <a href="passenger_list.php?flight_id=<?php echo urlencode($row['id']); ?>" class="btn passenger-btn">Passengers</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Pagination using links instead of buttons to match the original structure -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="<?php echo $current_page; ?>?page=<?php echo ($page - 1); ?>">Previous</a>
                <?php else: ?>
                    <a class="disabled">Previous</a>
                <?php endif; ?>
                
     
                
                <?php if ($page < $total_pages): ?>
                    <a href="<?php echo $current_page; ?>?page=<?php echo ($page + 1); ?>">Next</a>
                <?php else: ?>
                    <a class="disabled">Next</a>
                <?php endif; ?>
            </div>
        <?php } else { ?>
            <p style="text-align: center; color: red; margin: 20px 0;">No flights available.</p>
        <?php } ?>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?>