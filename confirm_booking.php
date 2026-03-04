<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Validate required GET parameters
if (!isset($_GET['flight_id'], $_GET['seat_type'], $_GET['seats']) || empty($_GET['seats'])) {
    die("Error: Missing required parameters.");
}

$flight_id = $_GET['flight_id'];
$seat_type = $_GET['seat_type'];
$seats = explode(",", $_GET['seats']); // Convert seat list to an array
$seat_count = count($seats);

// Fetch flight details
$stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
$stmt->bind_param("i", $flight_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Error: Flight not found.");
}

$flight = $result->fetch_assoc();
$stmt->close();

$amount = ($seat_type == "Business") ? $flight['business_price'] : $flight['economy_price'];
$total_price = $seat_count * $amount;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['name'])) {
    $conn->begin_transaction(); // Start transaction
    try {
        $reference_id = uniqid('BKG'); // Generate a unique reference ID
        $seat_numbers = implode(",", $seats);

        foreach ($_POST['name'] as $i => $name) {
            $name = trim($_POST['name'][$i]);
            $aadhar_number = trim($_POST['aadhar_number'][$i]);
            $age = (int) $_POST['age'][$i];
            $gender = trim($_POST['gender'][$i]);
            $seat_number = $seats[$i];

            // Add reference_id to the INSERT query
            $stmt = $conn->prepare("
                INSERT INTO bookings 
                (user_id, flight_id, seat_class, seat_numbers, seat_type, seat_count, 
                total_amount, amount, total_price, status, aadhar_number, age, gender, 
                name, reference_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?, ?, ?, ?, ?)
            ");
            $seat_count_individual = 1;
            $stmt->bind_param("iisssidddssiss", 
                $user_id, $flight_id, $seat_type, $seat_number, $seat_type, 
                $seat_count_individual, $amount, $amount, $amount,
                $aadhar_number, $age, $gender, $name, $reference_id
            );
            $stmt->execute();
            $booking_id = $stmt->insert_id;
            $stmt->close();
        }

        $conn->commit();
        header("Location: payment.php?booking_id=$booking_id&ref=$reference_id");
        exit();

    } catch (Exception $e) {
        $conn->rollback(); // Rollback transaction on error
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking - <?= htmlspecialchars($flight['flight_name']); ?></title>
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
            --warning: #f6ad55;
            --info: #60a5fa;
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
        
        .page-subtitle {
            color: #718096;
            margin-top: 0.5rem;
        }
        
        .card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }
        
        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .flight-info {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .flight-info-item {
            flex: 1 1 250px;
            margin-right: 2rem;
            margin-bottom: 1rem;
        }
        
        .flight-info-label {
            font-size: 0.875rem;
            color: #718096;
            margin-bottom: 0.25rem;
        }
        
        .flight-info-value {
            font-weight: 600;
            color: var(--dark);
        }
        
        .booking-summary {
            background-color: #f8fafc;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }
        
        .summary-item:last-child {
            margin-bottom: 0;
            padding-top: 0.75rem;
            border-top: 1px solid #e2e8f0;
            font-weight: 700;
        }
        
        .passenger-form {
            margin-bottom: 2rem;
        }
        
        .passenger-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .passenger-header {
            background-color: var(--primary);
            color: white;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
        
        .passenger-body {
            padding: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
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
        
        .form-select {
            display: block;
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: var(--dark);
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #e2e8f0;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }
        
        .form-select:focus {
            border-color: var(--primary);
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
        }
        
        .form-error {
            color: var(--danger);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        
        .button {
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
            cursor: pointer;
        }
        
        .button-primary {
            color: #fff;
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .button-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .button-outline {
            color: var(--primary);
            background-color: transparent;
            border-color: var(--primary);
        }
        
        .button-outline:hover {
            color: #fff;
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .alert {
            position: relative;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }
        
        .alert-info {
            color: #385d7a;
            background-color: #e2f0fb;
            border-color: #bce8f1;
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 9999px;
        }
        
        .badge-primary {
            color: #fff;
            background-color: var(--primary);
        }
        
        .badge-success {
            color: #fff;
            background-color: var(--success);
        }
        
        @media (max-width: 768px) {
            .flight-info-item {
                flex: 1 1 100%;
                margin-right: 0;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 1rem;
            }
            
            .button {
                width: 100%;
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
            <h1 class="page-title">Confirm Your Booking</h1>
            <p class="page-subtitle">Flight: <?= htmlspecialchars($flight['flight_name']); ?> (<?= htmlspecialchars($flight['flight_id']); ?>)</p>
        </div>
    </header>

    <main class="container">
        <div class="card">
            <div class="card-header">
                <h3>Flight Details</h3>
            </div>
            <div class="card-body">
                <div class="flight-info">
                    <div class="flight-info-item">
                        <div class="flight-info-label">From</div>
                        <div class="flight-info-value"><?= htmlspecialchars($flight['source']); ?></div>
                    </div>
                    <div class="flight-info-item">
                        <div class="flight-info-label">To</div>
                        <div class="flight-info-value"><?= htmlspecialchars($flight['destination']); ?></div>
                    </div>
                    <div class="flight-info-item">
                        <div class="flight-info-label">Date</div>
                        <div class="flight-info-value"><?= date('d M Y', strtotime($flight['date'])); ?></div>
                    </div>
                    <div class="flight-info-item">
                        <div class="flight-info-label">Departure</div>
                        <div class="flight-info-value"><?= date('H:i', strtotime($flight['departure_time'])); ?></div>
                    </div>
                    <div class="flight-info-item">
                        <div class="flight-info-label">Arrival</div>
                        <div class="flight-info-value"><?= date('H:i', strtotime($flight['arrival_time'])); ?></div>
                    </div>
                </div>

                <div class="booking-summary">
                    <h4 style="margin-bottom: 1rem;">Booking Summary</h4>
                    <div class="summary-item">
                        <span>Seat Type</span>
                        <span><?= htmlspecialchars($seat_type); ?> Class</span>
                    </div>
                    <div class="summary-item">
                        <span>Selected Seats</span>
                        <div>
                            <?php foreach ($seats as $seat): ?>
                                <span class="badge badge-primary"><?= htmlspecialchars($seat); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="summary-item">
                        <span>Price per Seat</span>
                        <span>₹<?= number_format($amount, 2); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Number of Seats</span>
                        <span><?= $seat_count; ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Total Price</span>
                        <span>₹<?= number_format($total_price, 2); ?></span>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Please provide passenger details for each selected seat.
                </div>

                <form method="post" class="passenger-form">
                    <?php foreach ($seats as $index => $seat): ?>
                        <div class="passenger-card">
                            <div class="passenger-header">
                                <i class="fas fa-user"></i> Passenger for Seat <?= htmlspecialchars($seat); ?>
                            </div>
                            <div class="passenger-body">
                                <div class="form-group">
                                    <label for="name_<?= $index; ?>" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name_<?= $index; ?>" name="name[]" placeholder="Enter passenger's full name" required>
                                </div>

                                <div class="form-group">
                                    <label for="aadhar_<?= $index; ?>" class="form-label">Aadhar Number</label>
                                    <input type="text" class="form-control" id="aadhar_<?= $index; ?>" name="aadhar_number[]" placeholder="12-digit Aadhar number" required pattern="[0-9]{12}">
                                    <div class="form-error" id="aadhar_error_<?= $index; ?>" style="display: none;">Please enter a valid 12-digit Aadhar number.</div>
                                </div>

                                <div class="form-group">
                                    <label for="age_<?= $index; ?>" class="form-label">Age</label>
                                    <input type="number" class="form-control" id="age_<?= $index; ?>" name="age[]" min="1" max="100" placeholder="Enter age" required>
                                </div>

                                <div class="form-group" style="margin-bottom: 0;">
                                    <label for="gender_<?= $index; ?>" class="form-label">Gender</label>
                                    <select class="form-select" id="gender_<?= $index; ?>" name="gender[]" required>
                                        <option value="" selected disabled>Select gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="action-buttons">
                        <a href="javascript:history.back()" class="button button-outline">
                            <i class="fas fa-arrow-left"></i> Back to Seat Selection
                        </a>
                        <button type="submit" id="confirmBtn" class="button button-primary">
                            Proceed to Payment <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Validate Aadhar number format
        document.addEventListener('DOMContentLoaded', function() {
            <?php foreach ($seats as $index => $seat): ?>
            document.getElementById('aadhar_<?= $index; ?>').addEventListener('input', function(e) {
                const aadharInput = e.target;
                const aadharError = document.getElementById('aadhar_error_<?= $index; ?>');
                
                // Only allow numbers
                aadharInput.value = aadharInput.value.replace(/[^0-9]/g, '');
                
                // Check length
                if (aadharInput.value.length > 0 && aadharInput.value.length !== 12) {
                    aadharError.style.display = 'block';
                } else {
                    aadharError.style.display = 'none';
                }
            });
            <?php endforeach; ?>
            
            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                let isValid = true;
                
                <?php foreach ($seats as $index => $seat): ?>
                const aadhar_<?= $index; ?> = document.getElementById('aadhar_<?= $index; ?>').value;
                if (aadhar_<?= $index; ?>.length !== 12) {
                    document.getElementById('aadhar_error_<?= $index; ?>').style.display = 'block';
                    isValid = false;
                }
                <?php endforeach; ?>
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>