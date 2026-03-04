<?php
include 'db.php';

// Fetch distinct sources and destinations from active cargo flights
$sources_query = "SELECT DISTINCT source FROM cargo_flights WHERE status = 'Active'";
$sources_result = mysqli_query($conn, $sources_query);

$destinations_query = "SELECT DISTINCT destination FROM cargo_flights WHERE status = 'Active'";
$destinations_result = mysqli_query($conn, $destinations_query);

$source = $destination = $date = "";
$result = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_GET['source'])) {
    $source = isset($_POST['source']) ? mysqli_real_escape_string($conn, $_POST['source']) : mysqli_real_escape_string($conn, $_GET['source']);
    $destination = isset($_POST['destination']) ? mysqli_real_escape_string($conn, $_POST['destination']) : mysqli_real_escape_string($conn, $_GET['destination']);
    $date = isset($_POST['date']) ? mysqli_real_escape_string($conn, $_POST['date']) : mysqli_real_escape_string($conn, $_GET['date']);

    // Fetch only active flights that match user input and have available weight
    // Modify the query to calculate available weight
    $query = "SELECT cf.*, 
        (cf.total_weight - IFNULL((
            SELECT SUM(weight) 
            FROM cargo_bookings 
            WHERE flight_id = cf.flight_id 
            AND status = 'Confirmed'
        ), 0)) as available_weight 
        FROM cargo_flights cf
        WHERE cf.source = '$source' 
        AND cf.destination = '$destination' 
        AND cf.date = '$date' 
        AND cf.status = 'Active'
        HAVING available_weight > 0";
    
    $result = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Cargo Flight</title>
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
            <h1 class="page-title">Book Cargo Flight</h1>
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
                            <option value="" disabled selected>Select Source</option>
                            <?php while ($row = mysqli_fetch_assoc($sources_result)) { ?>
                                <option value="<?php echo htmlspecialchars($row['source']); ?>" 
                                    <?php echo ($source == $row['source']) ? "selected" : ""; ?>>
                                    <?php echo htmlspecialchars($row['source']); ?>
                                </option>
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
                            <?php while ($row = mysqli_fetch_assoc($destinations_result)) { ?>
                                <option value="<?php echo htmlspecialchars($row['destination']); ?>" 
                                    <?php echo ($destination == $row['destination']) ? "selected" : ""; ?>>
                                    <?php echo htmlspecialchars($row['destination']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="date" class="form-label">Date</label>
                    <div class="icon-input">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" name="date" id="date" class="form-control" value="<?php echo htmlspecialchars($date); ?>" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i> Search Flights
                    </button>
                </div>
            </form>
        </div>

        <?php if ($result !== null) { ?>
            <h2 class="results-title">Available Cargo Flights</h2>
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
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) { ?>
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
                                        <a class="book-button" href="cargo_payment.php?flight_id=<?php echo $row['flight_id']; ?>&price=<?php echo $row['price_per_kg']; ?>">
                                            Book Now
                                        </a>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="6" class="text-center">No active flights available for the selected criteria.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
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
    </script>
</body>
</html>
