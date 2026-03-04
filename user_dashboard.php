<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
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
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .deal-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .route-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .route-info i {
            color: #0052cc;
            font-size: 20px;
        }

        .route-cities {
            font-size: 18px;
            font-weight: 600;
            color: #1a365d;
        }

        .btn-book {
            margin-top: auto;
            display: block;
            background: #0052cc;
            color: white;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-book:hover {
            background: #003d99;
            transform: translateY(-2px);
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
            justify-content: center;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            background-color: #2c3e50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-icon i {
            font-size: 18px;
            line-height: 1;
        }

        .social-icon:hover {
            background-color: #0052cc;
            transform: translateY(-2px);
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
<?php 
// Include the navbar at the very top
include 'user_navbar.php'; 
?>
    
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
    
    <!-- Move Our Services section up -->
    <section class="container" style="margin-top: -50px; position: relative; z-index: 10;">
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
    
    <!-- Move Featured Deals section up -->
    <section class="bg-gray" style="margin-top: 20px;">
        <div class="container">
            <h2>Featured Deals</h2>
            
            <!-- Passenger Flights -->
            <h3 class="text-center mb-4" style="color: #1a365d;">Featured Passenger Flights</h3>
            <div class="grid md-grid-3 mb-5">
                <?php
                $featured_query = "SELECT *, DATE_FORMAT(date, '%d %b %Y') as formatted_date FROM flights 
                                 WHERE status = 'Active' AND date > CURDATE() 
                                 ORDER BY date ASC LIMIT 3";
                $featured_result = mysqli_query($conn, $featured_query);
                
                while ($flight = mysqli_fetch_assoc($featured_result)) {
                    ?>
                    <div class="card deal-card">
                        <div class="deal-info">
                            <div>
                                <div class="route-info" style="display: flex; flex-direction: column; gap: 4px; padding: 8px 0; align-items: center; text-align: center;">
                                    <span class="route-cities" style="font-weight: 500; color: #1a365d; width: 100%;">
                                        <?php echo $flight['source']; ?>
                                    </span>
                                    <div class="route-arrow" style="color: #0052cc; font-size: 1.5rem; margin: 8px 0; display: flex; justify-content: center;">
                                        <i class="fas fa-plane" style="transform: rotate(45deg);"></i>
                                    </div>
                                    <span class="route-cities" style="font-weight: 500; color: #1a365d; width: 100%;">
                                        <?php echo $flight['destination']; ?>
                                    </span>
                                </div>
                                <p style="color: #666; margin-top: 10px; text-align: center;"><?php echo $flight['formatted_date']; ?></p>
                            </div>
                        </div>
                        <div class="price-boxes" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 15px 0;">
                            <div style="background: #f8f9fa; padding: 10px; border-radius: 8px; text-align: center;">
                                <p style="color: #666;">Economy</p>
                                <p style="color: #0052cc; font-size: 1.2em;">₹<?php echo number_format($flight['economy_price']); ?></p>
                            </div>
                            <div style="background: #f8f9fa; padding: 10px; border-radius: 8px; text-align: center;">
                                <p style="color: #666;">Business</p>
                                <p style="color: #0052cc; font-size: 1.2em;">₹<?php echo number_format($flight['business_price']); ?></p>
                            </div>
                        </div>
                        <a href="search_flight.php?flight_id=<?php echo $flight['flight_id']; ?>&source=<?php echo urlencode($flight['source']); ?>&destination=<?php echo urlencode($flight['destination']); ?>&date=<?php echo $flight['date']; ?>" 
                           class="btn-book">
                            <i class="ri-plane-line"></i>Book Flight
                        </a>
                    </div>
                    <?php
                }
                ?>
            </div>

            <!-- Cargo Flights -->
            <h3 class="text-center mb-4" style="color: #1a365d;">Featured Cargo Services</h3>
            <div class="grid md-grid-3">
                <?php
                $cargo_query = "SELECT *, DATE_FORMAT(date, '%d %b %Y') as formatted_date FROM cargo_flights 
                               WHERE status = 'Active' AND date > CURDATE() 
                               ORDER BY date ASC LIMIT 3";
                $cargo_result = mysqli_query($conn, $cargo_query);
                
                while ($cargo = mysqli_fetch_assoc($cargo_result)) {
                    ?>
                    <!-- Cargo Flights -->
                    <div class="card deal-card">
                        <div class="deal-info">
                            <div>
                                <div class="route-info" style="display: flex; flex-direction: column; gap: 4px; padding: 8px 0; align-items: center; text-align: center;">
                                    <span class="route-cities" style="font-weight: 500; color: #1a365d; width: 100%;">
                                        <?php echo $cargo['source']; ?>
                                    </span>
                                    <div class="route-arrow" style="color: #0052cc; font-size: 1.5rem; margin: 8px 0; display: flex; justify-content: center;">
                                        <i class="fas fa-plane" style="transform: rotate(45deg);"></i>
                                    </div>
                                    <span class="route-cities" style="font-weight: 500; color: #1a365d; width: 100%;">
                                        <?php echo $cargo['destination']; ?>
                                    </span>
                                </div>
                                <p style="color: #666; margin-top: 10px; text-align: center;"><?php echo $cargo['formatted_date']; ?></p>
                            </div>
                        </div>
                        <div class="price-boxes" style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center; margin: 15px 0;">
                            <p style="color: #666;">Price per kg</p>
                            <p style="color: #0052cc; font-size: 1.4em;">₹<?php echo number_format($cargo['price_per_kg']); ?></p>
                            <p style="color: #666; margin-top: 5px;">Available: <?php echo $cargo['available_weight']; ?> kg</p>
                        </div>
                        <a href="book_cargo.php?source=<?php echo urlencode($cargo['source']); ?>&destination=<?php echo urlencode($cargo['destination']); ?>&date=<?php echo urlencode($cargo['date']); ?>" 
                           class="btn-book" style="display: block; background: #00875a; color: white; text-align: center; padding: 12px; border-radius: 8px; text-decoration: none; transition: all 0.3s ease;">
                            <i class="ri-truck-line" style="margin-right: 8px;"></i>Book Cargo
                        </a>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </section>
    
    <!-- Move Why Choose SkyWings section up -->
    <section class="container" style="margin-top: 20px;">
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
    
    <!-- Move Our Fleet section up -->
    <section class="bg-gray" style="margin-top: 20px;">
        <div class="container">
            <h2>Our Flights</h2>
            <div class="grid md-grid-3">
                <div class="card">
                    <img src="https://public.readdy.ai/ai/img_res/5c9a7ff1451e9cd84b7913821270dcd1.jpg" alt="Passenger Flights" />
                    <div class="card-content">
                        <h3>Passenger Flights</h3>
                        <ul>
                            <li><i class="ri-check-line"></i>Comfortable Seating</li>
                            <li><i class="ri-check-line"></i>In-flight Entertainment</li>
                            <li><i class="ri-check-line"></i>Complimentary Meals</li>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <img src="https://public.readdy.ai/ai/img_res/d35a5c063a58fb1b137b9015439c43e8.jpg" alt="Cargo Flights" />
                    <div class="card-content">
                        <h3>Cargo Flights</h3>
                        <ul>
                            <li><i class="ri-check-line"></i>Express Delivery</li>
                            <li><i class="ri-check-line"></i>Secure Transportation</li>
                            <li><i class="ri-check-line"></i>Real-time Tracking</li>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <img src="https://public.readdy.ai/ai/img_res/6bc2eb54050e22aef3b2b9e15b5af658.jpg" alt="Flight Services" />
                    <div class="card-content">
                        <h3>Flight Services</h3>
                        <ul>
                            <li><i class="ri-check-line"></i>24/7 Customer Support</li>
                            <li><i class="ri-check-line"></i>Priority Boarding</li>
                            <li><i class="ri-check-line"></i>Special Assistance</li>
                        </ul>
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
