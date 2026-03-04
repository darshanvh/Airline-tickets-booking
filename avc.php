<?php
include('db.php');

$recordsPerPage = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = max($page, 1);
$startFrom = ($page - 1) * $recordsPerPage;

$totalQuery = "SELECT COUNT(*) as total FROM cargo_flights WHERE status = 'Active' AND date >= CURDATE()";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

$cargoQuery = "SELECT cf.*, 
    (cf.total_weight - IFNULL((
        SELECT SUM(weight) 
        FROM cargo_bookings 
        WHERE flight_id = cf.flight_id 
        AND status = 'Confirmed'
    ), 0)) as available_weight 
    FROM cargo_flights cf 
    WHERE cf.status = 'Active' 
    AND cf.date >= CURDATE() 
    ORDER BY cf.date ASC 
    LIMIT $startFrom, $recordsPerPage";

$cargoResult = mysqli_query($conn, $cargoQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Cargo Flights</title>
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
        
        .nav-links a {
            color: var(--dark);
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
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
        
        .book-button {
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
        
        .book-button:hover {
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
        
        .pagination-item:hover:not(.current) {
            background-color: #edf2f7;
        }
        
        .pagination-item.current {
            background-color: var(--primary);
            color: white;
            pointer-events: none;
        }
        
        .pagination-item.disabled {
            color: var(--gray);
            pointer-events: none;
        }
        
        .pagination-text {
            margin: 0 1rem;
            font-weight: 500;
        }
        
        .price {
            font-weight: 600;
            color: #2d3748;
        }
        
        .text-center {
            text-align: center;
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
            <h1 class="page-title">Available Cargo Flights</h1>
        </div>
    </header>

    <main class="container">
        <div class="flights-table">
            <table>
                <thead>
                    <tr>
                        <th>Flight Name</th>
                        <th>Route</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Price (₹)</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($cargoResult)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['flight_name']); ?></td>
                        <td>
                            <div class="route-info">
                                <div class="airport-name"><?php echo htmlspecialchars($row['source']); ?></div>
                                <div class="route-arrow"><i class="fas fa-plane-departure"></i></div>
                                <div class="airport-name"><?php echo htmlspecialchars($row['destination']); ?></div>
                            </div>
                        </td>
                        <td><?php echo date('d M Y', strtotime($row['date'])); ?></td>
                        <td>
                            <div class="time-info"><?php echo date('H:i', strtotime($row['departure_time'])); ?> - <?php echo date('H:i', strtotime($row['arrival_time'])); ?></div>
                        </td>
                        <td>
                            <div class="price">Price per kg: ₹<?php echo number_format($row['price_per_kg']); ?></div>
                            <div class="price">Available: <?php echo number_format($row['available_weight']); ?> kg</div>
                        </td>
                        <td class="text-center">
                            <a class="book-button" href="book_cargo.php?flight_id=<?php echo $row['flight_id']; ?>&source=<?php echo urlencode($row['source']); ?>&destination=<?php echo urlencode($row['destination']); ?>&date=<?php echo urlencode($row['date']); ?>">
                                Book Now
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <?php if ($page > 1) { ?>
                <a href="?page=<?php echo ($page - 1); ?>" class="pagination-item">
                    <i class="fas fa-chevron-left"></i>
                </a>
            <?php } else { ?>
                <span class="pagination-item disabled">
                    <i class="fas fa-chevron-left"></i>
                </span>
            <?php } ?>
            
            <span class="pagination-text">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
            
            <?php if ($page < $totalPages) { ?>
                <a href="?page=<?php echo ($page + 1); ?>" class="pagination-item">
                    <i class="fas fa-chevron-right"></i>
                </a>
            <?php } else { ?>
                <span class="pagination-item disabled">
                    <i class="fas fa-chevron-right"></i>
                </span>
            <?php } ?>
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
