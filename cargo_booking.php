<?php
include 'db.php';
session_start();

// Fetch distinct sources and destinations from active cargo flights
$sources_query = "SELECT DISTINCT source FROM cargo_flights WHERE status = 'Active'";
$sources_result = mysqli_query($conn, $sources_query);

$destinations_query = "SELECT DISTINCT destination FROM cargo_flights WHERE status = 'Active'";
$destinations_result = mysqli_query($conn, $destinations_query);

$source = $destination = $date = "";
$result = null;
$step = isset($_POST['step']) ? $_POST['step'] : 1;

// Process form submissions based on step
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($step == 1) {
        $source = mysqli_real_escape_string($conn, $_POST['source']);
        $destination = mysqli_real_escape_string($conn, $_POST['destination']);
        $date = mysqli_real_escape_string($conn, $_POST['date']);

        // Query to dynamically calculate available weight
        $query = "SELECT cf.flight_id, cf.flight_name, cf.source, cf.destination, cf.date, 
                         cf.price_per_kg, cf.total_weight, 
                         (cf.total_weight - IFNULL(SUM(cb.weight), 0)) AS available_weight
                  FROM cargo_flights cf
                  LEFT JOIN cargo_bookings cb 
                  ON cf.flight_id = cb.flight_id AND cb.status = 'Confirmed'
                  WHERE cf.source = '$source' 
                  AND cf.destination = '$destination' 
                  AND cf.date = '$date' 
                  AND cf.status = 'Active'
                  GROUP BY cf.flight_id
                  HAVING available_weight > 0";

        $result = mysqli_query($conn, $query);
    } elseif ($step == 3) {
        if (!isset($_SESSION['user_id'])) {
            echo "<script>alert('You need to log in first!'); window.location.href='login.php';</script>";
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $flight_id = $_POST['flight_id'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $aadhar_number = $_POST['aadhar_number'];
        $weight = $_POST['weight'];
        $total_price = $_POST['total_price'];

        // Generate a Unique Booking ID
        $booking_id = "CB" . time() . rand(100, 999);

        // Insert Booking into Database
        $query = "INSERT INTO cargo_bookings (booking_id, user_id, flight_id, user_name, phone, email, user_address, weight, total_price, status, aadhar_number) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Confirmed', ?)";

        $stmt = mysqli_prepare($conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sisssssdss", $booking_id, $user_id, $flight_id, $name, $phone, $email, $address, $weight, $total_price, $aadhar_number);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                echo "<script>
                    setTimeout(function() {
                        alert('Booking Successfully Completed! Your Booking ID: " . $booking_id . "');
                        window.location.href = 'user_dashboard.php';
                    }, 1000);
                </script>";
            } else {
                echo "Error: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Cargo</title>
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
        
        /* Step indicators styling */
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        
        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
            color: var(--dark);
            font-weight: 600;
        }
        
        .step.active {
            background: var(--primary);
            color: white;
        }
        
        .step-line {
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            height: 2px;
            background: #e2e8f0;
            z-index: 0;
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
            <h1 class="page-title">Book Cargo Shipment</h1>
        </div>
    </header>

    <main class="container">
        <div class="search-card">
            <div class="step-indicator">
                <div class="step-line"></div>
                <div class="step <?php echo $step >= 1 ? 'active' : ''; ?>">1</div>
                <div class="step <?php echo $step >= 2 ? 'active' : ''; ?>">2</div>
                <div class="step <?php echo $step >= 3 ? 'active' : ''; ?>">3</div>
            </div>

            <?php if ($step == 1) { ?>
                <form method="POST" class="search-form">
                    <input type="hidden" name="step" value="1">
                    <div class="form-group">
                        <label for="source" class="form-label">From</label>
                        <div class="icon-input">
                            <i class="fas fa-plane-departure"></i>
                            <select name="source" id="source" class="form-control" required>
                                <option value="" disabled selected>Select Origin</option>
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
                        <label for="date" class="form-label">Shipment Date</label>
                        <div class="icon-input">
                            <i class="fas fa-calendar-alt"></i>
                            <input type="date" 
                                   name="date" 
                                   id="date" 
                                   class="form-control" 
                                   value="<?php echo date('Y-m-d'); ?>" 
                                   min="<?php echo date('Y-m-d'); ?>" 
                                   required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="search-button">
                            <i class="fas fa-search"></i> Search Cargo Flights
                        </button>
                    </div>
                </form>
            </div>

            <?php if ($result && mysqli_num_rows($result) > 0) { ?>
                <h2 class="results-title">Available Cargo Flights</h2>
                <div class="flights-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Flight Name</th>
                                <th>Route</th>
                                <th>Date</th>
                                <th>Price/KG (₹)</th>
                                <th>Available Weight</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['flight_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['source']); ?> to <?php echo htmlspecialchars($row['destination']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($row['date'])); ?></td>
                                    <td><?php echo number_format($row['price_per_kg']); ?></td>
                                    <td><?php echo htmlspecialchars($row['available_weight']); ?> KG</td>
                                    <td class="text-center">
                                        <form method="POST">
                                            <input type="hidden" name="step" value="2">
                                            <input type="hidden" name="flight_id" value="<?php echo $row['flight_id']; ?>">
                                            <input type="hidden" name="price_per_kg" value="<?php echo $row['price_per_kg']; ?>">
                                            <button type="submit" class="book-button">Book Now</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } elseif (isset($result) && mysqli_num_rows($result) == 0) { ?>
                <div class="search-card text-center">
                    <i class="fas fa-exclamation-circle" style="font-size: 48px; color: var(--gray); margin-bottom: 1rem;"></i>
                    <h3>No Cargo Flights Found</h3>
                    <p>We couldn't find any cargo flights matching your search criteria. Please try different dates or destinations.</p>
                </div>
            <?php } ?>

        <?php } elseif ($step == 2) { ?>
            <div class="search-card">
                <h2 class="results-title">Enter Cargo Booking Details</h2>
                <form method="POST" class="search-form" onsubmit="return validateForm()">
                    <input type="hidden" name="step" value="3">
                    <input type="hidden" name="flight_id" value="<?php echo $_POST['flight_id']; ?>">
                    <input type="hidden" name="price_per_kg" value="<?php echo $_POST['price_per_kg']; ?>">

                    <div class="form-group">
                        <label for="name" class="form-label">Full Name</label>
                        <div class="icon-input">
                            <i class="fas fa-user"></i>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter your full name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <div class="icon-input">
                            <i class="fas fa-phone"></i>
                            <input type="tel" name="phone" id="phone" class="form-control" placeholder="10-digit phone number" pattern="\d{10}" title="Please enter a valid 10-digit phone number" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="icon-input">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Your email address" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label">Shipping Address</label>
                        <div class="icon-input">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" name="address" id="address" class="form-control" placeholder="Complete shipping address" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="aadhar_number" class="form-label">Aadhaar Number</label>
                        <div class="icon-input">
                            <i class="fas fa-id-card"></i>
                            <input type="text" name="aadhar_number" id="aadhar_number" class="form-control" placeholder="12-digit Aadhaar number" pattern="\d{12}" title="Please enter a valid 12-digit Aadhaar number" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="weight" class="form-label">Weight to Book (in KG)</label>
                        <div class="icon-input">
                            <i class="fas fa-weight"></i>
                            <input type="number" name="weight" id="weight" class="form-control" min="1" max="110" placeholder="Enter weight in KG" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="total_price" class="form-label">Total Price (₹)</label>
                        <div class="icon-input">
                            <i class="fas fa-rupee-sign"></i>
                            <input type="number" name="total_price" id="total_price" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="search-button">
                            <i class="fas fa-credit-card"></i> Proceed to Payment
                        </button>
                    </div>
                </form>
            </div>

            <script>
                function calculateTotal() {
                    const weight = document.getElementById('weight').value;
                    const pricePerKg = <?php echo $_POST['price_per_kg']; ?>;
                    const total = weight * pricePerKg;
                    document.getElementById('total_price').value = total;
                }

                document.getElementById('weight').addEventListener('input', calculateTotal);

                function validateForm() {
                    const weight = document.getElementById('weight').value;
                    if (weight < 1 || weight > 110) {
                        alert('Weight must be between 1 and 110 KG');
                        return false;
                    }
                    return true;
                }

                // Initialize calculation
                calculateTotal();
            </script>
        <?php } ?>
    </main>

    <script>
        // Add animation to the form elements
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
        
        // Set minimum date to today
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Format today's date as YYYY-MM-DD
        const todayFormatted = today.toISOString().split('T')[0];
        
        // Set min attribute and default value if not already set
        dateInput.setAttribute('min', todayFormatted);
        if (!dateInput.value) {
            dateInput.value = todayFormatted;
        }
        
        // Prevent manual entry of past dates
        dateInput.addEventListener('input', function() {
            const selectedDate = new Date(this.value);
            if (selectedDate < today) {
                this.value = todayFormatted;
            }
        });
    </script>
</body>
</html>