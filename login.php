<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
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
        .login-container {
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
        .btn-login {
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
        .btn-login:hover {
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
        <div class="login-container">
            <h2 class="text-center mb-4">Welcome Back</h2>

            <?php if (!empty($error_message)) : ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" id="loginForm" autocomplete="off">
                <div class="form-floating">
                    <input type="text" name="phone" class="form-control" id="phone" 
                           placeholder="Phone" pattern="\d{10}" required autocomplete="off">
                    <label for="phone"><i class="fas fa-phone me-2"></i>Phone</label>
                </div>

                <div class="form-floating">
                    <input type="password" name="password" class="form-control" id="password" 
                           placeholder="Password" required autocomplete="off">
                    <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                </div>

                <button type="submit" class="btn btn-login mt-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>

            <div class="links-container">
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
                <p><a href="change_password.php"><i class="fas fa-key me-2"></i>Forgot Password?</a></p>
            </div>

            <?php
            include 'db.php';
            session_start();

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $phone = $_POST['phone'];
                $password = $_POST['password'];

                $result = mysqli_query($conn, "SELECT * FROM users WHERE phone='$phone'");
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    if (password_verify($password, $row['password'])) {
                        $_SESSION['user_id'] = $row['id'];
                        $_POST = array(); // Clear POST data
                        header("Location: user_dashboard.php");
                        exit();
                    } else {
                        $_POST = array(); // Clear POST data
                        echo "<div class='alert alert-danger mt-3' role='alert'><i class='fas fa-exclamation-circle me-2'></i>Incorrect password.</div>";
                    }
                } else {
                    $_POST = array(); // Clear POST data
                    echo "<div class='alert alert-danger mt-3' role='alert'><i class='fas fa-exclamation-circle me-2'></i>User not found.</div>";
                }
            }
            ?>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const phone = document.getElementById('phone').value;
            
            if (phone.length !== 10 || !/^\d+$/.test(phone)) {
                e.preventDefault();
                alert('Please enter a valid 10-digit phone number!');
            }
        });
    </script>
</body>
</html>