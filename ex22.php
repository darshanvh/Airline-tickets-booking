<?php
include 'db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SkyWings - Book Flights & Cargo Services</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f9fafb;
            font-family: Arial, sans-serif;
        }

        /* Top Navigation Bar */
        .topnav {
            background-color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-container img {
            width: 50px;
            height: 50px;
            margin-right: 15px;
            border-radius: 50%;
        }

        .logo-container span {
            font-size: 22px;
            font-weight: bold;
            color: white;
        }

        .menu-toggle {
            font-size: 25px;
            color: white;
            cursor: pointer;
            display: block;
        }

        .dropdown-menu {
            position: fixed;
            top: 70px;
            right: 20px;
            width: 200px;
            background-color: #222;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: none;
            z-index: 1000;
        }

        .dropdown-menu a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 15px 20px;
            font-size: 18px;
            border-bottom: 1px solid #444;
        }

        .dropdown-menu a:hover {
            background-color: #555;
        }

        .container {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 16px;
            padding-right: 16px;
            margin-top: 70px; /* Offset for the topnav */
        }

        header {
            position: relative;
            height: 600px;
            overflow: hidden;
        }

        #carousel {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        #carousel img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: opacity 0.5s ease;
            opacity: 0;
        }

        #carousel img.active {
            opacity: 1;
        }

        .header-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.7), transparent);
        }

        .header-content {
            height: 100%;
            display: flex;
            align-items: center;
        }

        .header-text {
            max-width: 672px;
            color: #ffffff;
        }

        .header-text h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 24px;
        }

        .header-text p {
            font-size: 20px;
            margin-bottom: 32px;
        }

        .search-container {
            margin-top: -80px;
            position: relative;
            z-index: 10;
        }

        .search-box {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            padding: 24px;
        }

        .search-tabs {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }

        .search-tab {
            flex: 1;
            padding: 12px 24px;
            text-align: center;
            border-radius: 9999px;
            transition: all 0.3s ease;
            font-weight: 500;
            cursor: pointer;
            background-color: #e5e7eb;
            color: #374151;
        }

        .search-tab.active {
            background-color: #0052cc;
            color: #ffffff;
        }

        .search-tab i {
            margin-right: 8px;
        }

        .search-form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        @media (min-width: 768px) {
            .search-form {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .search-form.hidden {
            display: none;
        }

        .form-group {
            position: relative;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #4b5563;
            margin-bottom: 4px;
        }

        .input-wrapper {
            position: relative;
        }

        input {
            width: 100%;
            padding: 8px 16px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
        }

        input:focus {
            outline: none;
            border-color: #0052cc;
            box-shadow: 0 0 0 2px rgba(0, 82, 204, 0.5);
        }

        .input-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .date-input::-webkit-calendar-picker-indicator {
            opacity: 0;
            position: absolute;
            right: 0;
            top: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .search-button {
            margin-top: 24px;
            text-align: center;
        }

        button {
            background-color: #0052cc;
            color: #ffffff;
            padding: 12px 32px;
            border-radius: 8px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: rgba(0, 82, 204, 0.9);
        }

        section {
            padding: 64px 0;
        }

        h2 {
            font-size: 30px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 48px;
        }

        .grid {
            display: grid;
            gap: 32px;
        }

        @media (min-width: 768px) {
            .md-grid-2 {
                grid-template-columns: repeat(2, 1fr);
            }
            .md-grid-3 {
                grid-template-columns: repeat(3, 1fr);
            }
            .md-grid-4 {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card img {
            width: 100%;
            height: 192px;
            object-fit: cover;
        }

        .card-content {
            padding: 24px;
        }

        h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        ul {
            list-style: none;
        }

        li {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        li i {
            color: #0052cc;
            margin-right: 8px;
        }

        .bg-gray {
            background-color: #f3f4f6;
        }

        .deal-card {
            padding: 24px;
        }

        .deal-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .deal-info h3 {
            font-size: 18px;
            margin-bottom: 0;
        }

        .price {
            font-size: 24px;
            font-weight: 700;
            color: #0052cc;
        }

        .price-label {
            font-size: 12px;
            color: #6b7280;
        }

        .bg-primary {
            background-color: #0052cc;
            color: #ffffff;
        }

        .subscribe-form {
            display: flex;
        }

        .subscribe-form input {
            flex: 1;
            border-radius: 4px 0 0 4px;
            color: #111827;
        }

        .subscribe-form button {
            background-color: #00875a;
            border-radius: 0 4px 4px 0;
            padding: 8px 24px;
        }

        .subscribe-form button:hover {
            background-color: rgba(0, 135, 90, 0.9);
        }

        .app-buttons {
            display: flex;
            gap: 16px;
        }

        .app-button {
            background-color: #000000;
            padding: 8px 24px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 82, 204, 0.1);
            border-radius: 9999px;
        }

        .feature-icon i {
            font-size: 24px;
            color: #0052cc;
        }

        .text-center {
            text-align: center;
        }

        .text-gray {
            color: #6b7280;
        }

        @media (min-width: 768px) {
            .md-text-right {
                text-align: right;
            }
        }

/* Reviews Section */
.reviews-section {
    padding: 64px 0;
    background-color: #ffffff;
    overflow: hidden;
}

.reviews-slider {
    display: flex;
    width: max-content; /* Allows the slider to expand based on content */
    animation: slide 20s linear infinite; /* Smooth scrolling animation */
}

.review-card {
    flex: 0 0 300px; /* Fixed width for each card */
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin: 0 20px;
    text-align: center;
}

.review-card img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin-bottom: 10px;
}

.review-card p {
    margin-bottom: 10px;
    color: #4b5563;
}

.review-author {
    font-weight: 600;
    color: #374151;
}

/* Animation for sliding effect */
@keyframes slide {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%); /* Moves half the total width for seamless loop */
    }
}

/* Optional: Pause animation on hover */
.reviews-slider:hover {
    animation-play-state: paused;
}
        /* Improved Footer */
        footer {
            background-color: #1a2a44;
            color: #ffffff;
            padding: 48px 0;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 32px;
        }

        .footer-section h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .footer-section p,
        .footer-section a {
            color: #d1d5db;
            text-decoration: none;
            display: block;
            margin-bottom: 8px;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: #ffffff;
        }

        .social-icons {
            display: flex;
            gap: 16px;
            margin-top: 16px;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            background-color: #2c3e50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: background-color 0.3s ease;
        }

        .social-icon:hover {
            background-color: #0052cc;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 24px;
            border-top: 1px solid #2c3e50;
            margin-top: 32px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="topnav">
        <div class="logo-container">
            <img src="1.jpg" alt="Airline Logo">
            <span>SkyNest Airlines</span>
        </div>
        <span class="menu-toggle" onclick="toggleMenu()">☰</span>
    </div>

    <!-- Dropdown Menu -->
    <div class="dropdown-menu" id="dropdownMenu">
        <a href="edit_profile.php">Profile</a>
        <a href="available_flights.php">Available Flights</a>
        <a href="avc.php">Available Cargo Flights</a>
        <a href="search_flight.php">Book Flight</a>
        <a href="cancel_booking.php">Cancel Book Flight</a>
        <a href="book_cargo.php">Book Cargo Flight</a>
        <a href="cargo_cancel.php">Cancel Cargo Flight</a>
        <a href="my_account.php">My Account</a>
        <a href="my_cargo_account.php">My Cargo Account</a>
        <a href="user_feedback.php">Feedback</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <header>
        <div id="carousel">
            <img src="https://public.readdy.ai/ai/img_res/27fe87eef0ed10198b7d66bcbd85ee61.jpg" alt="Airport Terminal" class="active" />
            <img src="https://public.readdy.ai/ai/img_res/27fe87eef0ed10198b7d66bcbd85ee61.jpg" alt="Business Class" />
            <img src="https://public.readdy.ai/ai/img_res/607f9a685f169c3849c8e4ae4300427b.jpg" alt="Flying" />
        </div>
        <div class="header-overlay">
            <div class="container header-content">
                <div class="header-text">
                    <h1>Your Journey Begins Here</h1>
                    <p>Experience seamless travel and cargo solutions worldwide. Book your next flight or ship your cargo with confidence.</p>
                </div>
            </div>
        </div>
    </header>
    <div class="container search-container">
        <div class="search-box">
            <div class="search-tabs">
                <button class="search-tab active" data-tab="passenger">
                    <i class="ri-user-line"></i> Passenger Flights
                </button>
                <button class="search-tab" data-tab="cargo">
                    <i class="ri-truck-line"></i> Cargo Flights
                </button>
            </div>
            <div id="passenger-form" class="search-form">
                <div class="form-group">
                    <label>From</label>
                    <div class="input-wrapper">
                        <input type="text" placeholder="Departure City" />
                        <i class="ri-flight-takeoff-line input-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label>To</label>
                    <div class="input-wrapper">
                        <input type="text" placeholder="Arrival City" />
                        <i class="ri-flight-land-line input-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <div class="input-wrapper">
                        <input type="date" class="date-input" />
                        <i class="ri-calendar-line input-icon"></i>
                    </div>
                </div>
            </div>
            <div id="cargo-form" class="search-form hidden">
                <div class="form-group">
                    <label>From</label>
                    <div class="input-wrapper">
                        <input type="text" placeholder="Origin" />
                        <i class="ri-map-pin-line input-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label>To</label>
                    <div class="input-wrapper">
                        <input type="text" placeholder="Destination" />
                        <i class="ri-map-pin-line input-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label>Weight (kg)</label>
                    <input type="number" min="0" />
                </div>
            </div>
            <div class="search-button">
                <button>Search Flights</button>
            </div>
        </div>
    </div>
    <section class="container">
        <h2>Our Services</h2>
        <div class="grid md-grid-2">
            <div class="card">
                <img src="https://public.readdy.ai/ai/img_res/27fe87eef0ed10198b7d66bcbd85ee61.jpg" alt="Passenger Flights" />
                <div class="card-content">
                    <h3>Passenger Flights</h3>
                    <ul>
                        <li><i class="ri-check-line"></i>Flexible booking options</li>
                        <li><i class="ri-check-line"></i>24/7 customer support</li>
                        <li><i class="ri-check-line"></i>Complimentary meals</li>
                    </ul>
                </div>
            </div>
            <div class="card">
                <img src="https://public.readdy.ai/ai/img_res/dd40673f8427d5a146f736e626bfde7a.jpg" alt="Cargo Services" />
                <div class="card-content">
                    <h3>Cargo Services</h3>
                    <ul>
                        <li><i class="ri-check-line"></i>Express shipping</li>
                        <li><i class="ri-check-line"></i>Real-time tracking</li>
                        <li><i class="ri-check-line"></i>Door-to-door delivery</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="bg-gray">
        <div class="container">
            <h2>Featured Deals</h2>
            <div class="grid md-grid-3">
                <div class="card deal-card">
                    <div class="deal-info">
                        <div>
                            <h3>London → New York</h3>
                            <p class="text-gray">Direct Flight</p>
                        </div>
                        <div>
                            <p class="price">$499</p>
                            <p class="price-label">Round Trip</p>
                        </div>
                    </div>
                    <button>Book Now</button>
                </div>
                <div class="card deal-card">
                    <div class="deal-info">
                        <div>
                            <h3>Paris → Dubai</h3>
                            <p class="text-gray">Direct Flight</p>
                        </div>
                        <div>
                            <p class="price">$599</p>
                            <p class="price-label">Round Trip</p>
                        </div>
                    </div>
                    <button>Book Now</button>
                </div>
                <div class="card deal-card">
                    <div class="deal-info">
                        <div>
                            <h3>Tokyo → Singapore</h3>
                            <p class="text-gray">Direct Flight</p>
                        </div>
                        <div>
                            <p class="price">$399</p>
                            <p class="price-label">Round Trip</p>
                        </div>
                    </div>
                    <button>Book Now</button>
                </div>
            </div>
        </div>
    </section>
    <section class="container">
        <h2>Why Choose SkyWings?</h2>
        <div class="grid md-grid-4">
            <div class="text-center">
                <div class="feature-icon">
                    <i class="ri-shield-check-line"></i>
                </div>
                <h3>Safety First</h3>
                <p class="text-gray">Highest safety standards for both passenger and cargo operations</p>
            </div>
            <div class="text-center">
                <div class="feature-icon">
                    <i class="ri-global-line"></i>
                </div>
                <h3>Global Network</h3>
                <p class="text-gray">Extensive route network covering major destinations worldwide</p>
            </div>
            <div class="text-center">
                <div class="feature-icon">
                    <i class="ri-time-line"></i>
                </div>
                <h3>On-Time Performance</h3>
                <p class="text-gray">Reliable schedules and punctual service delivery</p>
            </div>
            <div class="text-center">
                <div class="feature-icon">
                    <i class="ri-customer-service-2-line"></i>
                </div>
                <h3>24/7 Support</h3>
                <p class="text-gray">Round-the-clock customer service and cargo tracking</p>
            </div>
        </div>
    </section>
    <section class="bg-gray">
        <div class="container">
            <h2>Our Fleet</h2>
            <div class="grid md-grid-3">
                <div class="card">
                    <img src="https://public.readdy.ai/ai/img_res/5c9a7ff1451e9cd84b7913821270dcd1.jpg" alt="Passenger Aircraft" />
                    <div class="card-content">
                        <h3>Passenger Fleet</h3>
                        <ul>
                            <li><i class="ri-check-line"></i>Modern Airbus & Boeing Aircraft</li>
                            <li><i class="ri-check-line"></i>Premium Cabin Configuration</li>
                            <li><i class="ri-check-line"></i>Latest Entertainment Systems</li>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <img src="https://public.readdy.ai/ai/img_res/d35a5c063a58fb1b137b9015439c43e8.jpg" alt="Cargo Aircraft" />
                    <div class="card-content">
                        <h3>Cargo Fleet</h3>
                        <ul>
                            <li><i class="ri-check-line"></i>Dedicated Freighter Aircraft</li>
                            <li><i class="ri-check-line"></i>Temperature Controlled Capacity</li>
                            <li><i class="ri-check-line"></i>Special Cargo Handling</li>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <img src="https://public.readdy.ai/ai/img_res/6bc2eb54050e22aef3b2b9e15b5af658.jpg" alt="Maintenance" />
                    <div class="card-content">
                        <h3>Maintenance</h3>
                        <ul>
                            <li><i class="ri-check-line"></i>State-of-the-art Facilities</li>
                            <li><i class="ri-check-line"></i>Certified Engineers</li>
                            <li><i class="ri-check-line"></i>Regular Maintenance Checks</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="container">
        <div class="grid md-grid-3">
            <div>
                <h3>Latest Updates</h3>
                <div>
                    <div class="flex items-start">
                        <i class="ri-notification-3-line"></i>
                        <div>
                            <p>Flight Status Updates</p>
                            <p class="text-gray">Real-time flight information</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="ri-cloud-line"></i>
                        <div>
                            <p>Weather Alerts</p>
                            <p class="text-gray">Current weather conditions</p>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <h3>Quick Links</h3>
                <div>
                    <div class="flex items-start">
                        <i class="ri-book-open-line"></i>
                        <div>
                            <p>Booking Guide</p>
                            <p class="text-gray">How to book your flight</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="ri-truck-line"></i>
                        <div>
                            <p>Cargo Guidelines</p>
                            <p class="text-gray">Shipping instructions</p>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <h3>Contact Support</h3>
                <div>
                    <div class="flex items-start">
                        <i class="ri-customer-service-2-line"></i>
                        <div>
                            <p>24/7 Support</p>
                            <p class="text-gray">+1 (800) 123-4567</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="ri-mail-line"></i>
                        <div>
                            <p>Email Support</p>
                            <p class="text-gray">support@skywings.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Reviews Section -->
<section class="reviews-section">
    <div class="container">
        <h2>Customer Reviews</h2>
        <div class="reviews-slider">
            <div class="review-card">
                <img src="https://picsum.photos/80/80?random=1" alt="Reviewer 1" />
                <p>"Amazing service and comfortable flights! Highly recommend SkyWings."</p>
                <p class="review-author">John Doe</p>
            </div>
            <div class="review-card">
                <img src="https://picsum.photos/80/80?random=2" alt="Reviewer 2" />
                <p>"Fast cargo delivery and great support team. Excellent experience."</p>
                <p class="review-author">Jane Smith</p>
            </div>
            <div class="review-card">
                <img src="https://picsum.photos/80/80?random=3" alt="Reviewer 3" />
                <p>"The best airline for international travel. Smooth and reliable."</p>
                <p class="review-author">Michael Brown</p>
            </div>
            <div class="review-card">
                <img src="https://picsum.photos/80/80?random=4" alt="Reviewer 4" />
                <p>"Fantastic crew and on-time flights every time. Great value!"</p>
                <p class="review-author">Sarah Lee</p>
            </div>
            <!-- Duplicate cards for seamless loop -->
            <div class="review-card">
                <img src="https://picsum.photos/80/80?random=1" alt="Reviewer 1" />
                <p>"Amazing service and comfortable flights! Highly recommend SkyWings."</p>
                <p class="review-author">John Doe</p>
            </div>
            <div class="review-card">
                <img src="https://picsum.photos/80/80?random=2" alt="Reviewer 2" />
                <p>"Fast cargo delivery and great support team. Excellent experience."</p>
                <p class="review-author">Jane Smith</p>
            </div>
            <div class="review-card">
                <img src="https://picsum.photos/80/80?random=3" alt="Reviewer 3" />
                <p>"The best airline for international travel. Smooth and reliable."</p>
                <p class="review-author">Michael Brown</p>
            </div>
            <div class="review-card">
                <img src="https://picsum.photos/80/80?random=4" alt="Reviewer 4" />
                <p>"Fantastic crew and on-time flights every time. Great value!"</p>
                <p class="review-author">Sarah Lee</p>
            </div>
        </div>
    </div>
</section>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About SkyWings</h3>
                    <p>Leading airline offering top-tier passenger and cargo services worldwide.</p>
                </div>
                <div class="footer-section">
                    <h3>Services</h3>
                    <a href="#">Passenger Flights</a>
                    <a href="#">Cargo Shipping</a>
                    <a href="#">Travel Deals</a>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <a href="tel:+18001234567">+1 (800) 123-4567</a>
                    <a href="mailto:support@skywings.com">support@skywings.com</a>
                    <a href="#">123 Aviation Lane, NY</a>
                </div>
                <div class="footer-section">
                    <h3>Follow Us</h3>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="ri-facebook-fill"></i></a>
                        <a href="#" class="social-icon"><i class="ri-twitter-fill"></i></a>
                        <a href="#" class="social-icon"><i class="ri-instagram-fill"></i></a>
                        <a href="#" class="social-icon"><i class="ri-linkedin-fill"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 SkyWings. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script>
        // Toggle Dropdown Menu
        function toggleMenu() {
            const dropdownMenu = document.getElementById("dropdownMenu");
            dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
        }

        // Close Dropdown Menu when clicking outside
        window.onclick = function (event) {
            const dropdownMenu = document.getElementById("dropdownMenu");
            if (event.target !== dropdownMenu && !event.target.matches('.menu-toggle')) {
                dropdownMenu.style.display = "none";
            }
        };

        // Carousel Functionality
        const images = document.querySelectorAll("#carousel img");
        let currentImage = 0;

        function showNextImage() {
            images[currentImage].classList.remove("active");
            currentImage = (currentImage + 1) % images.length;
            images[currentImage].classList.add("active");
        }

        images[0].classList.add("active");
        setInterval(showNextImage, 3000);

        // Search Tab Functionality
        const tabs = document.querySelectorAll(".search-tab");
        const forms = document.querySelectorAll(".search-form");
        tabs.forEach((tab) => {
            tab.addEventListener("click", () => {
                const target = tab.dataset.tab;
                tabs.forEach((t) => t.classList.remove("active"));
                tab.classList.add("active");
                forms.forEach((form) => {
                    form.classList.add("hidden");
                    if (form.id === `${target}-form`) {
                        form.classList.remove("hidden");
                    }
                });
            });
        });
    </script>
</body>
</html>