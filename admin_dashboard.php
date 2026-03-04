<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch statistics
$total_flights = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM flights"))['total'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings"))['total'];
$current_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE status = 'Booked'"))['total'];
$cancelled_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE status = 'Cancelled'"))['total'];

$total_cargo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM cargo_flights"))['total'];
$total_cargo_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM cargo_bookings"))['total'];
$current_cargo_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM cargo_bookings WHERE status = 'Confirmed'"))['total'];
$cancelled_cargo_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM cargo_bookings WHERE status = 'Cancelled'"))['total'];

$passenger_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) AS total FROM bookings WHERE status = 'Booked'"))['total'] ?? 0;
$cargo_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) AS total FROM cargo_bookings WHERE status = 'Confirmed'"))['total'] ?? 0;
$total_revenue = $passenger_revenue + $cargo_revenue;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Reset and global styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
        }

        .main-content {
            width: 100%;
        }

        /* Header with #0b4d75 */
        .header {
            background: #0b4d75;
            color: white;
            padding: 10px 0;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            width: 100%;
        }

        /* Sidebar with #0b4d75 */
        .sidebar {
            background: #0b4d75;
            width: 20%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }

        /* Sidebar link styles */
        .sidebar-link {
            width: 100%;
            padding: 12px 25px; /* Increased padding from 15px to 25px */
            margin-top: 8px;
            font-size: 16px;
            font-weight: 500;
            color: white;
            text-align: left;
            display: flex;
            align-items: center;
            transition: background 0.3s ease-in-out;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Hover Effect */
        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.2);
            cursor: pointer;
        }

        /* Last link without border */
        .sidebar-link:last-child {
            border-bottom: none;
        }

        /* Admin Panel Title */
        .sidebar-header {
            font-size: 22px;
            font-weight: bold;
            color: white;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Main content padding */
        .content-wrapper {
            padding: 20px;
            width: 80%;
        }

        /* Dashboard card styles */
        .dashboard-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Admin Panel Title -->
        <div class="sidebar-header">Admin Panel</div>

        <div class="w-full px-4">
            <a class="sidebar-link" href="admin_dashboard.php">🏠 Dashboard</a>
            <a class="sidebar-link" href="add_flight.php">✈️ Add Flight</a>
            <a class="sidebar-link" href="manage_flight.php">📝 Manage Flights</a>
            <a class="sidebar-link" href="add_cargo.php">📦 Add Cargo Flight</a>
            <a class="sidebar-link" href="manage_cargo.php">🚚 Manage Cargo Flights</a>
            <a class="sidebar-link" href="admin_account.php">👤 My Admin Account</a>
            <a class="sidebar-link" href="admin_passenger_bookings.php">👥 My Passengers</a>
            <a class="sidebar-link" href="admin_cargo_bookings.php">📦 My Cargo</a>
            <a class="sidebar-link" href="feedback.php">💬 Feedback History</a>
            <a class="sidebar-link" href="my_user.php">👥 my user</a>
            <a class="sidebar-link" href="logout.php">🚪 Logout</a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            Welcome to the Admin Panel
        </div>

        <!-- Content Box for Admin Dashboard -->
        <style>
            .grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 1rem;
                padding: 1rem;
            }

            .dashboard-card {
                background: linear-gradient(to right, #0b4d75, rgb(19, 132, 174));
                color: white;
                padding: 1rem;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s;
            }

            .dashboard-card h2 {
                font-size: 0.9rem;
                margin-bottom: 0.5rem;
                color: #f0f0f0;
            }

            .dashboard-card p {
                font-size: 1.5rem;
                font-weight: bold;
                color: #ffffff;
            }
        </style>

        <div class="content-wrapper">
            <h1 class="text-2xl font-bold text-gray-700 mb-6">Admin Dashboard</h1>
            <div class="grid">
                <div class="dashboard-card">
                    <h2>Total Flights</h2>
                    <p><?php echo $total_flights; ?></p>
                </div>
                <div class="dashboard-card">
                    <h2>Total Passenger Bookings</h2>
                    <p><?php echo $total_bookings; ?></p>
                </div>
                <div class="dashboard-card">
                    <h2>Confirmed Bookings</h2>
                    <p><?php echo $current_bookings; ?></p>
                </div>
                <div class="dashboard-card">
                    <h2>Cancelled Bookings</h2>
                    <p><?php echo $cancelled_bookings; ?></p>
                </div>
                <div class="dashboard-card">
                    <h2>Total Cargo Flights</h2>
                    <p><?php echo $total_cargo; ?></p>
                </div>
                <div class="dashboard-card">
                    <h2>Total Cargo Bookings</h2>
                    <p><?php echo $total_cargo_bookings; ?></p>
                </div>
                <div class="dashboard-card">
                    <h2>Confirmed Cargo Bookings</h2>
                    <p><?php echo $current_cargo_bookings; ?></p>
                </div>
                <div class="dashboard-card">
                    <h2>Cancelled Cargo Bookings</h2>
                    <p><?php echo $cancelled_cargo_bookings; ?></p>
                </div>
                <div class="dashboard-card">
                    <h2>Passenger Revenue</h2>
                    <p><?php echo number_format($passenger_revenue); ?></p>
                </div>
                <div class="dashboard-card">
                    <h2>Cargo Revenue</h2>
                    <p><?php echo number_format($cargo_revenue); ?></p>
                </div>
                <div class="dashboard-card">
                    <h2>Total Revenue</h2>
                    <p><?php echo number_format($total_revenue); ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>