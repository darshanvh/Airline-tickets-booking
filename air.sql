CREATE DATABASE air17;
USE air17;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    phone VARCHAR(10) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Admin Table
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Flights Table
CREATE TABLE flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flight_name VARCHAR(100) NOT NULL,
    flight_id VARCHAR(20) UNIQUE NOT NULL,
    source VARCHAR(50) NOT NULL,
    destination VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    departure_time TIME NOT NULL,
    arrival_time TIME NOT NULL,
    economy_seats INT NOT NULL,
    business_seats INT NOT NULL,
    economy_price DECIMAL(10,2) NOT NULL,
    business_price DECIMAL(10,2) NOT NULL,
    status ENUM('Scheduled', 'Cancelled') DEFAULT 'Scheduled'
);

-- Flight Bookings Table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    flight_id INT,
    seat_numbers TEXT,
    total_amount DECIMAL(10,2),
    status ENUM('Confirmed', 'Cancelled') DEFAULT 'Confirmed',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (flight_id) REFERENCES flights(id)
);

-- Cargo Flights Table
CREATE TABLE cargo_flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flight_name VARCHAR(100) NOT NULL,
    flight_id VARCHAR(20) UNIQUE NOT NULL,
    source VARCHAR(50) NOT NULL,
    destination VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    departure_time TIME NOT NULL,
    arrival_time TIME NOT NULL,
    price_per_kg DECIMAL(10,2) NOT NULL,
    total_weight INT NOT NULL,
    available_weight INT NOT NULL
);

-- Cargo Bookings Table
CREATE TABLE cargo_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    flight_id INT,
    weight INT NOT NULL,
    total_amount DECIMAL(10,2),
    status ENUM('Confirmed', 'Cancelled') DEFAULT 'Confirmed',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (flight_id) REFERENCES cargo_flights(id)
);

-- Feedback Table
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
ALTER TABLE flights MODIFY COLUMN status VARCHAR(20) DEFAULT 'Active';
UPDATE flights SET status = 'Active' WHERE status IS NULL;
UPDATE bookings SET status = 'Active' WHERE status IS NULL;
UPDATE bookings SET status = 'Active' WHERE status IS NULL;
ALTER TABLE flights ADD COLUMN name VARCHAR(255) NOT NULL;
UPDATE flights SET name = 'Flight 101' WHERE flight_id = '101';
ALTER TABLE bookings ADD COLUMN amount DECIMAL(10,2);
ALTER TABLE cargo_bookings ADD COLUMN amount DECIMAL(10,2);
ALTER TABLE bookings ADD COLUMN total_price DECIMAL(10,2) NOT NULL DEFAULT 0;
ALTER TABLE cargo_bookings ADD COLUMN total_price DECIMAL(10,2) NOT NULL DEFAULT 0;
ALTER TABLE cargo_flights ADD COLUMN status VARCHAR(20) DEFAULT 'Active';

ALTER TABLE feedback ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE bookings ADD COLUMN seat_no INT NOT NULL;
ALTER TABLE bookings MODIFY COLUMN seat_no INT NOT NULL;
ALTER TABLE bookings DROP COLUMN seat_no;
ALTER TABLE bookings ADD COLUMN seat_no INT NOT NULL;
ALTER TABLE bookings CHANGE COLUMN seat_no seat_number INT;
ALTER TABLE bookings ADD COLUMN seat_type VARCHAR(20) NOT NULL;
ALTER TABLE bookings ADD COLUMN seat_count INT NOT NULL;
SELECT * FROM flights WHERE id = 'FLIGHT_ID_HERE';
UPDATE bookings SET status = 'Booked' WHERE status IS NULL;
ALTER TABLE bookings MODIFY COLUMN status VARCHAR(20) DEFAULT 'Booked';
ALTER TABLE bookings DROP COLUMN status;
ALTER TABLE bookings ADD COLUMN status VARCHAR(20) DEFAULT 'Booked';
SELECT * FROM bookings WHERE flight_id = 'YOUR_FLIGHT_ID';
SELECT * FROM bookings WHERE user_id = 'YOUR_USER_ID';
SELECT * FROM cargo_flights WHERE source='YOUR_SOURCE' AND destination='YOUR_DESTINATION' AND date='YYYY-MM-DD' AND available_weight > 0;
UPDATE cargo_flights SET date = '2025-03-10' WHERE flight_id = 'CARGO001';
UPDATE cargo_flights SET available_weight = total_weight WHERE available_weight = 0;
ALTER TABLE cargo_bookings 
ADD COLUMN phone VARCHAR(15) NOT NULL;
ALTER TABLE cargo_bookings 
ADD COLUMN IF NOT EXISTS email VARCHAR(255) NOT NULL,
ADD COLUMN IF NOT EXISTS address TEXT NOT NULL,
ADD COLUMN IF NOT EXISTS weight INT NOT NULL,
ADD COLUMN IF NOT EXISTS total_price DECIMAL(10,2) NOT NULL;
ALTER TABLE cargo_bookings MODIFY COLUMN flight_id VARCHAR(255);
ALTER TABLE cargo_flights MODIFY COLUMN flight_id VARCHAR(255);
ALTER TABLE cargo_flights ADD UNIQUE (flight_id);
SELECT DISTINCT flight_id FROM cargo_bookings WHERE flight_id NOT IN (SELECT flight_id FROM cargo_flights);
ALTER TABLE users ADD COLUMN email VARCHAR(255);ALTER TABLE bookings ADD COLUMN seats_booked INT(11) NOT NULL;
SELECT b.*, u.username, u.phone, u.email
FROM bookings b
JOIN users u ON b.user_id = u.id
WHERE b.flight_id = 104;
SELECT 
    COUNT(DISTINCT user_id) AS total_passengers,
    SUM(seat_count) AS total_seats_booked,
    SUM(total_price) AS total_revenue
FROM bookings
WHERE status = 'Booked';
ALTER TABLE users MODIFY COLUMN email VARCHAR(255) NOT NULL;
ALTER TABLE cargo_bookings ADD COLUMN weight_booked DECIMAL(10,2) NOT NULL;
SELECT id, user_id, phone, email, address 
FROM cargo_bookings 
WHERE phone IS NULL OR email IS NULL OR address IS NULL;
ALTER TABLE admin ADD COLUMN phone VARCHAR(15) NULL;
ALTER TABLE admin ADD COLUMN email VARCHAR(100) NULL;
ALTER TABLE admin ADD COLUMN address VARCHAR(255) NULL;
ALTER TABLE cargo_bookings MODIFY COLUMN weight_booked DECIMAL(10,2);
ALTER TABLE cargo_bookings MODIFY weight_booked DECIMAL(10,2) NOT NULL DEFAULT 0;
ALTER TABLE cargo_bookings ADD COLUMN booking_id VARCHAR(20);
UPDATE cargo_bookings 
SET booking_id = CONCAT('CB', id)
WHERE booking_id IS NULL OR booking_id = '';
ALTER TABLE cargo_bookings MODIFY COLUMN booking_id VARCHAR(20) NOT NULL UNIQUE;
$booking_id = "CB" . time(); // Unique ID using timestamp
$query = "INSERT INTO cargo_bookings (booking_id, flight_id, user_name, phone, email, address, weight, total_price, status) 
          VALUES ('$booking_id', '$flight_id', '$name', '$phone', '$email', '$address', '$weight', '$total_price', 'Confirmed')";
ALTER TABLE cargo_bookings ADD COLUMN aadhar_number VARCHAR(12) NOT NULL;
ALTER TABLE bookings 
ADD COLUMN aadhar_number VARCHAR(20) NOT NULL,
ADD COLUMN age INT NOT NULL,
ADD COLUMN gender ENUM('Male', 'Female', 'Other') NOT NULL;
ALTER TABLE bookings ADD COLUMN name VARCHAR(255) NOT NULL;



ALTER TABLE users 
ADD COLUMN profile_pic VARCHAR(255) DEFAULT 'default.jpg',
ADD COLUMN gender ENUM('Male', 'Female', 'Other') DEFAULT 'Other',
ADD COLUMN dob DATE NULL,
ADD COLUMN favorite_place VARCHAR(100) NULL,
ADD COLUMN address TEXT NULL;
ALTER TABLE users ADD about TEXT NULL;


## nethra raju
Delete

Fatal error: Uncaught TypeError: mysqli_num_rows(): Argument #1 ($result) must be of type mysqli_result, bool given in C:\xampp\htdocs\air171\flight_booking.php:262 Stack trace: #0 C:\xampp\htdocs\air171\flight_booking.php(262): mysqli_num_rows(false) #1 {main} thrown in C:\xampp\htdocs\air171\flight_booking.php on line 262

## Trae AI
I notice there's an error in the flight search results handling where mysqli_num_rows() is receiving a boolean instead of a mysqli result. This indicates the search query failed, likely due to a SQL error. I'll help modify the flight_booking.php file to add proper error handling for the search query, validate the SQL query execution, and ensure proper type checking before using mysqli_num_rows(). The key areas to focus on are the flight search query execution and results display logic.

Examining the database connection file to understand the SQL query execution context.

db.php :( 1 - 20 )

Adding proper error handling and validation for the flight search query to prevent the mysqli_num_rows() error.

flight_booking.php

+ 1 - 1

Review

Fixed a fatal error in flight_booking.php by adding proper validation for the SQL query result before calling mysqli_num_rows(). The issue was caused by trying to check the number of rows on a failed query result (boolean false). By adding a check for $result being truthy before calling mysqli_num_rows(), we ensure the function only runs on valid mysqli_result objects, preventing the TypeError.

Unknown system error, please try again later.

Get Help

## nethra raju
Delete

available_flights.php,avc.php,book_flight.php,booking_details.php,cancel_booking.php,cancel_flight.php,cargo_booking_details.php,cargo_boooking.php,cargo_cancel_process.php,cargo_cancel.php,cargo_cancel_process.php,cargo_cancel.php,cargo_details.php,cdb.php,change_password.php,confirm_booking.php,edit_profile.php,flight_details.php,login.php,logout.php,my_account.php,my_cargo_account.php,my_user.php,payment.php,process_cancel.php,process_payment.php,processs_cancel.php,search_flight.php,signup.php,user_dashboard.php,user_details.php,user_feedback.php,user_history.php  in these files remove all the css and styles and profeesionaly design the ui and there is a navbar in user_dashboard.php get the navbar and design the navbar profesionaly and create a file for that navbar and use that navbar in all pages use consistant across styles across all pages