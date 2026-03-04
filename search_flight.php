<?php
include 'db.php'; // Database connection
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch unique sources and destinations from flights table
$sources = mysqli_query($conn, "SELECT DISTINCT source FROM flights WHERE status='Active'");
$destinations = mysqli_query($conn, "SELECT DISTINCT destination FROM flights WHERE status='Active'");

// Check if values are provided via GET request
$selected_source = isset($_GET['source']) ? $_GET['source'] : '';
$selected_destination = isset($_GET['destination']) ? $_GET['destination'] : '';
$selected_date = isset($_GET['date']) ? $_GET['date'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $date = $_POST['date'];

    // Fetch available flights (only future dates)
    $query = "SELECT * FROM flights 
              WHERE source='$source' 
              AND destination='$destination' 
              AND date='$date' 
              AND date > CURDATE() 
              AND status='Active'";
    $result = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Flights</title>
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
            min-height: 100vh;
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
        
        .search-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .search-form {
            display: flex;
            flex-wrap: wrap;
            margin: -0.5rem;
        }
        
        .form-group {
            flex: 1 1 280px;
            margin: 0.5rem;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            line-height: 1.5;
            color: var(--dark);
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #e2e8f0;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
        }
        
        .search-button {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.25rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            color: #fff;
            background-color: var(--primary);
            border-color: var(--primary);
            cursor: pointer;
            width: 100%;
            margin-top: 1.5rem;
        }
        
        .search-button:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
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
        
        .results-title {
            font-size: 1.5rem;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 1rem;
        }
        
        .icon-input {
            position: relative;
        }
        
        .icon-input i {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .icon-input select,
        .icon-input input {
            padding-left: 2.5rem;
        }
        
        .form-actions {
            flex: 1 1 100%;
            display: flex;
            justify-content: flex-end;
            margin: 0.5rem;
        }
        
        .text-center {
            text-align: center;
        }
        
        .price {
            font-weight: 600;
            color: #2d3748;
        }
        
        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
            }
            
            .form-group {
                flex: 1 1 100%;
            }
            
            .container {
                padding: 0 10px;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .flights-table {
                overflow-x: auto;
                display: block;
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
            <h1 class="page-title">Find Your Perfect Flight</h1>
        </div>
    </header>

    <main class="container">
        <div class="search-card">
            <form method="POST" class="search-form">
                <div class="form-group">
                    <label for="source" class="form-label">From</label>
                    <div class="icon-input">
                        <i class="fas fa-plane-departure"></i>
                        <select name="source" id="source" class="form-control" required>
                            <option value="" disabled selected>Select Origin</option>
                            <?php 
                            // Reset the pointer of sources result set
                            mysqli_data_seek($sources, 0);
                            while ($row = mysqli_fetch_assoc($sources)) { 
                                $selected = ($row['source'] == $selected_source) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $row['source']; ?>" <?php echo $selected; ?>><?php echo $row['source']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="destination" class="form-label">To</label>
                    <div class="icon-input">
                        <i class="fas fa-plane-arrival"></i>
                        <select name="destination" id="destination" class="form-control" required>
                            <option value="" disabled selected>Select Destination</option>
                            <?php 
                            // Reset the pointer of destinations result set
                            mysqli_data_seek($destinations, 0);
                            while ($row = mysqli_fetch_assoc($destinations)) { 
                                $selected = ($row['destination'] == $selected_destination) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $row['destination']; ?>" <?php echo $selected; ?>><?php echo $row['destination']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="date" class="form-label">Departure Date</label>
                    <div class="icon-input">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" 
                               name="date" 
                               id="date" 
                               class="form-control" 
                               value="<?php echo $selected_date; ?>" 
                               min="<?php echo date('Y-m-d'); ?>" 
                               required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i> Search Flights
                    </button>
                </div>
            </form>
        </div>

        <?php if (isset($result) && mysqli_num_rows($result) > 0) { ?>
            <h2 class="results-title">Available Flights</h2>
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
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $row['flight_name']; ?></td>
                              
                                <td>
                                    <div class="route-info">
                                        <div class="airport-name"><?php echo $row['source']; ?></div>
                                        <div class="route-arrow"><i class="fas fa-plane"></i></div>
                                        <div class="airport-name"><?php echo $row['destination']; ?></div>
                                    </div>
                                </td>
                                <td><?php echo date('d M Y', strtotime($row['date'])); ?></td>
                                <td>
                                    <div class="time-info"><?php echo date('H:i', strtotime($row['departure_time'])); ?> - <?php echo date('H:i', strtotime($row['arrival_time'])); ?></div>
                                </td>
                                <td>
                                    <div class="price">Economy: ₹<?php echo number_format($row['economy_price']); ?></div>
                                    <div class="price">Business: ₹<?php echo number_format($row['business_price']); ?></div>
                                </td>
                                <td class="text-center">
                                    <a class="book-button" href="book_flight.php?flight_id=<?php echo $row['id']; ?>">Book Now</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } elseif (isset($result) && mysqli_num_rows($result) == 0) { ?>
            <div class="search-card text-center">
                <i class="fas fa-exclamation-circle" style="font-size: 48px; color: var(--gray); margin-bottom: 1rem;"></i>
                <h3>No Flights Found</h3>
                <p>We couldn't find any flights matching your search criteria. Please try different dates or destinations.</p>
            </div>
        <?php } ?>
    </main>

    <script>
        // Add animation to the search form
        document.addEventListener('DOMContentLoaded', function() {
            const formGroups = document.querySelectorAll('.form-group');
            formGroups.forEach((group, index) => {
                group.style.opacity = '0';
                group.style.transform = 'translateY(20px)';
                group.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                
                setTimeout(() => {
                    group.style.opacity = '1';
                    group.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });
            
            // Add animation to search results if they exist
            if (document.querySelector('.flights-table')) {
                const rows = document.querySelectorAll('.flights-table tbody tr');
                rows.forEach((row, index) => {
                    row.style.opacity = '0';
                    row.style.transform = 'translateY(20px)';
                    row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    
                    setTimeout(() => {
                        row.style.opacity = '1';
                        row.style.transform = 'translateY(0)';
                    }, 500 + (index * 50));
                });
            }
        });

        // Date input restrictions
        const dateInput = document.getElementById('date');
        
        // Set minimum date to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(0, 0, 0, 0);
        
        // Format tomorrow's date as YYYY-MM-DD
        const tomorrowFormatted = tomorrow.toISOString().split('T')[0];
        
        // Set min attribute and default value if not already set
        dateInput.setAttribute('min', tomorrowFormatted);
        if (!dateInput.value) {
            dateInput.value = tomorrowFormatted;
        }
        
        // Prevent manual entry of today's date or past dates
        dateInput.addEventListener('input', function() {
            const selectedDate = new Date(this.value);
            if (selectedDate <= tomorrow) {
                this.value = tomorrowFormatted;
            }
        });
    });
    // Rest of your existing animation code...
</body>
</html>