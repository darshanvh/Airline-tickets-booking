<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Fetch total records count
$total_query = "SELECT COUNT(*) AS total FROM cargo_bookings WHERE user_id = ?";
$total_stmt = mysqli_prepare($conn, $total_query);
mysqli_stmt_bind_param($total_stmt, "i", $user_id);
mysqli_stmt_execute($total_stmt);
$total_result = mysqli_stmt_get_result($total_stmt);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch cargo bookings with limit and offset
$query = "SELECT cb.id AS booking_id, cb.flight_id, cb.user_name, cb.email, cb.weight AS weight_booked, 
                 cb.total_price, cb.status, cb.aadhar_number, 
                 cf.source, cf.destination, cf.date, cf.departure_time, cf.arrival_time
          FROM cargo_bookings cb
          JOIN cargo_flights cf ON cb.flight_id = cf.flight_id
          WHERE cb.user_id = ?
          ORDER BY cb.id DESC  -- Sorting by latest booking first
          LIMIT ?, ?";


$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iii", $user_id, $offset, $records_per_page);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cargo Bookings</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #3490dc;
            --primary-dark: #2779bd;
            --secondary: #f6993f;
            --light: #f8fafc;
            --dark: #2d3748;
            --success: #38c172;
            --danger: #e3342f;
            --gray: #b8c2cc;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', 'Roboto', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: #f0f5fa;
        }
        
        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
            position: relative;
        }
        
        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .home-button {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        
        .home-button i {
            margin-right: 8px;
        }
        
        .home-button:hover {
            background-color: var(--primary-dark);
        }
        
        .page-header {
            background-color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }
        
        .flights-table {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }
        
        .flights-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .flights-table th {
            background-color: #f8fafc;
            color: var(--dark);
            font-weight: 600;
            text-align: left;
            padding: 1rem;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }
        
        .flights-table td {
            padding: 1rem;
            border-bottom: 1px solid #edf2f7;
            color: #4a5568;
        }
        
        .flights-table tr:hover {
            background-color: #f8fafc;
        }
        
        .flights-table tr:last-child td {
            border-bottom: none;
        }
        
        .details-btn {
            background-color: var(--success);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .details-btn:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }
        
        .details-btn:hover:before {
            width: 200px;
            height: 200px;
        }
        
        .details-btn:hover {
            background-color: #2d9d5b;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(45, 157, 91, 0.2);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin: 2rem 0;
            align-items: center;
        }
        
        .pagination-item {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            margin: 0 0.25rem;
            border-radius: 50%;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .pagination-item:hover:not(.disabled) {
            background-color: #edf2f7;
        }
        
        .pagination-item.disabled {
            color: var(--gray);
            pointer-events: none;
        }
        
        .pagination-text {
            margin: 0 1rem;
            font-weight: 500;
        }
        
        @media (max-width: 1200px) {
            .flights-table {
                overflow-x: auto;
                display: block;
            }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .pagination-text {
                display: none;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container navbar-content">
            <div class="logo">AirBooking</div>
            <div class="nav-links">
                <a href="user_dashboard.php" class="home-button">
                    <i class="fas fa-home"></i> Home
                </a>
            </div>
        </div>
    </nav>

    <header class="page-header">
        <div class="container">
            <h1 class="page-title">My Cargo Bookings</h1>
        </div>
    </header>

    <main class="container">
        <div class="flights-table">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                           
                            <th>Flight ID</th>
                            <th>Source</th>
                            <th>Destination</th>
                            <th>Date</th>
                            <th>Departure Time</th>
                            <th>Arrival Time</th>
                           
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                               
                                <td><?= $row['flight_id'] ?></td>
                                <td><?= $row['source'] ?></td>
                                <td><?= $row['destination'] ?></td>
                                <td><?= $row['date'] ?></td>
                                <td><?= $row['departure_time'] ?></td>
                                <td><?= $row['arrival_time'] ?></td>
                               
                                <td>
                                    <a href="cdb.php?booking_id=<?= $row['booking_id'] ?>" class="details-btn">Details</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 2rem;">No cargo booking history found.</p>
            <?php endif; ?>
        </div>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="pagination-item">
                    <i class="fas fa-chevron-left"></i>
                </a>
            <?php else: ?>
                <span class="pagination-item disabled">
                    <i class="fas fa-chevron-left"></i>
                </span>
            <?php endif; ?>

            <span class="pagination-text">Page <?= $page ?> of <?= $total_pages ?></span>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>" class="pagination-item">
                    <i class="fas fa-chevron-right"></i>
                </a>
            <?php else: ?>
                <span class="pagination-item disabled">
                    <i class="fas fa-chevron-right"></i>
                </span>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

<?php
mysqli_stmt_close($stmt);
mysqli_stmt_close($total_stmt);
$conn->close();
?>
