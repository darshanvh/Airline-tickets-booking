<?php


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!-- Top Navigation Bar Styles -->
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
        font-family: 'Segoe UI', 'Roboto', sans-serif;
    }

    body {
        padding-top: 70px;
        background-color: #f0f5fa;
    }

    .topnav {
        background-color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
        box-shadow: var(--card-shadow);
    }

    .logo-container {
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: transform 0.3s ease;
    }

    .logo-container:hover {
        transform: scale(1.02);
    }

    .logo-container img {
        width: 40px;
        height: 40px;
        margin-right: 12px;
        border-radius: 8px;
        box-shadow: var(--card-shadow);
    }

    .logo-container span {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
    }

    .menu-toggle {
        font-size: 1.5rem;
        color: var(--dark);
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .menu-toggle:hover {
        background-color: var(--light);
        color: var(--primary);
    }

    .dropdown-menu {
        position: fixed;
        top: 70px;
        right: 2rem;
        width: 280px;
        background: white;
        border-radius: 8px;
        box-shadow: var(--card-shadow);
        display: none;
        z-index: 1000;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .dropdown-menu a {
        display: flex;
        align-items: center;
        color: var(--dark);
        text-decoration: none;
        padding: 1rem 1.5rem;
        font-size: 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .dropdown-menu a:hover {
        background-color: var(--light);
        color: var(--primary);
        padding-left: 1.75rem;
    }

    .dropdown-menu a.logout {
        color: var(--danger);
    }

    .dropdown-menu a.logout:hover {
        background-color: var(--danger);
        color: white;
    }

    @media (max-width: 768px) {
        .topnav {
            padding: 1rem;
        }

        .logo-container span {
            font-size: 1.25rem;
        }

        .dropdown-menu {
            width: calc(100% - 2rem);
            right: 1rem;
        }
    }
</style>

<!-- Top Navigation Bar -->
<div class="topnav">
    <a href="user_dashboard.php" class="logo-container">
        <img src="1.jpg" alt="Airline Logo">
        <span>SkyNest Airlines</span>
    </a>
    <span class="menu-toggle" onclick="toggleMenu()">☰</span>
</div>

<!-- Dropdown Menu -->
<div class="dropdown-menu" id="dropdownMenu">
    <div class="dropdown-header">
        <img src="<?php echo !empty($user['profile_pic']) ? 'uploads/' . $user['profile_pic'] : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png'; ?>" 
             alt="Profile">
        <h4><?php echo htmlspecialchars($user['username']); ?></h4>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
    </div>
    
    <div class="dropdown-section">
        <span class="section-title">Account</span>
        <a href="edit_profile.php"><i class="ri-user-line"></i>Profile</a>
       
    </div>
    
    <div class="dropdown-section">
        <span class="section-title">Services</span>
        <a href="available_flights.php"><i class="ri-flight-takeoff-line"></i>Available Flights</a>
        <a href="search_flight.php"><i class="ri-search-line"></i>Book Flight</a>
        <a href="cancel_booking.php"><i class="ri-close-circle-line"></i>Cancel Flight</a>
        <a href="my_account.php"><i class="ri-dashboard-line"></i>Dashboard</a>
    </div>
    
    <div class="dropdown-section">
        <span class="section-title">Cargo</span>
        <a href="avc.php"><i class="ri-truck-line"></i>Available Cargo</a>
        <a href="cargo_booking.php"><i class="ri-search-line"></i>Book Cargo</a>
        <a href="cargo_cancel.php"><i class="ri-delete-bin-line"></i>Cancel Cargo</a>
        <a href="my_cargo_account.php"><i class="ri-folder-line"></i>Cargo Account</a>
    </div>
    
    <div class="dropdown-section">
        <a href="user_feedback.php"><i class="ri-message-2-line"></i>Feedback</a>
        <a href="logout.php" class="logout"><i class="ri-logout-box-r-line"></i>Logout</a>
    </div>
</div>

<style>
    .dropdown-menu {
        max-height: 85vh;
        overflow-y: auto;
        padding: 4px 0;
    }

    .dropdown-section {
        padding: 4px 0;
        border-bottom: 1px solid #eee;
    }

    .section-title {
        padding: 4px 16px;
        font-size: 11px;
    }

    .dropdown-menu a {
        padding: 6px 16px;
        font-size: 0.9rem;
        gap: 8px;
    }

    .dropdown-header {
        padding: 12px;
    }

    .dropdown-header h4 {
        margin-bottom: 2px;
    }

    .dropdown-header {
        padding: 16px;
        text-align: center;
        border-bottom: 1px solid #eee;
    }

    .dropdown-header img {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        margin-bottom: 8px;
        border: 3px solid var(--primary);
        padding: 2px;
    }

    .dropdown-header h4 {
        color: var(--dark);
        margin-bottom: 4px;
    }

    .dropdown-header p {
        color: var(--gray);
        font-size: 14px;
    }

    .dropdown-section {
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }

    .dropdown-section:last-child {
        border-bottom: none;
    }

    .section-title {
        display: block;
        padding: 8px 16px;
        font-size: 12px;
        text-transform: uppercase;
        color: var(--gray);
        font-weight: 600;
    }

    .dropdown-menu a {
        padding: 10px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--dark);
        transition: all 0.3s ease;
    }

    .dropdown-menu a:hover {
        background: #f8f9fa;
        color: var(--primary);
    }

    .dropdown-menu a i {
        font-size: 18px;
        color: var(--gray);
    }

    .dropdown-menu a:hover i {
        color: var(--primary);
    }

    .dropdown-menu .logout {
        color: var(--danger);
    }

    .dropdown-menu .logout:hover {
        background: #fff5f5;
        color: var(--danger);
    }

    .dropdown-menu .logout i {
        color: var(--danger);
    }
</style>

<script>
    function toggleMenu() {
        const dropdownMenu = document.getElementById("dropdownMenu");
        dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
    }

    window.onclick = function(event) {
        const dropdownMenu = document.getElementById("dropdownMenu");
        if (event.target !== dropdownMenu && !event.target.matches('.menu-toggle')) {
            dropdownMenu.style.display = "none";
        }
    };
</script>