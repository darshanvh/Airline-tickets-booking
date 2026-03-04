<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate username (only letters and spaces)
    if (!preg_match("/^[a-zA-Z ]+$/", $username)) {
        $error_message = "❗ Username can only contain letters and spaces.";
    }
    // Check password length
    else if (strlen($password) < 5) {
        $error_message = "❗ Password must be at least 5 characters long.";
    } else {
        // Check if phone number already exists
        $check_user = mysqli_query($conn, "SELECT * FROM users WHERE phone='$phone'");
        if (mysqli_num_rows($check_user) > 0) {
            $error_message = "❗ Phone number already registered. Please log in.";
        } else {
            if ($password === $confirm_password) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $query = "INSERT INTO users (username, phone, password) VALUES ('$username', '$phone', '$hashed_password')";
                if (mysqli_query($conn, $query)) {
                    // Clear POST data and redirect
                    $_POST = array();
                    header("Location: login.php");
                    exit();
                } else {
                    $error_message = "❗ Error: " . mysqli_error($conn);
                }
            } else {
                $error_message = "❗ Passwords do not match.";
            }
        }
    }
    // Clear form data if there was an error
    if (isset($error_message)) {
        $_POST = array();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Poppins', sans-serif;
        }
        .signup-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
            margin: auto;
        }
        .form-floating {
            margin-bottom: 20px;
        }
        .btn-signup {
            background: #764ba2;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            width: 100%;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
        }
        .btn-signup:hover {
            background: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .alert {
            border-radius: 15px;
            margin-bottom: 20px;
        }
        .links-container {
            margin-top: 20px;
            text-align: center;
        }
        .links-container a {
            color: #764ba2;
            text-decoration: none;
            transition: color 0.3s;
        }
        .links-container a:hover {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="signup-container">
            <h2 class="text-center mb-4">Create Account</h2>

            <?php if (!empty($error_message)) : ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="signup.php" method="POST" id="signupForm" autocomplete="off">
                <div class="form-floating">
                    <input type="text" name="username" class="form-control" id="username" 
                           placeholder="Username" pattern="[a-zA-Z ]+" 
                           title="Username can only contain letters and spaces"
                           required autocomplete="off">
                    <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                </div>

                <div class="form-floating">
                    <input type="text" name="phone" class="form-control" id="phone" placeholder="Phone" pattern="\d{10}" required autocomplete="off">
                    <label for="phone"><i class="fas fa-phone me-2"></i>Phone</label>
                </div>

                <div class="form-floating">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password" minlength="5" required autocomplete="off">
                    <label for="password"><i class="fas fa-lock me-2"></i>Password (min. 5 characters)</label>
                </div>

                <div class="form-floating">
                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm Password" required autocomplete="off">
                    <label for="confirm_password"><i class="fas fa-lock me-2"></i>Confirm Password</label>
                </div>

                <button type="submit" class="btn btn-signup mt-3">
                    <i class="fas fa-user-plus me-2"></i>Sign Up
                </button>
            </form>

            <div class="links-container">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <script>
            document.getElementById('signupForm').addEventListener('submit', function(e) {
                const username = document.getElementById('username').value;
                const phone = document.getElementById('phone').value;
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                // Username validation
                if (!/^[a-zA-Z ]+$/.test(username)) {
                    e.preventDefault();
                    alert('Username can only contain letters and spaces!');
                    return;
                }
                
                if (phone.length !== 10 || !/^\d+$/.test(phone)) {
                    e.preventDefault();
                    alert('Please enter a valid 10-digit phone number!');
                    return;
                }

                if (password.length < 5) {
                    e.preventDefault();
                    alert('Password must be at least 5 characters long!');
                    return;
                }

                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                    return;
                }
            });
        </script>
    </script>
</body>
</html>
