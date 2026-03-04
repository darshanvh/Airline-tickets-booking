<?php
session_start();
include("db.php"); // Database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>❗ Please login to access this page.</p>";
    exit();
}

$user_id = $_SESSION['user_id']; // Get logged-in user's ID

// Pagination variables
$records_per_page = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $records_per_page; // Offset for SQL query

// Fetch total number of bookings
$total_query = "SELECT COUNT(*) AS total FROM bookings WHERE user_id = '$user_id'";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total']; // Total number of bookings
$total_pages = ceil($total_records / $records_per_page); // Calculate total pages

// Fetch user bookings with pagination (latest first)
$bookings_query = "SELECT b.id AS booking_id, f.id AS flight_id, f.flight_name, f.source, f.destination, f.date, f.departure_time 
                   FROM bookings b 
                   JOIN flights f ON b.flight_id = f.id 
                   WHERE b.user_id = '$user_id' 
                   ORDER BY b.id DESC 
                   LIMIT $offset, $records_per_page";
$bookings_result = mysqli_query($conn, $bookings_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Bookings</title>
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
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .details-btn:hover {
            background-color: #2d9d5b;
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
            <h1 class="page-title">My Bookings</h1>
        </div>
    </header>

    <main class="container">
        <div class="flights-table">
            <?php if (mysqli_num_rows($bookings_result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Flight ID</th>
                            <th>Flight Name</th>
                            <th>Source</th>
                            <th>Destination</th>
                            <th>Travel Date</th>
                            <th>Departure Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = mysqli_fetch_assoc($bookings_result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($booking['flight_id']); ?></td>
                                <td><?= htmlspecialchars($booking['flight_name']); ?></td>
                                <td><?= htmlspecialchars($booking['source']); ?></td>
                                <td><?= htmlspecialchars($booking['destination']); ?></td>
                                <td><?= htmlspecialchars($booking['date']); ?></td>
                                <td><?= htmlspecialchars($booking['departure_time']); ?></td>
                                <td>
                                    <a class="details-btn" href="booking_details.php?booking_id=<?= $booking['booking_id']; ?>">Details</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center" style="padding: 2rem;">No bookings found.</p>
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

    <script>
        // Add animation to table rows
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.flights-table tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, 100 + (index * 50));
            });
        });
    </script>
</body>
</html>
