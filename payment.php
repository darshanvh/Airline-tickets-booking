<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle POST request for payment processing
// Update the booking status update query to handle all bookings with the same reference_id
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['booking_id']) || !isset($_POST['payment_method']) || !isset($_POST['reference_id'])) {
        header("Location: user_dashboard.php?error=invalid_request");
        exit();
    }

    $booking_id = $_POST['booking_id'];
    $payment_method = $_POST['payment_method'];
    $reference_id = $_POST['reference_id'];

    // Update all bookings with the same reference_id
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Booked', payment_method = ? WHERE reference_id = ?");
    $stmt->bind_param("ss", $payment_method, $reference_id);

    if ($stmt->execute()) {
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
                        <h2 class='message-title'>Payment Successful!</h2>
                        <p class='message-text'>Your booking has been confirmed. Thank you for choosing AirBooking.</p>
                        <a href='user_dashboard.php' class='home-button'>
                            <i class='fas fa-home'></i> Go to Dashboard
                        </a>
                    </div>
                    
                    <p class='redirect-text' id='redirect-text'>Processing your payment...</p>
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
                alert('Payment failed! Please try again.');
                window.location.href = 'payment.php?booking_id=$booking_id';
              </script>";
        exit();
    }

// Close the prepared statement to free up resources and avoid memory leaks
$stmt->close();
}

// If not a POST request, handle like regular payment page
if (!isset($_GET['booking_id']) || empty($_GET['booking_id'])) {
    header("Location: user_dashboard.php?error=missing_booking_id");
    exit();
}

$booking_id = $_GET['booking_id'];
$reference_id = $_GET['ref'];

// Modified query to fetch all related bookings
$stmt = $conn->prepare("
    SELECT b.id AS booking_id, b.flight_id, b.seat_numbers, b.seat_type, 
           b.seat_count, b.total_price, b.name, b.age, b.gender, 
           b.aadhar_number, b.reference_id,
           f.flight_name, f.flight_id AS flight_code, f.source, f.destination, 
           f.date, f.departure_time, f.arrival_time
    FROM bookings b
    JOIN flights f ON b.flight_id = f.id
    WHERE b.reference_id = ?
");
$stmt->bind_param("s", $reference_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: user_dashboard.php?error=booking_not_found");
    exit();
}

$bookings = $result->fetch_all(MYSQLI_ASSOC);
$booking = $bookings[0]; // First booking for flight details
$total_amount = 0;
foreach ($bookings as $b) {
    $total_amount += $b['total_price'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - AirBooking</title>
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
        
        .badge-success {
            color: #fff;
            background-color: var(--success);
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
        
        .flight-time {
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .flight-date {
            color: #718096;
            font-size: 0.875rem;
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
            <h1 class="page-title">Complete Your Payment</h1>
            <p class="page-subtitle">Booking ID: <?= htmlspecialchars($booking['booking_id']); ?></p>
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
                        <div class="flight-city"><?= htmlspecialchars($booking['source']); ?></div>
                        <div class="flight-time">
                            <?= isset($booking['departure_time']) ? date('H:i', strtotime($booking['departure_time'])) : '--:--'; ?>
                        </div>
                        <div class="flight-date"><?= date('d M Y', strtotime($booking['date'])); ?></div>
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
                        <div class="flight-city"><?= htmlspecialchars($booking['destination']); ?></div>
                        <div class="flight-time">
                            <?= isset($booking['arrival_time']) ? date('H:i', strtotime($booking['arrival_time'])) : '--:--'; ?>
                        </div>
                        <div class="flight-date"><?= date('d M Y', strtotime($booking['date'])); ?></div>
                    </div>
                </div>

                <div class="flight-info">
                    <div class="flight-info-item">
                        <div class="flight-info-label">Flight</div>
                        <div class="flight-info-value">
                            <?= htmlspecialchars($booking['flight_name']); ?>
                            <small>(<?= htmlspecialchars($booking['flight_code'] ?? $booking['flight_id']); ?>)</small>
                        </div>
                    </div>
                    <div class="flight-info-item">
                        <div class="flight-info-label">Class</div>
                        <div class="flight-info-value"><?= htmlspecialchars($booking['seat_type']); ?></div>
                    </div>
                    
                  
                </div>

                <div class="booking-summary">
                    <h4 style="margin-bottom: 1rem;">Booking Summary</h4>
                    <div class="summary-item">
                        <span>Seat Type</span>
                        <span><?= htmlspecialchars($booking['seat_type']); ?> Class</span>
                    </div>
                    <div class="summary-item">
                        <span>Passenger Details</span>
                        <div>
                            <?php foreach ($bookings as $b): ?>
                                <div style="margin-bottom: 0.5rem;">
                                    <span class="badge badge-primary"><?= htmlspecialchars($b['seat_numbers']); ?></span>
                                    - <?= htmlspecialchars($b['name']); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="summary-item">
                        <span>Price per Seat</span>
                        <span>₹<?= number_format($booking['total_price'], 2); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Number of Seats</span>
                        <span><?= count($bookings); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Total Price</span>
                        <span>₹<?= number_format($total_amount, 2); ?></span>
                    </div>
                </div>

                <form method="POST" action="payment.php" id="paymentForm">
                    <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['booking_id']); ?>">
                    <input type="hidden" name="reference_id" value="<?= htmlspecialchars($reference_id); ?>">
                    
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
                            <input type="radio" name="payment_method" value="Visa Card" id="visa" class="payment-radio" required>
                            <div class="payment-icon">
                                <i class="fab fa-cc-visa"></i>
                            </div>
                            <label for="visa" class="payment-label">Visa Card</label>
                        </div>
                    </div>
                    
                    <button type="submit" id="payBtn" class="button button-success" disabled>
                        <i class="fas fa-lock"></i> Complete Payment - ₹<?= number_format($booking['total_price'], 2); ?>
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