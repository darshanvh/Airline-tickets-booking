<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login to access this page.'); window.location.href='login.php';</script>";
    exit();
}

if (!isset($_GET['booking_id'])) {
    echo "<script>alert('Invalid booking.'); window.location.href='my_account.php';</script>";
    exit();
}

$booking_id = $_GET['booking_id'];
$user_id = $_SESSION['user_id'];

// Fetch booking details
$query = "SELECT 
            b.*, 
            f.flight_name, 
            f.flight_id,
            f.source, 
            f.destination, 
            f.date, 
            f.departure_time, 
            f.arrival_time
          FROM bookings b 
          JOIN flights f ON b.flight_id = f.id 
          WHERE b.id = '$booking_id' AND b.user_id = '$user_id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Booking not found.'); window.location.href='my_account.php';</script>";
    exit();
}

$booking = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        /* Copy the root and basic styles from my_account.php */
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
        
        /* Navbar styles from my_account.php */
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
        
        .nav-links {
            display: flex;
            gap: 1rem;
        }
        
        .back-button, .home-button, .download-button {
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
        
        .back-button i, .home-button i, .download-button i {
            margin-right: 8px;
        }
        
        .back-button:hover, .home-button:hover, .download-button:hover {
            background-color: var(--primary-dark);
        }
        
        .download-button {
            background-color: var(--secondary);
        }
        
        .download-button:hover {
            background-color: #e67e22;
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
        
        /* Booking details specific styles */
        .booking-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .booking-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-size: 1.1rem;
            color: var(--dark);
            font-weight: 500;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-booked {
            background-color: var(--success);
            color: white;
        }
        
        .status-cancelled {
            background-color: var(--danger);
            color: white;
        }
        
        .action-bar {
            display: flex;
            justify-content: flex-end;
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .booking-card {
                padding: 1rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media print {
            .navbar, .action-bar, .download-button {
                display: none;
            }
            
            body {
                background-color: white;
            }
            
            .container {
                width: 100%;
                max-width: none;
                padding: 0;
                margin: 0;
            }
            
            .booking-card {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container navbar-content">
            <div class="logo">AirBooking</div>
            <div class="nav-links">
                <a href="my_account.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <a href="user_dashboard.php" class="home-button">
                    <i class="fas fa-home"></i> Home
                </a>
            </div>
        </div>
    </nav>

    <header class="page-header">
        <div class="container">
            <h1 class="page-title">Booking Details</h1>
        </div>
    </header>

    <main class="container">
        <div id="booking-content">
            <div class="booking-card">
                <div class="booking-section">
                    <h2 class="section-title">Flight Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Flight ID</span>
                            <span class="info-value"><?= htmlspecialchars($booking['flight_id']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Flight Name</span>
                            <span class="info-value"><?= htmlspecialchars($booking['flight_name']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Route</span>
                            <div class="route-info" style="display: flex; flex-direction: column; gap: 4px; padding: 8px 0; align-items: center; text-align: center;">
                                <span class="route-cities" style="font-weight: 500; color: #1a365d; width: 100%;">
                                    <?= htmlspecialchars($booking['source']) ?>
                                </span>
                                <div class="route-arrow" style="color: #0052cc; font-size: 1.5rem; margin: 8px 0; display: flex; justify-content: center;">
                                    <i class="fas fa-plane" style="transform: rotate(90deg);"></i>
                                </div>
                                <span class="route-cities" style="font-weight: 500; color: #1a365d; width: 100%;">
                                    <?= htmlspecialchars($booking['destination']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date</span>
                            <span class="info-value"><?= date('d M Y', strtotime($booking['date'])) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Departure Time</span>
                            <span class="info-value"><?= date('H:i', strtotime($booking['departure_time'])) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Arrival Time</span>
                            <span class="info-value"><?= date('H:i', strtotime($booking['arrival_time'])) ?></span>
                        </div>
                    </div>
                </div>

                <div class="booking-section">
                    <h2 class="section-title">Passenger Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Passenger Name</span>
                            <span class="info-value"><?= htmlspecialchars($booking['name']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Aadhar Number</span>
                            <span class="info-value"><?= htmlspecialchars($booking['aadhar_number']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Age</span>
                            <span class="info-value"><?= htmlspecialchars($booking['age']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="booking-section">
                    <h2 class="section-title">Booking Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Booking ID</span>
                            <span class="info-value"><?= htmlspecialchars($booking['id']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Seat Class</span>
                            <span class="info-value"><?= htmlspecialchars($booking['seat_class']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Seat Type</span>
                            <span class="info-value"><?= htmlspecialchars($booking['seat_type']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Seat Numbers</span>
                            <span class="info-value">
                                <?= !empty($booking['seat_numbers']) ? htmlspecialchars($booking['seat_numbers']) : htmlspecialchars($booking['seat_number']) ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Total Seats</span>
                            <span class="info-value"><?= htmlspecialchars($booking['seat_count']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Total Amount</span>
                            <span class="info-value">₹<?= htmlspecialchars($booking['total_price']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <span class="status-badge <?= strtolower($booking['status']) === 'booked' ? 'status-booked' : 'status-cancelled' ?>">
                                <?= htmlspecialchars($booking['status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="action-bar">
            <button onclick="downloadPDF()" class="download-button">
                <i class="fas fa-download"></i> Download PDF
            </button>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.booking-section');
            sections.forEach((section, index) => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';
                section.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                
                setTimeout(() => {
                    section.style.opacity = '1';
                    section.style.transform = 'translateY(0)';
                }, 100 + (index * 200));
            });
        });
        
        function downloadPDF() {
            // Set filename with booking details
            const fileName = 'AirBooking-Ticket-<?= htmlspecialchars($booking['id']) ?>.pdf';
            
            // Element to convert to PDF
            const element = document.getElementById('booking-content');
            
            // Configuration for html2pdf
            const opt = {
                margin: 1,
                filename: fileName,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'cm', format: 'a4', orientation: 'portrait' }
            };
            
            // Generate PDF
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>