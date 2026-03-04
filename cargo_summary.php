<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle POST request for payment processing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment_method'])) {
    $user_id = $_SESSION['user_id'];
    $flight_id = $_POST['flight_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $aadhar_number = $_POST['aadhar_number'];
    $weight = $_POST['weight'];
    $total_price = $_POST['total_price'];
    $payment_method = $_POST['payment_method'];

    // Generate a Unique Booking ID
    $booking_id = "CB" . time() . rand(100, 999); 

    // Insert Booking into Database
    $query = "INSERT INTO cargo_bookings (booking_id, user_id, flight_id, user_name, phone, email, user_address, weight, total_price, status, aadhar_number, payment_method) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Confirmed', ?, ?)";

    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sisssssdsss", $booking_id, $user_id, $flight_id, $name, $phone, $email, $address, $weight, $total_price, $aadhar_number, $payment_method);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            echo "
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Payment Successful - AirBooking</title>
                <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css' rel='stylesheet'>
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
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;
                        text-align: center;
                        padding: 2rem;
                    }
                    
                    .container {
                        width: 100%;
                        max-width: 500px;
                    }
                    
                    .card {
                        background-color: white;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: var(--card-shadow);
                        padding: 2rem;
                    }
                    
                    .spinner {
                        width: 80px;
                        height: 80px;
                        border: 6px solid rgba(52, 144, 220, 0.2);
                        border-top: 6px solid var(--primary);
                        border-radius: 50%;
                        animation: spin 1s linear infinite;
                        margin: 0 auto 2rem;
                    }
                    
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                    
                    .message {
                        display: none;
                        margin-top: 1.5rem;
                    }
                    
                    .success-icon {
                        font-size: 4rem;
                        color: var(--success);
                        margin-bottom: 1rem;
                    }
                    
                    .message-title {
                        font-size: 1.5rem;
                        font-weight: 700;
                        color: var(--success);
                        margin-bottom: 0.5rem;
                    }
                    
                    .message-text {
                        color: var(--dark);
                        margin-bottom: 1.5rem;
                    }
                    
                    .redirect-text {
                        color: var(--gray);
                        font-size: 0.9rem;
                        margin-top: 1.5rem;
                    }
                    
                    .home-button {
                        display: inline-block;
                        margin-top: 1.5rem;
                        background-color: var(--primary);
                        color: white;
                        border: none;
                        padding: 0.75rem 1.5rem;
                        border-radius: 4px;
                        font-weight: 500;
                        cursor: pointer;
                        transition: background-color 0.3s;
                        text-decoration: none;
                    }
                    
                    .home-button:hover {
                        background-color: var(--primary-dark);
                    }
                    
                    .logo {
                        font-size: 1.5rem;
                        font-weight: 700;
                        color: var(--primary);
                        margin-bottom: 2rem;
                    }

                    .booking-id {
                        background-color: #f8fafc;
                        padding: 0.75rem;
                        border-radius: 4px;
                        font-weight: 600;
                        margin: 1rem 0;
                        border: 1px solid #e2e8f0;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='logo'>AirBooking</div>
                    <div class='card'>
                        <div class='spinner' id='spinner'></div>
                        
                        <div class='message' id='message'>
                            <div class='success-icon'>
                                <i class='fas fa-check-circle'></i>
                            </div>
                            <h2 class='message-title'>Booking Confirmed!</h2>
                            <p class='message-text'>Your cargo booking has been successfully processed.</p>
                            <div class='booking-id'>Booking ID: <strong>$booking_id</strong></div>
                            <a href='user_dashboard.php' class='home-button'>
                                <i class='fas fa-home'></i> Go to Dashboard
                            </a>
                        </div>
                        
                        <p class='redirect-text' id='redirect-text'>Processing your booking...</p>
                    </div>
                </div>

                <script>
                    setTimeout(function() {
                        document.getElementById('spinner').style.display = 'none';
                        document.getElementById('message').style.display = 'block';
                        document.getElementById('redirect-text').innerHTML = 'You will be redirected to your dashboard in a few seconds...';
                        
                        setTimeout(function() {
                            window.location.href = 'user_dashboard.php';
                        }, 3000); // Redirect after 3 seconds
                    }, 3000); // Show success message after 3 seconds
                </script>
            </body>
            </html>";
            exit();
        } else {
            echo "<script>
                    alert('Booking failed! Please try again.');
                    window.location.href = 'cargo_summary.php';
                  </script>";
            exit();
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<script>
                alert('Error preparing statement: " . mysqli_error($conn) . "');
                window.location.href = 'cargo_summary.php';
              </script>";
        exit();
    }
} else {
    // Regular page load
    if (!isset($_POST['flight_id']) || !isset($_POST['weight'])) {
        header("Location: user_dashboard.php?error=missing_data");
        exit();
    }

    $flight_id = $_POST['flight_id'];
    $price_per_kg = $_POST['price_per_kg'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $aadhar_number = $_POST['aadhar_number'];
    $weight = $_POST['weight'];

    // Fetch flight details
    $query = "SELECT * FROM cargo_flights WHERE flight_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $flight_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $flight = mysqli_fetch_assoc($result);

    if (!$flight) {
        echo "<script>
                alert('Flight not found.');
                window.location.href = 'user_dashboard.php';
              </script>";
        exit();
    }

    // Calculate total price
    $total_price = $weight * $price_per_kg;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargo Booking Summary - AirBooking</title>
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
            max-width: 1000px;
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
            flex: 1 1 200px;
            margin-right: 1.5rem;
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
            font-size: 1.1rem;
        }
        
        .payment-methods {
            margin-bottom: 2rem;
        }
        
        .payment-method-label {
            font-weight: 500;
            margin-bottom: 1rem;
            display: block;
        }
        
        .payment-option {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .payment-option:hover {
            border-color: var(--primary);
            background-color: #f8fafc;
        }
        
        .payment-option.selected {
            border-color: var(--primary);
            background-color: rgba(52, 144, 220, 0.05);
        }
        
        .payment-radio {
            margin-right: 1rem;
        }
        
        .payment-icon {
            height: 32px;
            width: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: #718096;
            font-size: 1.25rem;
        }
        
        .payment-label {
            font-weight: 500;
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
            width: 100%;
        }
        
        .button-success {
            color: #fff;
            background-color: var(--success);
            border-color: var(--success);
        }
        
        .button-success:hover {
            background-color: #2d9b5e;
            border-color: #2d9b5e;
        }
        
        .button-success:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }
        
        .flight-card {
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 1rem 0;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .flight-from-to {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        
        .flight-city {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .flight-code {
            color: #718096;
            font-size: 0.875rem;
        }
        
        .flight-timeline {
            display: flex;
            align-items: center;
            margin: 1rem 0;
            width: 100%;
            position: relative;
        }
        
        .flight-point {
            width: 12px;
            height: 12px;
            background-color: var(--primary);
            border-radius: 50%;
            z-index: 1;
        }
        
        .flight-line {
            height: 2px;
            background-color: var(--primary);
            flex-grow: 1;
            position: relative;
        }
        
        .flight-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.25rem;
            color: var(--primary);
            background-color: white;
            padding: 0 0.5rem;
        }
        
        @media (max-width: 768px) {
            .flight-info-item {
                flex: 1 1 100%;
                margin-right: 0;
            }
            
            .flight-from-to {
                flex-direction: column;
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
            <h1 class="page-title">Cargo Booking Summary</h1>
            <p class="page-subtitle">Flight ID: <?= htmlspecialchars($flight['flight_id']); ?></p>
        </div>
    </header>

    <main class="container">
        <div class="card">
            <div class="card-header">
                <h3>Flight Details</h3>
            </div>
            <div class="card-body">
                <div class="flight-card">
                    <div class="flight-from-to">
                        <div class="flight-city"><?= htmlspecialchars($flight['source']); ?></div>
                        <div class="flight-time">
                            <?= isset($flight['departure_time']) ? date('H:i', strtotime($flight['departure_time'])) : '--:--'; ?>
                        </div>
                    </div>
                    
                    <div class="flight-timeline">
                        <div class="flight-point"></div>
                        <div class="flight-line">
                            <div class="flight-icon">
                                <i class="fas fa-plane"></i>
                            </div>
                        </div>
                        <div class="flight-point"></div>
                    </div>
                    
                    <div class="flight-from-to">
                        <div class="flight-city"><?= htmlspecialchars($flight['destination']); ?></div>
                        <div class="flight-time">
                            <?= isset($flight['arrival_time']) ? date('H:i', strtotime($flight['arrival_time'])) : '--:--'; ?>
                        </div>
                    </div>
                </div>

                <div class="flight-info">
                    <div class="flight-info-item">
                        <div class="flight-info-label">Source</div>
                        <div class="flight-info-value"><?= htmlspecialchars($flight['source']); ?></div>
                    </div>
                    <div class="flight-info-item">
                        <div class="flight-info-label">Destination</div>
                        <div class="flight-info-value"><?= htmlspecialchars($flight['destination']); ?></div>
                    </div>
                    <div class="flight-info-item">
                        <div class="flight-info-label">Departure Time</div>
                        <div class="flight-info-value"><?= htmlspecialchars($flight['departure_time']); ?></div>
                    </div>
                    <div class="flight-info-item">
                        <div class="flight-info-label">Arrival Time</div>
                        <div class="flight-info-value"><?= htmlspecialchars($flight['arrival_time']); ?></div>
                    </div>
                </div>

                <div class="flight-info">
                    <div class="flight-info-item">
                        <div class="flight-info-label">Sender Name</div>
                        <div class="flight-info-value"><?= htmlspecialchars($name); ?></div>
                    </div>
                    <div class="flight-info-item">
                        <div class="flight-info-label">Phone</div>
                        <div class="flight-info-value"><?= htmlspecialchars($phone); ?></div>
                    </div>
                    <div class="flight-info-item">
                        <div class="flight-info-label">Email</div>
                        <div class="flight-info-value"><?= htmlspecialchars($email); ?></div>
                    </div>
                    <div class="flight-info-item">
                        <div class="flight-info-label">Aadhaar Number</div>
                        <div class="flight-info-value"><?= htmlspecialchars($aadhar_number); ?></div>
                    </div>
                </div>

                <div class="booking-summary">
                    <h4 style="margin-bottom: 1rem;">Payment Summary</h4>
                    <div class="summary-item">
                        <span>Total Weight</span>
                        <span><?= htmlspecialchars($weight); ?> KG</span>
                    </div>
                    <div class="summary-item">
                        <span>Price per KG</span>
                        <span>₹<?= number_format($price_per_kg, 2); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Total Amount</span>
                        <span>₹<?= number_format($total_price, 2); ?></span>
                    </div>
                </div>

                <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="paymentForm">
                    <input type="hidden" name="flight_id" value="<?= htmlspecialchars($flight_id); ?>">
                    <input type="hidden" name="name" value="<?= htmlspecialchars($name); ?>">
                    <input type="hidden" name="phone" value="<?= htmlspecialchars($phone); ?>">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email); ?>">
                    <input type="hidden" name="address" value="<?= htmlspecialchars($address); ?>">
                    <input type="hidden" name="aadhar_number" value="<?= htmlspecialchars($aadhar_number); ?>">
                    <input type="hidden" name="weight" value="<?= htmlspecialchars($weight); ?>">
                    <input type="hidden" name="total_price" value="<?= htmlspecialchars($total_price); ?>">
                    
                    <div class="payment-methods">
                        <label class="payment-method-label">Select Payment Method</label>
                        
                        <div class="payment-option" data-payment="PhonePe">
                            <input type="radio" name="payment_method" value="PhonePe" id="phonepe" class="payment-radio" required>
                            <div class="payment-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <label for="phonepe" class="payment-label">PhonePe</label>
                        </div>
                        
                        <div class="payment-option" data-payment="Google Pay">
                            <input type="radio" name="payment_method" value="Google Pay" id="googlepay" class="payment-radio" required>
                            <div class="payment-icon">
                                <i class="fab fa-google-pay"></i>
                            </div>
                            <label for="googlepay" class="payment-label">Google Pay</label>
                        </div>
                        
                        <div class="payment-option" data-payment="Paytm">
                            <input type="radio" name="payment_method" value="Paytm" id="paytm" class="payment-radio" required>
                            <div class="payment-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <label for="paytm" class="payment-label">Paytm</label>
                        </div>
                        
                        <div class="payment-option" data-payment="Visa Card">
                            <input type="radio" name="payment_method" value="Visa" id="visa" class="payment-radio" required>
                            <div class="payment-icon">
                                <i class="fab fa-cc-visa"></i>
                            </div>
                            <label for="visa" class="payment-label">Visa Card</label>
                        </div>
                    </div>
                    
                    <button type="submit" id="payBtn" class="button button-success" disabled>
                        <i class="fas fa-lock"></i> Confirm & Pay - ₹<?= number_format($total_price, 2); ?>
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentOptions = document.querySelectorAll('.payment-option');
            const paymentForm = document.getElementById('paymentForm');
            const payBtn = document.getElementById('payBtn');
            
            // Make payment options clickable
            paymentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selection from all options
                    paymentOptions.forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    
                    // Select the clicked option
                    this.classList.add('selected');
                    
                    // Check the radio inside this option
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    
                    // Enable the payment button
                    payBtn.disabled = false;
                });
            });
            
            // Also handle direct radio button clicks
            const radioButtons = document.querySelectorAll('.payment-radio');
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        // Remove selection from all options
                        paymentOptions.forEach(opt => {
                            opt.classList.remove('selected');
                        });
                        
                        // Add selected class to parent option
                        this.closest('.payment-option').classList.add('selected');
                        
                        // Enable the payment button
                        payBtn.disabled = false;
                    }
                });
            });
        });
    </script>
</body>
</html>