<?php
include('db.php'); // Connect to the database

$records_per_page = 10; // Number of flights per page

// Get the current page number, default is 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure page number is at least 1

$offset = ($page - 1) * $records_per_page; // Calculate offset for SQL query

// Fetch total flight count
$totalQuery = "SELECT COUNT(*) AS total FROM flights 
               WHERE status = 'Active' AND date > CURDATE()";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$total_records = $totalRow['total'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch upcoming passenger flights with pagination
$passengerQuery = "SELECT * FROM flights 
                   WHERE status = 'Active' AND date > CURDATE() 
                   ORDER BY date ASC LIMIT $offset, $records_per_page";
$passengerResult = mysqli_query($conn, $passengerQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Passenger Flights</title>
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
            <h1 class="page-title">Upcoming Passenger Flights</h1>
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
                    <?php while ($row = mysqli_fetch_assoc($passengerResult)) { ?>
                    <tr>
                        <td><?php echo $row['flight_name']; ?></td>
                     
                        <td>
                            <div class="route-info">
                                <div class="airport-name"><?php echo $row['source']; ?></div>
                                <div class="route-arrow"><i class="fas fa-plane"></i></div>
                                <div class="airport-name"><?php echo $row['destination']; ?></div>
                            </div>
                        </td>

<style>
    .route-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding: 8px 0;
        align-items: center;
        text-align: center;
    }

    .airport-name {
        font-weight: 500;
        color: var(--dark);
        width: 100%;
    }

    .route-arrow {
        color: var(--primary);
        font-size: 1.2rem;
        margin: 8px 0;
        display: flex;
        justify-content: center;
    }

    .route-arrow i {
        transform: rotate(90deg);
    }
</style>
                        <td><?php echo date('d M Y', strtotime($row['date'])); ?></td>
                        <td>
                            <div><?php echo date('H:i', strtotime($row['departure_time'])); ?> - <?php echo date('H:i', strtotime($row['arrival_time'])); ?></div>
                        </td>
                       
                        <td>
                            <div class="price">Economy: ₹<?php echo number_format($row['economy_price']); ?></div>
                            <div class="price">Business: ₹<?php echo number_format($row['business_price']); ?></div>
                        </td>
                        <td class="text-center">
                            <a class="book-button" href="search_flight.php?flight_id=<?php echo $row['flight_id']; ?>&source=<?php echo urlencode($row['source']); ?>&destination=<?php echo urlencode($row['destination']); ?>&date=<?php echo $row['date']; ?>">
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
            
            <span class="pagination-text">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
            
            <?php if ($page < $total_pages) { ?>
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