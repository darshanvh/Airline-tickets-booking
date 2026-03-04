<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #2c3e50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #2c3e50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        button:disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
        }
        button:hover:not(:disabled) {
            background-color: #34495e;
        }
    </style>
</head>
<body>
    <h2>Sample Passenger Table</h2>
    <table id="passengerTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Age</th>
                <th>Seat</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <tr>
                <td>John Doe</td>
                <td>9876543210</td>
                <td>30</td>
                <td>A1</td>
                <td>Confirmed</td>
            </tr>
            <tr>
                <td>Jane Smith</td>
                <td>9123456789</td>
                <td>27</td>
                <td>B4</td>
                <td>Pending</td>
            </tr>
            <tr>
                <td>Mike Johnson</td>
                <td>9012345678</td>
                <td>45</td>
                <td>C2</td>
                <td>Cancelled</td>
            </tr>
            <tr>
                <td>Emily Davis</td>
                <td>9234567890</td>
                <td>22</td>
                <td>D5</td>
                <td>Confirmed</td>
            </tr>
            <tr>
                <td>Robert Brown</td>
                <td>9345678901</td>
                <td>35</td>
                <td>E3</td>
                <td>Pending</td>
            </tr>
            <tr>
                <td>Sarah Wilson</td>
                <td>9456789012</td>
                <td>29</td>
                <td>F1</td>
                <td>Confirmed</td>
            </tr>
            <tr>
                <td>David Clark</td>
                <td>9567890123</td>
                <td>40</td>
                <td>G4</td>
                <td>Cancelled</td>
            </tr>
            <tr>
                <td>Laura Adams</td>
                <td>9678901234</td>
                <td>31</td>
                <td>H2</td>
                <td>Pending</td>
            </tr>
        </tbody>
    </table>
    <div class="pagination">
        <button id="prevBtn" disabled>Previous</button>
        <button id="nextBtn">Next</button>
    </div>
    <script>
        const rowsPerPage = 5;
        let currentPage = 0;
        const tableBody = document.getElementById('tableBody');
        const rows = tableBody.getElementsByTagName('tr');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        function showPage(page) {
            for (let i = 0; i < rows.length; i++) {
                rows[i].style.display = 'none';
            }
            const start = page * rowsPerPage;
            const end = Math.min(start + rowsPerPage, rows.length);
            for (let i = start; i < end; i++) {
                rows[i].style.display = '';
            }
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
        showPage(currentPage);
    </script>
</body>
</html>



____________
<?php
session_start();
include 'db.php';
include 'adminnav.php'; 

// Ensure admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Pagination settings
$records_per_page = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total number of records
$total_query = "SELECT COUNT(*) as total FROM flights";
$total_result = mysqli_query($conn, $total_query);
$total_records = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch flights for the current page
$sql = "SELECT * FROM flights ORDER BY date ASC LIMIT $records_per_page OFFSET $offset";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Flights</title>
    <style>
        * { box-sizing: border-box; }

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
            margin: 60px auto;
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

        .btn {
            padding: 6px 12px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            color: white;
            border-radius: 4px;
            font-size: 14px;
            margin: 2px;
            display: inline-block;
        }

        .cancel-btn { background-color: #dc3545; }
        .cancel-btn:hover { background-color: #c82333; }
        .edit-btn { background-color: #fd7e14; }
        .edit-btn:hover { background-color: #e06b12; }
        .passenger-btn { background-color: #007bff; }
        .passenger-btn:hover { background-color: #0056b3; }
        .disabled {
            background-color: #95a5a6 !important;
            cursor: not-allowed;
            pointer-events: none;
        }

        td[style*="color: red"] {
            font-weight: bold;
        }

        .no-data {
            color: red;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        

        <?php if (mysqli_num_rows($result) > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Flight ID</th>
                        <th>Flight Name</th>
                        <th>Source</th>
                        <th>Destination</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['flight_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['source']); ?></td>
                            <td><?php echo htmlspecialchars($row['destination']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td style="color: <?php echo ($row['status'] == 'Cancelled') ? 'red' : 'green'; ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'Cancelled') { ?>
                                    <button class="btn disabled">Cancel</button>
                                    <button class="btn disabled">Edit</button>
                                <?php } else { ?>
                                    <a href="cancel_flight.php?id=<?php echo urlencode($row['id']); ?>&page=<?php echo $page; ?>" class="btn cancel-btn">Cancel</a>
                                    <a href="edit_flight.php?id=<?php echo urlencode($row['id']); ?>" class="btn edit-btn">Edit</a>
                                <?php } ?>
                                <a href="passenger_list.php?flight_id=<?php echo urlencode($row['id']); ?>" class="btn passenger-btn">Passengers</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="pagination">
                <button <?php echo $page <= 1 ? 'disabled' : ''; ?> onclick="window.location.href='manage_flights.php?page=<?php echo $page - 1; ?>'">Previous</button>
                <button <?php echo $page >= $total_pages ? 'disabled' : ''; ?> onclick="window.location.href='manage_flights.php?page=<?php echo $page + 1; ?>'">Next</button>
            </div>
        <?php } else { ?>
            <p class="no-data">No flights available.</p>
        <?php } ?>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?>