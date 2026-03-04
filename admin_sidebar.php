<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vertical Navbar Layout</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

    

        header {
            background: linear-gradient(to right, #0b4d75, rgb(19, 132, 174));
            color: white;
            padding: 1rem;
            text-align: center;
        }

        .container {
            display: flex;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(to right, #0b4d75, rgb(19, 132, 174));
            padding: 1rem 0.5rem;
            min-height: 100vh;
            color: white;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            width: 100%;
            height: 3.5rem;
            padding: 0 1rem;
            margin-top: 0.5rem;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            font-size: 1rem;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #4338ca; /* Similar to Indigo-700 */
        }

        .sidebar a span {
            margin-left: 0.5rem;
            font-size: 1.1rem;
        }

        .content {
            padding: 2rem;
            flex-grow: 1;
        }
        .box-container {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem;
        }

        .small-box {
            flex: 1;
            background: linear-gradient(to right, #0b4d75, rgb(19, 132, 174));
            padding: 1rem;
            border-radius: 8px;
            color: white;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .small-box h3 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .small-box p {
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
</head>

<div class="container">
    <nav class="sidebar">
        <a href="admin_dashboard.php">
            <span>Dashboard</span>
        </a>
        <a href="add_flight.php">
            <span>Add Flight</span>
        </a>
        <a href="manage_flight.php">
            <span>Manage Flights</span>
        </a>
        <a href="admin_passenger_bookings.php">
            <span> passanger list</span>
        </a>
        <a href="add_cargo.php">
            <span>Add Cargo Flight</span>
        </a>
        <a href="manage_cargo.php">
            <span>Manage Cargo Flights</span>
        </a>
        <a href="admin_cargo_bookings.php">
            <span> cargo passanger list</span>
        </a>
        <a href="admin_account.php">
            <span>My Admin Account</span>
        </a>
        <a href="feedback.php">
            <span>Feedback History</span>
        </a>
        <a href="my_user.php">
            <span>My User</span>
        </a>
        <a href="a_logout.php">
            <span>Logout</span>
        </a>
    </nav>

    <div class="content">
        <div class="box-container">
            <div class="small-box">
                <h3>Total Flights</h3>
                <p>150</p>
            </div>
            <div class="small-box">
                <h3>Total Bookings</h3>
                <p>275</p>
            </div>
            <div class="small-box">
                <h3>Revenue</h3>
                <p>₹50,000</p>
            </div>
        </div>
    </div>
</div>

</html>
