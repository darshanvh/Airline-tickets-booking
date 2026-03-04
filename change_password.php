<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "air17");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$showPasswordFields = false;
$phone = $email = $favorite_place = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify'])) {
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $favorite_place = $_POST['favorite_place'];

    $query = "SELECT * FROM users WHERE phone='$phone' AND email='$email' AND favorite_place='$favorite_place'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['reset_phone'] = $phone; // Store phone in session to update password
        $showPasswordFields = true;
    } else {
        $error = "Details do not match! Please try again.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset'])) {
    if (!isset($_SESSION['reset_phone'])) {
        die("Unauthorized access!");
    }

    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $phone = $_SESSION['reset_phone'];

        $update_query = "UPDATE users SET password='$hashed_password' WHERE phone='$phone'";
        if (mysqli_query($conn, $update_query)) {
            session_destroy();
            echo "<script>alert('Password changed successfully!'); window.location='login.php';</script>";
        } else {
            $error = "Error updating password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Consistent styling with booking_details.php and edit_profile.php */
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
        
        .btn {
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
            font-size: 1rem;
            width: auto;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn:hover {
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
        
        .password-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 600px;
        }
        
        .error-message {
            background-color: #fef2f2;
            color: var(--danger);
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--danger);
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.9rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--gray);
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(52, 144, 220, 0.25);
        }
        
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
        }
        
        .submit-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
            font-size: 1rem;
        }
        
        .submit-btn:hover {
            background-color: var(--primary-dark);
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .password-card {
                padding: 1.5rem;
                margin: 1rem;
            }
            
            .action-bar {
                flex-direction: column;
                gap: 1rem;
            }
            
            .action-bar .btn {
                width: 100%;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.querySelector('.password-card');
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</head>
<body>
    <nav class="navbar">
        <div class="container navbar-content">
            <div class="logo">AirBooking</div>
            <div class="nav-links">
                <a href="edit_profile.php" class="btn">
                    <i class="fas fa-arrow-left"></i> Back to Profile
                </a>
                <a href="user_dashboard.php" class="btn">
                    <i class="fas fa-home"></i> Home
                </a>
            </div>
        </div>
    </nav>

    <header class="page-header">
        <div class="container">
            <h1 class="page-title">Change Password</h1>
        </div>
    </header>

    <main class="container">
        <div class="password-card">
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (!$showPasswordFields): ?>
                <div class="form-section">
                    <h2 class="section-title">Verify Your Identity</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Favorite Place</label>
                            <input type="text" name="favorite_place" class="form-control" required>
                        </div>
                        <button type="submit" name="verify" class="submit-btn">
                            <i class="fas fa-check-circle"></i> Verify
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="form-section">
                    <h2 class="section-title">Set New Password</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" name="reset" class="submit-btn">
                            <i class="fas fa-save"></i> Save New Password
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>