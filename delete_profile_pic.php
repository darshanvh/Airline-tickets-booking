<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "air17");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get current profile pic
$query = "SELECT profile_pic FROM users WHERE id='$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Delete file if it exists and is not default
if (!empty($user['profile_pic']) && $user['profile_pic'] != 'default.jpg' && file_exists("uploads/" . $user['profile_pic'])) {
    unlink("uploads/" . $user['profile_pic']);
}

// Update database to default image
$update = mysqli_query($conn, "UPDATE users SET profile_pic='default.jpg' WHERE id='$user_id'");

echo json_encode(['success' => true]);
?>