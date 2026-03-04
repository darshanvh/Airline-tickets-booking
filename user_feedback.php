<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login to submit feedback.'); window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$user_query = "SELECT username, phone FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback'])) {
    $feedback = mysqli_real_escape_string($conn, trim($_POST['feedback']));

    if (!empty($feedback)) {
        $insertQuery = "INSERT INTO feedback (user_id, message) VALUES ('$user_id', '$feedback')";

        if (mysqli_query($conn, $insertQuery)) {
            $success_message = "<p class='message success'>Feedback submitted successfully!</p>";
        } else {
            $error_message = "<p class='message error'>Error: " . mysqli_error($conn) . "</p>";
        }
    } else {
        $error_message = "<p class='message error'>Please enter valid feedback.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Feedback</title>
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
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        
        .navbar-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar a {
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .navbar a:hover {
            background-color: var(--light);
        }
        
        .navbar .active {
            color: var(--primary);
            font-weight: 600;
        }
        
        h2 {
            color: var(--dark);
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        textarea {
            width: 100%;
            padding: 1rem;
            border: 1px solid var(--gray);
            border-radius: 4px;
            margin-bottom: 1rem;
            font-size: 1rem;
            min-height: 150px;
            resize: vertical;
        }
        
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(52, 144, 220, 0.2);
        }
        
        button {
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
        
        button:hover {
            background-color: var(--primary-dark);
        }
        
        .message {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <a href="user_dashboard.php" class="active">
                <i class="fas fa-home"></i> Home
            </a>
        </div>
    </nav>

    <div class="container">
        <h2>Submit Feedback by <br>
            <?php echo htmlspecialchars($user_data['username']); ?></h2>
        
        <?php 
        if (isset($success_message)) echo $success_message;
        if (isset($error_message)) echo $error_message;
        ?>

        <form method="POST">
            <textarea name="feedback" placeholder="Write your feedback here..." required></textarea>
            <button type="submit">
                <i class="fas fa-paper-plane"></i> Submit Feedback
            </button>
        </form>
    </div>

    <!-- Add these styles to your existing CSS -->
    <style>
        .user-info {
            background-color: var(--light);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 1px solid var(--gray);
        }
    
        .info-group {
            display: flex;
            margin-bottom: 0.5rem;
        }
    
        .info-group:last-child {
            margin-bottom: 0;
        }
    
        .info-group label {
            font-weight: 600;
            width: 100px;
            color: var(--dark);
        }
    
        .info-group span {
            color: var(--primary);
            font-weight: 500;
        }
    </style>
</body>
</html>
