<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "air17");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login to access this page.'); window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$query = "SELECT * FROM users WHERE id='$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Check if user is at least 18 years old
if (!empty($user['dob'])) {
    $age = date_diff(date_create($user['dob']), date_create('today'))->y;
    if ($age < 18) {
        echo "<script>alert('You must be at least 18 years old to access this page.'); window.location.href='user_dashboard.php';</script>";
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    
    // Validate age (must be 18+)
    $age = date_diff(date_create($dob), date_create('today'))->y;
    if ($age < 18) {
        echo "<script>alert('You must be at least 18 years old to register.'); window.location='edit_profile.php';</script>";
        exit();
    }
    
    $favorite_place = $_POST['favorite_place'];
    $address = $_POST['address'];
    $about = $_POST['about'];

    // Handle profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = time() . '.' . $file_ext;
            $target = "uploads/" . $new_filename;

            if (!is_dir("uploads")) {
                mkdir("uploads", 0777, true);
            }

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
                // Delete old image if not default
                if (!empty($user['profile_pic']) && file_exists("uploads/" . $user['profile_pic']) && $user['profile_pic'] != 'default.jpg') {
                    unlink("uploads/" . $user['profile_pic']);
                }

                // Update profile picture in the database
                mysqli_query($conn, "UPDATE users SET profile_pic='$new_filename' WHERE id='$user_id'");
            } else {
                echo "<script>alert('File upload failed!');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type! Only JPG, PNG, GIF, WebP allowed.');</script>";
        }
    }

    // Update user details
    $update_query = "UPDATE users SET email='$email', gender='$gender', dob='$dob', favorite_place='$favorite_place', address='$address', about='$about' WHERE id='$user_id'";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Profile Updated Successfully!'); window.location='edit_profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile!');</script>";
    }

    // Refresh user data
    $query = "SELECT * FROM users WHERE id='$user_id'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Consistent styling with booking_details.php */
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
        }
        
        body {
            font-family: 'Segoe UI', 'Roboto', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: #f0f5fa;
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
        
        .nav-links {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
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
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-secondary {
            background-color: var(--secondary);
        }
        
        .btn-secondary:hover {
            background-color: #e67e22;
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
        
        .profile-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .profile-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            padding: 0.75rem;
            border: 1px solid var(--gray);
            border-radius: 4px;
            font-size: 1rem;
            width: 100%;
        }
        
        .form-control:disabled {
            background-color: #f5f5f5;
            color: #666;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .profile-image-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
            cursor: pointer;
            box-shadow: var(--card-shadow);
            margin-bottom: 1rem;
        }
        
        .hidden-input {
            display: none;
        }
        
        .action-bar {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .full-width {
            grid-column: 1 / -1;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .profile-card {
                padding: 1rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .action-bar {
                flex-direction: column;
            }
            
            .action-bar .btn {
                width: 100%;
            }
        }
    </style>
    <script>
        function triggerFileUpload() { 
            document.getElementById('profileInput').click(); 
        }
        
        function previewProfilePic(event) {
            var reader = new FileReader();
            reader.onload = function() { 
                document.getElementById('profileImage').src = reader.result; 
            };
            reader.readAsDataURL(event.target.files[0]);
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.profile-section');
            sections.forEach((section, index) => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';
                section.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                
                setTimeout(() => {
                    section.style.opacity = '1';
                    section.style.transform = 'translateY(0)';
                }, 100 + (index * 200));
            });
        });
    </script>
</head>
<body>
    <nav class="navbar">
        <div class="container navbar-content">
            <div class="logo">AirBooking</div>
            <div class="nav-links">
                <a href="user_dashboard.php" class="btn">
                    <i class="fas fa-home"></i> Home
                </a>
            </div>
        </div>
    </nav>

    <header class="page-header">
        <div class="container">
            <h1 class="page-title">Edit Profile</h1>
        </div>
    </header>

    <main class="container">
        <div class="profile-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="profile-section">
                    <div class="profile-image-container">
                        <img src="<?php echo !empty($user['profile_pic']) ? 'uploads/'.$user['profile_pic'] : 'uploads/default.jpg'; ?>" 
                             id="profileImage" class="profile-image" onclick="triggerFileUpload()">
                        <input type="file" name="profile_pic" id="profileInput" class="hidden-input" onchange="previewProfilePic(event)">
                        <span class="form-label">Click on the image to change your profile picture</span>
                        <button type="button" class="btn btn-danger delete-photo" onclick="deleteProfilePic()">
                            <i class="fas fa-trash"></i> Delete Photo
                        </button>
                    </div>
                </div>

                <div class="profile-section">
                    <h2 class="section-title">Personal Information</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-control">
                                <option value="Male" <?php if ($user['gender'] == 'Male') echo "selected"; ?>>Male</option>
                                <option value="Female" <?php if ($user['gender'] == 'Female') echo "selected"; ?>>Female</option>
                                <option value="Other" <?php if ($user['gender'] == 'Other') echo "selected"; ?>>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" id="dob" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['dob']); ?>" 
                                   max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>" 
                                   required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Favorite Place</label>
                            <input type="text" name="favorite_place" class="form-control" value="<?php echo htmlspecialchars($user['favorite_place']); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="profile-section">
                    <h2 class="section-title">Additional Information</h2>
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control"><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">About Me</label>
                            <textarea name="about" class="form-control"><?php echo htmlspecialchars($user['about']); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="action-bar">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="change_password.php" class="btn btn-secondary">
                        <i class="fas fa-key"></i> Change Password
                    </a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('.profile-section');
        sections.forEach((section, index) => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            
            setTimeout(() => {
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, 100 + (index * 200));
        });
    });
</script>

<style>
    .btn-danger {
        background-color: var(--danger);
    }
    
    .btn-danger:hover {
        background-color: #cc1f1a;
    }
    
    .delete-photo {
        margin-top: 10px;
        font-size: 0.9rem;
        padding: 0.4rem 0.8rem;
    }
</style>

<script>
    function deleteProfilePic() {
        if (confirm('Are you sure you want to delete your profile picture?')) {
            fetch('delete_profile_pic.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('profileImage').src = 'uploads/default.jpg';
                } else {
                    alert('Error deleting profile picture');
                }
            });
        }
    }
</script>