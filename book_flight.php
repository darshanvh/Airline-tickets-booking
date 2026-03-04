<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$flight_id = $_GET['flight_id'];

// Fetch flight details
$stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
$stmt->bind_param("i", $flight_id);
$stmt->execute();
$flight = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$flight) {
    die("Flight not found.");
}

// Fetch booked seats
$stmt = $conn->prepare("SELECT seat_numbers FROM bookings WHERE flight_id = ? AND status IN ('Booked', 'Paid')");
$stmt->bind_param("i", $flight_id);
$stmt->execute();
$result = $stmt->get_result();
$booked_seats = [];

while ($row = $result->fetch_assoc()) {
    $booked_seats = array_merge($booked_seats, explode(',', $row['seat_numbers']));
}

$stmt->close();
$booked_seats = array_map('intval', $booked_seats);

$business_seats = (int) $flight['business_seats'];
$economy_seats = (int) $flight['economy_seats'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Selection - <?= htmlspecialchars($flight['flight_name']); ?></title>
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
            --warning: #f6ad55;
            --info: #60a5fa;
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
            min-height: 100vh;
        }
        
        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
            position: relative;
        }
        
        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .nav-links a {
            color: var(--dark);
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: var(--primary);
        }
        
        .home-button {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        
        .home-button i {
            margin-right: 8px;
        }
        
        .home-button:hover {
            background-color: var(--primary-dark);
        }
        
        .page-header {
            background-color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }
        
        .page-subtitle {
            color: #718096;
            margin-top: 0.5rem;
        }
        
        .card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }
        
        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .flight-info {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .flight-info-item {
            flex: 1 1 250px;
            margin-right: 2rem;
            margin-bottom: 1rem;
        }
        
        .flight-info-label {
            font-size: 0.875rem;
            color: #718096;
            margin-bottom: 0.25rem;
        }
        
        .flight-info-value {
            font-weight: 600;
            color: var(--dark);
        }
        
        .seat-type-selector {
            margin-bottom: 2rem;
        }
        
        .seat-type-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .seat-type-select {
            width: 100%;
            max-width: 300px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            line-height: 1.5;
            color: var(--dark);
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #e2e8f0;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .seat-type-select:focus {
            border-color: var(--primary);
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
        }
        
        .seat-map {
            margin-bottom: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .seat-map-header {
            width: 90%;
            max-width: 800px;
            text-align: center;
            font-weight: bold;
            padding: 1rem;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            background-color: var(--primary);
            color: white;
            margin-bottom: 1rem;
        }
        
        .seat-map-container {
            background-color: #f8fafc;
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            overflow-x: auto;
        }
        
        .seat-chart {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .seat-row {
            display: flex;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .seat {
            width: 45px;
            height: 45px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
            user-select: none;
        }
        
        .seat-available {
            background-color: white;
            border: 2px solid var(--success);
            color: var(--success);
        }
        
        .seat-available:hover {
            background-color: rgba(56, 193, 114, 0.1);
            transform: translateY(-2px);
        }
        
        .seat-booked {
            background-color: rgba(227, 52, 47, 0.1);
            border: 2px solid var(--danger);
            color: var(--danger);
            cursor: not-allowed;
            opacity: 0.7;
        }
        
        .seat-selected {
            background-color: var(--success);
            border: 2px solid var(--success);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .seat-aisle {
            width: 20px;
        }
        
        .seat-legend {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin: 0 1rem;
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            margin-right: 8px;
            border-radius: 3px;
        }
        
        .legend-available {
            background-color: white;
            border: 2px solid var(--success);
        }
        
        .legend-selected {
            background-color: var(--success);
            border: 2px solid var(--success);
        }
        
        .legend-booked {
            background-color: rgba(227, 52, 47, 0.1);
            border: 2px solid var(--danger);
        }
        
        .seat-selection-summary {
            background-color: #f8fafc;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .selected-seats-list {
            display: flex;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }
        
        .selected-seat-badge {
            background-color: var(--success);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        
        .button {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.25rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            cursor: pointer;
        }
        
        .button-primary {
            color: #fff;
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .button-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .button-primary:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }
        
        .button-outline {
            color: var(--primary);
            background-color: transparent;
            border-color: var(--primary);
        }
        
        .button-outline:hover {
            color: #fff;
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        @media (max-width: 768px) {
            .flight-info-item {
                flex: 1 1 100%;
                margin-right: 0;
            }
            
            .seat {
                width: 40px;
                height: 40px;
                margin: 0 3px;
                font-size: 0.875rem;
            }
            
            .seat-map-container {
                padding: 1rem;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 1rem;
            }
            
            .button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container navbar-content">
            <div class="logo">AirBooking</div>
            <div class="nav-links">
                <a href="user_dashboard.php" class="home-button">
                    <i class="fas fa-home"></i> Home
                </a>
            </div>
        </div>
    </nav>

    <header class="page-header">
        <div class="container">
            <h1 class="page-title">Select Your Seats</h1>
            <p class="page-subtitle">Flight: <?= htmlspecialchars($flight['flight_name']); ?> (<?= htmlspecialchars($flight['flight_id']); ?>)</p>
        </div>
    </header>

    <main class="container">
        <div class="card">
            <div class="card-header">
                <h3>Flight Details</h3>
            </div>
            <div class="card-body">
                <div class="flight-info">
                    <div class="flight-info-item">
                        <div class="flight-info-label">From</div>
                        <div class="flight-info-value"><?= htmlspecialchars($flight['source']); ?></div>
                    </div>
                    <div class="flight-info-item">
                        <div class="flight-info-label">To</div>
                        <div class="flight-info-value"><?= htmlspecialchars($flight['destination']); ?></div>
                    </div>
                    <div class="flight-info-item">
                        <div class="flight-info-label">Date</div>
                        <div class="flight-info-value"><?= date('d M Y', strtotime($flight['date'])); ?></div>
                    </div>
                    <div class="flight-info-item">
                        <div class="flight-info-label">Departure</div>
                        <div class="flight-info-value"><?= date('H:i', strtotime($flight['departure_time'])); ?></div>
                    </div>
                    <div class="flight-info-item">
                        <div class="flight-info-label">Arrival</div>
                        <div class="flight-info-value"><?= date('H:i', strtotime($flight['arrival_time'])); ?></div>
                    </div>
                </div>

                <div class="seat-type-selector">
                    <label for="seatType" class="seat-type-label">Select Cabin Class:</label>
                    <select id="seatType" class="seat-type-select">
                        <option value="Business">Business Class</option>
                        <option value="Economy">Economy Class</option>
                    </select>
                </div>

                <div class="seat-map">
                    <div class="seat-map-header">
                        <i class="fas fa-plane"></i> FRONT OF AIRCRAFT
                    </div>
                    <div class="seat-map-container">
                        <div id="seatChart" class="seat-chart"></div>
                    </div>
                    <div class="seat-legend">
                        <div class="legend-item">
                            <div class="legend-color legend-available"></div>
                            <span>Available</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color legend-selected"></div>
                            <span>Selected</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color legend-booked"></div>
                            <span>Booked</span>
                        </div>
                    </div>
                </div>

                <div id="selectionSummary" class="seat-selection-summary" style="display: none;">
                    <h4>Your Selection</h4>
                    <div id="selectedSeatsDisplay" class="selected-seats-list"></div>
                    <div id="priceEstimate"></div>
                </div>

                <div class="action-buttons">
                    <a href="javascript:history.back()" class="button button-outline">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button id="nextBtn" class="button button-primary" onclick="goToConfirmation()" disabled>
                        Continue to Booking <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
        let bookedSeats = new Set(<?= json_encode($booked_seats); ?>);
        let selectedSeats = new Set();
        let economyPrice = <?= $flight['economy_price']; ?>;
        let businessPrice = <?= $flight['business_price']; ?>;

        document.addEventListener("DOMContentLoaded", function () {
            generateSeats();
            document.getElementById("seatType").addEventListener("change", generateSeats);
        });

        function generateSeats() {
            let seatChart = document.getElementById("seatChart");
            seatChart.innerHTML = "";
            selectedSeats.clear();
            updateSelectionSummary();

            let seatType = document.getElementById("seatType").value;
            let totalSeats = seatType === "Business" ? <?= $business_seats; ?> : <?= $economy_seats; ?>;
            let startSeat = seatType === "Business" ? 1 : <?= $business_seats; ?> + 1;
            let maxSeat = startSeat + totalSeats - 1;

            // Calculate rows and seats per row
            let seatsPerRow = 6;
            let rows = Math.ceil(totalSeats / seatsPerRow);

            for (let i = 0; i < rows; i++) {
                let row = document.createElement("div");
                row.classList.add("seat-row");

                for (let j = 0; j < seatsPerRow && startSeat <= maxSeat; j++) {
                    // Add aisle after the 3rd seat
                    if (j === 3) {
                        let aisle = document.createElement("div");
                        aisle.classList.add("seat-aisle");
                        row.appendChild(aisle);
                    }

                    let seatNumber = startSeat++;
                    let seat = createSeatElement(seatNumber);
                    row.appendChild(seat);
                }
                seatChart.appendChild(row);
            }
        }

        function createSeatElement(seatNumber) {
            let seat = document.createElement("div");
            seat.classList.add("seat");
            seat.innerText = seatNumber;

            if (bookedSeats.has(seatNumber)) {
                seat.classList.add("seat-booked");
            } else {
                seat.classList.add("seat-available");
                seat.addEventListener("click", () => toggleSeatSelection(seat, seatNumber));
            }
            return seat;
        }

        function toggleSeatSelection(seat, seatNumber) {
            if (bookedSeats.has(seatNumber)) return;

            if (selectedSeats.has(seatNumber)) {
                seat.classList.remove("seat-selected");
                seat.classList.add("seat-available");
                selectedSeats.delete(seatNumber);
            } else {
                seat.classList.remove("seat-available");
                seat.classList.add("seat-selected");
                selectedSeats.add(seatNumber);
            }
            updateSelectionSummary();
        }

        function updateSelectionSummary() {
            let summaryElement = document.getElementById("selectionSummary");
            let seatsDisplay = document.getElementById("selectedSeatsDisplay");
            let priceEstimate = document.getElementById("priceEstimate");
            
            if (selectedSeats.size === 0) {
                summaryElement.style.display = "none";
                document.getElementById("nextBtn").disabled = true;
                return;
            }
            
            // Show the summary section
            summaryElement.style.display = "block";
            document.getElementById("nextBtn").disabled = false;
            
            // Update selected seats display
            seatsDisplay.innerHTML = "";
            let sortedSeats = Array.from(selectedSeats).sort((a, b) => a - b);
            
            sortedSeats.forEach(seat => {
                let badge = document.createElement("span");
                badge.classList.add("selected-seat-badge");
                badge.innerText = seat;
                seatsDisplay.appendChild(badge);
            });
            
            // Calculate and display price estimate
            let seatType = document.getElementById("seatType").value;
            let pricePerSeat = seatType === "Business" ? businessPrice : economyPrice;
            let totalPrice = pricePerSeat * selectedSeats.size;
            
            priceEstimate.innerHTML = `
                <div style="margin-top: 1rem;">
                    <div><strong>${seatType} Class:</strong> ${selectedSeats.size} seat(s) × ₹${pricePerSeat.toLocaleString()}</div>
                    <div style="margin-top: 0.5rem; font-size: 1.25rem; font-weight: bold;">Total: ₹${totalPrice.toLocaleString()}</div>
                </div>
            `;
        }

        function goToConfirmation() {
            if (selectedSeats.size === 0) {
                alert("Please select at least one seat.");
                return;
            }
            let seatType = document.getElementById("seatType").value;
            let seatList = Array.from(selectedSeats).sort((a, b) => a - b).join(',');
            window.location.href = `confirm_booking.php?flight_id=<?= $flight_id; ?>&seat_type=${seatType}&seats=${seatList}`;
        }
    </script>
</body>
</html>