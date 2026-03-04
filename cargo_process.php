<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('You need to log in first!'); window.location.href='login.php';</script>";
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $flight_id = $_POST['flight_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $aadhar_number = $_POST['aadhar_number'];
    $weight = $_POST['weight'];
    $total_price = $_POST['total_price'];

    // Generate a Unique Booking ID
    $booking_id = "CB" . time() . rand(100, 999); 

    // Insert Booking into Database
    $query = "INSERT INTO cargo_bookings (booking_id, user_id, flight_id, user_name, phone, email, user_address, weight, total_price, status, aadhar_number) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Confirmed', ?)";

    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sisssssdss", $booking_id, $user_id, $flight_id, $name, $phone, $email, $address, $weight, $total_price, $aadhar_number);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            echo "
            <html>
            <head>
                <title>Processing Booking</title>
                <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
                <style>
                    body {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        background-color: #f8f9fa;
                        flex-direction: column;
                        text-align: center;
                    }
                    .spinner {
                        width: 100px;
                        height: 100px;
                        border: 8px solid rgba(0, 0, 0, 0.1);
                        border-top: 8px solid green;
                        border-radius: 50%;
                        animation: spin 1s linear infinite;
                    }
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                    .message {
                        display: none;
                        font-size: 24px;
                        font-weight: bold;
                        color: green;
                        margin-top: 20px;
                    }
                </style>
            </head>
            <body>
                <div class='spinner' id='spinner'></div>
                <div class='message' id='message'>✅ Booking Successfully Completed! <br> Your Booking ID: <b>$booking_id</b></div>

                <script>
                    setTimeout(function() {
                        document.getElementById('spinner').style.display = 'none';
                        document.getElementById('message').style.display = 'block';
                        
                        setTimeout(function() {
                            window.location.href = 'user_dashboard.php';
                        }, 3000); // Redirect after 3 seconds
                    }, 5000);
                </script>
            </body>
            </html>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
