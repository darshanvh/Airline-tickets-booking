<?php 
include 'db.php';

if (isset($_GET['id'])) {
    $cargo_id = $_GET['id'];
    $query = "SELECT * FROM cargo_flights WHERE id = '$cargo_id'";
    $result = mysqli_query($conn, $query);
    $cargo = mysqli_fetch_assoc($result);
}

if (isset($_POST['update'])) {
    $flight_name = $_POST['flight_name'];
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $date = $_POST['date'];
    $price_per_kg = $_POST['price_per_kg'];
    $total_weight = $_POST['total_weight'];
    
    $update_query = "UPDATE cargo_flights SET
                    flight_name = '$flight_name',
                    source = '$source',
                    destination = '$destination',
                    date = '$date',
                    price_per_kg = '$price_per_kg',
                    total_weight = '$total_weight'
                    WHERE id = '$cargo_id'";
    
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Cargo Flight Updated Successfully!'); window.location='manage_cargo.php';</script>";
    } else {
        echo "<script>alert('Error Updating Cargo Flight!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Cargo Flight</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            min-height: 100vh;
        }

        /* Main content padding */
        .content-wrapper {
            padding: 20px;
            width: 100%;
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            margin-top: 20px; /* Add some top margin */
        }

        .form-container {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        label {
            margin-bottom: 5px;
            color: #555;
            font-size: 14px;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        input[type="submit"],
        button[type="submit"] {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            grid-column: span 2;
            width: 100%;
        }

        input[type="submit"]:hover,
        button[type="submit"]:hover {
            background-color: #1a252f;
        }
    </style>
</head>
<body>
    <?php include 'adminnav.php'; ?>
    
    <div class="content-wrapper">
        <div class="form-container">
            <h2>Edit Cargo Flight</h2>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="flight_name">Flight Name</label>
                        <input type="text" name="flight_name" id="flight_name" value="<?php echo $cargo['flight_name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="source">Source</label>
                        <input type="text" name="source" id="source" value="<?php echo $cargo['source']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="destination">Destination</label>
                        <input type="text" name="destination" id="destination" value="<?php echo $cargo['destination']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" name="date" id="date" value="<?php echo $cargo['date']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="price_per_kg">Price Per KG</label>
                        <input type="number" name="price_per_kg" id="price_per_kg" value="<?php echo $cargo['price_per_kg']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="total_weight">Total Weight (KG)</label>
                        <input type="number" name="total_weight" id="total_weight" value="<?php echo $cargo['total_weight']; ?>" required>
                    </div>
                    <div class="form-group full-width">
                        <button type="submit" name="update">Update Cargo Flight</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>