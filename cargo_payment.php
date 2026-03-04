<?php 
include 'db.php';  
if (!isset($_GET['flight_id']) || !isset($_GET['price'])) {
    echo "Invalid request.";
    exit();
}

$flight_id = $_GET['flight_id'];
$price_per_kg = $_GET['price'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargo Booking</title>
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
        
        .button-primary {
            color: #fff;
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .button-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        @media (max-width: 768px) {
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
            <h1 class="page-title">Cargo Booking</h1>
            <p class="page-subtitle">Enter your cargo details for booking</p>
        </div>
    </header>

    <main class="container">
        <div class="card">
            <div class="card-header">
                <h3>Cargo Booking Details</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="cargo_summary.php">
                    <input type="hidden" name="flight_id" value="<?= $flight_id ?>">
                    <input type="hidden" name="price_per_kg" value="<?= $price_per_kg ?>">
                    
                    <div class="form-group">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control" required pattern="\d{10}" title="Enter a valid 10-digit phone number">
                        <div class="form-error" id="phone_error" style="display: none;">Please enter a valid 10-digit phone number.</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" id="address" name="address" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="aadhar_number" class="form-label">Aadhaar Number</label>
                        <input type="text" id="aadhar_number" name="aadhar_number" class="form-control" required pattern="\d{12}" title="Enter a valid 12-digit Aadhaar number">
                        <div class="form-error" id="aadhar_error" style="display: none;">Please enter a valid 12-digit Aadhaar number.</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="weight" class="form-label">Weight to Book (in KG)</label>
                        <input type="number" id="weight" name="weight" min="1" max="110" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="button button-primary">
                        Proceed to Summary <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Validate phone number and Aadhaar number format
        document.addEventListener('DOMContentLoaded', function() {
            // Phone validation
            document.getElementById('phone').addEventListener('input', function(e) {
                const phoneInput = e.target;
                const phoneError = document.getElementById('phone_error');
                
                // Only allow numbers
                phoneInput.value = phoneInput.value.replace(/[^0-9]/g, '');
                
                // Check length
                if (phoneInput.value.length > 0 && phoneInput.value.length !== 10) {
                    phoneError.style.display = 'block';
                } else {
                    phoneError.style.display = 'none';
                }
            });
            
            // Aadhaar validation
            document.getElementById('aadhar_number').addEventListener('input', function(e) {
                const aadharInput = e.target;
                const aadharError = document.getElementById('aadhar_error');
                
                // Only allow numbers
                aadharInput.value = aadharInput.value.replace(/[^0-9]/g, '');
                
                // Check length
                if (aadharInput.value.length > 0 && aadharInput.value.length !== 12) {
                    aadharError.style.display = 'block';
                } else {
                    aadharError.style.display = 'none';
                }
            });
            
            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                let isValid = true;
                
                const phone = document.getElementById('phone').value;
                if (phone.length !== 10) {
                    document.getElementById('phone_error').style.display = 'block';
                    isValid = false;
                }
                
                const aadhar = document.getElementById('aadhar_number').value;
                if (aadhar.length !== 12) {
                    document.getElementById('aadhar_error').style.display = 'block';
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>