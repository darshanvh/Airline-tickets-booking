<?php
session_start();

include 'db.php';

if (!isset($_GET['flight_id'])) {
    echo "<script>alert('Invalid Flight ID'); window.location.href='manage_flight.php';</script>";
    exit;
}

$flight_id = $_GET['flight_id'];

// Fetch flight details
$flight_query = "SELECT flight_name FROM flights WHERE id = ?";
$stmt = mysqli_prepare($conn, $flight_query);
mysqli_stmt_bind_param($stmt, "i", $flight_id);
mysqli_stmt_execute($stmt);
$flight_result = mysqli_stmt_get_result($stmt);
$flight_row = mysqli_fetch_assoc($flight_result);

if (!$flight_row) {
    echo "<script>alert('Flight not found'); window.location.href='manage_flight.php';</script>";
    exit;
}

$flight_name = $flight_row['flight_name'];

// Fetch passengers for the selected flight
$sql = "SELECT u.username, u.phone, b.id AS booking_id, b.seat_count, b.seat_numbers, b.status, b.aadhar_number, b.age, b.gender
        FROM users u
        JOIN bookings b ON u.id = b.user_id
        WHERE b.flight_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $flight_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            width: 90%;
            margin: auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 15px;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        td {
            background-color: #fdfdfd;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .pagination button {
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #2c3e50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .pagination button:disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
        }

        .pagination button:hover:not(:disabled) {
            background-color: #34495e;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .no-data {
            color: red;
            text-align: center;
            margin: 20px 0;
        }

        .back-button {
            display: inline-block;
            margin: 20px auto;
            padding: 10px 15px;
            background-color: #2c3e50;
            color: white;
            border-radius: 4px;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #34495e;
        }
    </style>
</head>
<body>
<?php include 'adminnav.php'; ?> 
    <div class="container">
        <h2>Passenger List for Flight: <?php echo htmlspecialchars($flight_name); ?></h2>

        <?php if (mysqli_num_rows($result) > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Aadhar Number</th>
                        <th>Age</th>
                        <th>Total Seats</th>
                        <th>Seat Numbers</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['aadhar_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                            <td><?php echo htmlspecialchars($row['seat_count']); ?></td>
                            <td><?php echo htmlspecialchars($row['seat_numbers']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><a href="details.php?booking_id=<?php echo urlencode($row['booking_id']); ?>">View Details</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="no-data">No passengers found for this flight.</p>
        <?php } ?>

        <div class="pagination">
            <button id="prevBtn" disabled>Previous</button>
            <button id="nextBtn" <?php echo mysqli_num_rows($result) <= 5 ? 'disabled' : ''; ?>>Next</button>
        </div>
    </div>

    <script>
        const rowsPerPage = 5;
        let currentPage = 0;
        const tableBody = document.querySelector('table tbody');
        const rows = tableBody.getElementsByTagName('tr');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        function showPage(page) {
            // Hide all rows
            for (let i = 0; i < rows.length; i++) {
                rows[i].style.display = 'none';
            }

            // Show rows for the current page
            const start = page * rowsPerPage;
            const end = Math.min(start + rowsPerPage, rows.length);
            for (let i = start; i < end; i++) {
                rows[i].style.display = '';
            }

            // Update button states
            prevBtn.disabled = page === 0;
            nextBtn.disabled = end >= rows.length;
        }

        prevBtn.addEventListener('click', () => {
            if (currentPage > 0) {
                currentPage--;
                showPage(currentPage);
            }
        });

        nextBtn.addEventListener('click', () => {
            if ((currentPage + 1) * rowsPerPage < rows.length) {
                currentPage++;
                showPage(currentPage);
            }
        });

        // Initial page load
        showPage(currentPage);
    </script>
</body>
</html>

<?php mysqli_close($conn); ?>