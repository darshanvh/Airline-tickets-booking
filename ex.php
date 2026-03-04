<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Preview</title>
    <script src="https://cdn.tailwindcss.com"></script> <!-- Include Tailwind CSS -->
    <style>
        /* Optional custom styles for the page layout */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .main-content {
           
            width: 100%;
        }

        /* Custom header style */
        .header {
            background: linear-gradient(to right, #0b4d75, rgb(19, 132, 174));
            color: white;
            padding: 9.5px 0; /* Increased padding for more emphasis */
            text-align: center;
            font-size: 28px; /* Adjusted header size to match Admin Panel */
            font-weight: bold;
            margin-top: 0; /* Removed margin top to align with the top of the page */
            width: 100%;
        }
        
    </style>
</head>

<body class="bg-gray-100">

    <!-- Sidebar -->
    <div class="flex flex-col items-center w-[25%] h-screen overflow-hidden text-indigo-300 bg-indigo-900">
        <!-- Admin Panel Header with Vertical Line -->
        <a class="flex items-center w-full px-5 mt-5 border-b border-indigo-500 pb-3" href="#">
            <span class="ml-2 text-lg font-bold">Admin Panel</span>
        </a>

        <div class="w-full px-2">
            <div class="flex flex-col items-center w-full mt-3 border-t border-gray-700">
                <!-- Navigation Links -->
                <a class="flex items-center w-full h-16 px-3 mt-2 rounded hover:bg-indigo-700" href="admin_dashboard.php">
                    <span class="ml-2 text-lg font-medium">Dashboard</span>
                </a>
                <a class="flex items-center w-full h-16 px-3 mt-2 rounded hover:bg-indigo-700" href="add_flight.php">
                    <span class="ml-2 text-lg font-medium">Add Flight</span>
                </a>
                <a class="flex items-center w-full h-16 px-3 mt-2 rounded hover:bg-indigo-700" href="manage_flight.php">
                    <span class="ml-2 text-lg font-medium">Manage Flights</span>
                </a>
                <a class="flex items-center w-full h-16 px-3 mt-2 rounded hover:bg-indigo-700" href="add_cargo.php">
                    <span class="ml-2 text-lg font-medium">Add Cargo Flight</span>
                </a>
                <a class="flex items-center w-full h-16 px-3 mt-2 rounded hover:bg-indigo-700" href="manage_cargo.php">
                    <span class="ml-2 text-lg font-medium">Manage Cargo Flights</span>
                </a>
                <a class="flex items-center w-full h-16 px-3 mt-2 rounded hover:bg-indigo-700" href="admin_account.php">
                    <span class="ml-2 text-lg font-medium">My Admin Account</span>
                </a>
                <a class="flex items-center w-full h-16 px-3 mt-2 rounded hover:bg-indigo-700" href="feedback.php">
                    <span class="ml-2 text-lg font-medium">Feedback History</span>
                </a>
                <a class="flex items-center w-full h-16 px-3 mt-2 rounded hover:bg-indigo-700" href="logout.php">
                    <span class="ml-2 text-lg font-medium">Logout</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            Welcome to the Admin Panel
        </div>
    </div>

</body>

</html>
