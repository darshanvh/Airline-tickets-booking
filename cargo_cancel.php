<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login to access this page.'); window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Get total records count
$total_query = "SELECT COUNT(*) AS total 
                FROM cargo_bookings cb 
                JOIN cargo_flights cf ON cb.flight_id = cf.flight_id 
                WHERE cb.user_id = ? 
                AND cf.date >= CURDATE()
                AND (CONCAT(cf.date, ' ', cf.departure_time) >= NOW() OR cb.status = 'Confirmed')";

// Fetch cargo bookings with pagination
$query = "SELECT 
            cb.id AS booking_id,
            cb.booking_id AS cargo_booking_number,
            cf.flight_name,
            cf.flight_id AS flight_number,
            cf.source,
            cf.destination,
            cf.date,
            cf.departure_time,
            cb.weight AS weight,
            cb.total_price,
            cb.status,
            cb.user_name,
            cb.user_address as address
          FROM cargo_bookings cb
          JOIN cargo_flights cf ON cb.flight_id = cf.flight_id
          WHERE cb.user_id = ?
          AND cf.date >= CURDATE()
          AND (CONCAT(cf.date, ' ', cf.departure_time) >= NOW() OR cb.status = 'Confirmed')
          ORDER BY cf.date ASC, cf.departure_time ASC, cb.id DESC 
          LIMIT ?, ?";

$stmt = $conn->prepare($total_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_result = $stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];

// Calculate total pages
$total_pages = ceil($total_records / $limit);

// Fetch cargo bookings with pagination
$query = "SELECT 
            cb.id AS booking_id,
            cb.booking_id AS cargo_booking_number,
            cf.flight_name,
            cf.flight_id AS flight_number,
            cf.source,
            cf.destination,
            cf.date,
            cf.departure_time,
            cb.weight AS weight,
            cb.total_price,
            cb.status,
            cb.user_name,
            cb.user_address as address
          FROM cargo_bookings cb
          JOIN cargo_flights cf ON cb.flight_id = cf.flight_id
          WHERE cb.user_id = ?
          AND cf.date >= CURDATE()
          AND (CONCAT(cf.date, ' ', cf.departure_time) >= NOW() OR cb.status = 'Confirmed')
          ORDER BY cf.date ASC, cf.departure_time ASC, cb.id DESC 
          LIMIT ?, ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $start, $limit);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Cargo Booking</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Copy all styles from booking_details.php */
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
        
        .cancel-button {
            background-color: var(--danger);
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
        
        .cancel-button:hover {
            background-color: #cc1f1a;
        }
        
        .cancel-button:disabled {
            background-color: var(--gray);
            cursor: not-allowed;
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
        
        .modal {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            max-width: 400px;
            margin: 15% auto;
            padding: 2rem;
            text-align: center;
        }
        
        .modal-content p {
            margin-bottom: 1.5rem;
            color: var(--dark);
        }
        
        .modal-btn {
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            border: none;
            margin: 0 0.5rem;
        }
        
        .yes-btn {
            background-color: var(--danger);
            color: white;
        }
        
        .yes-btn:hover {
            background-color: #cc1f1a;
        }
        
        .no-btn {
            background-color: var(--gray);
            color: white;
        }
        
        .no-btn:hover {
            background-color: #8795a1;
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
            <?php if (mysqli_num_rows($result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Flight Name</th>
                            <th>Source</th>
                            <th>Destination</th>
                            <th>Date</th>
                            <th>Departure</th>
                            <th>Weight (kg)</th>
                            
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['flight_name']) ?></td>
                                <td><?= htmlspecialchars($row['source']) ?></td>
                                <td><?= htmlspecialchars($row['destination']) ?></td>
                                <td><?= date('d M Y', strtotime($row['date'])) ?></td>
                                <td><?= date('H:i', strtotime($row['departure_time'])) ?></td>
                                <td><?= htmlspecialchars($row['weight']) ?> kg</td>
                               
                                <td>₹<?= number_format($row['total_price'], 2) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td>
                                    <?php if ($row['status'] == 'Confirmed'): ?>
                                        <button class="cancel-button" onclick="openModal(<?= $row['booking_id'] ?>)">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    <?php else: ?>
                                        <button class="cancel-button" disabled>Cancelled</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center" style="padding: 2rem;">No cargo bookings found.</p>
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

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to cancel this cargo booking? This action cannot be undone.</p>
            <form id="cancelForm" method="POST" action="process_cargo_cancel.php">
                <input type="hidden" id="booking_id" name="booking_id" value="">
                <button type="submit" class="modal-btn yes-btn" name="cancel_booking">Yes, Cancel</button>
                <button type="button" class="modal-btn no-btn" onclick="closeModal()">No, Keep</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(bookingId) {
            document.getElementById('booking_id').value = bookingId;
            document.getElementById('myModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('myModal').style.display = 'none';
        }

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
