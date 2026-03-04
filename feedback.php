<?php
include("adminnav.php");
include("db.php");

// How many records to show per page
$limit = 5;

// Get current page number from URL, default to 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset
$offset = ($page - 1) * $limit;

// Count total feedback records
$count_query = "SELECT COUNT(*) AS total FROM feedback";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $limit);

// Get current page feedback
$query = "SELECT f.id, u.username, u.phone, f.message 
          FROM feedback f 
          INNER JOIN users u ON f.user_id = u.id 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Feedback</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        
            background-color: #f4f4f4;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 80%;
            margin: 60px auto;
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

        .back-link {
            display: block;
            width: fit-content;
            margin: 20px auto;
            text-align: center;
            text-decoration: none;
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
        }

        .back-link:hover {
            background-color: #34495e;
        }
    </style>
</head>
<body>


    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Phone</th>
                <th>Feedback</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                    </tr>
                <?php }
            } else {
                echo "<tr><td colspan='4'>No feedback available.</td></tr>";
            } ?>
        </tbody>
    </table>

    <div class="pagination">
        <button id="prevBtn" <?php echo $page <= 1 ? 'disabled' : ''; ?> onclick="window.location.href='?page=<?php echo $page - 1; ?>'">Previous</button>
        <button id="nextBtn" <?php echo $page >= $total_pages ? 'disabled' : ''; ?> onclick="window.location.href='?page=<?php echo $page + 1; ?>'">Next</button>
    </div>

</body>
</html>

<?php
mysqli_close($conn);
?>