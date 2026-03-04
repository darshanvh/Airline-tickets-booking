# Air07 - Airline Management System

A comprehensive web-based airline management system built with PHP and MySQL. This system allows administrators and users to manage flights, cargo bookings, passenger bookings, and user accounts.

## Features

### Admin Features
- **Dashboard**: Overview of all bookings and flights
- **Flight Management**: Add, edit, and manage flights
- **Cargo Management**: Add, edit, and manage cargo shipments
- **Passenger Bookings**: View and manage passenger flight bookings
- **Cargo Bookings**: View and manage cargo bookings
- **User Management**: View and manage user accounts
- **Feedback Management**: View user feedback
- **Admin Profile**: Manage admin accounts and settings

### User Features
- **Flight Booking**: Search and book available flights
- **Cargo Booking**: Book cargo shipments
- **Payment Integration**: Process flight and cargo payments
- **Booking Management**: View, modify, and cancel bookings
- **Passenger List**: View passenger details for bookings
- **User Dashboard**: Personal dashboard with booking history
- **Profile Management**: Edit profile and change password
- **Feedback**: Submit feedback about services

## Project Structure

```
air07/
├── admin/
│   ├── admin_dashboard.php      # Admin dashboard
│   ├── manage_flight.php         # Flight management
│   ├── manage_cargo.php          # Cargo management
│   ├── admin_login.php           # Admin login
│   └── ...
├── css/
│   ├── admin.css                 # Admin styling
│   ├── dashboard.css             # Dashboard styling
│   ├── login.css                 # Login styling
│   └── signup.css                # Signup styling
├── img/                          # Images directory
├── uploads/                      # User uploads directory
├── db.php                        # Database connection
├── login.php                     # User login
├── signup.php                    # User registration
├── user_dashboard.php            # User dashboard
├── book_flight.php               # Flight booking
├── book_cargo.php                # Cargo booking
└── air.sql                       # Database schema
```

## Installation

### Prerequisites
- XAMPP or similar PHP/MySQL environment
- PHP 7.0 or higher
- MySQL 5.7 or higher

### Setup Steps

1. **Clone/Copy Project**
   - Place the project in `htdocs/air07/`

2. **Create Database**
   - Open phpMyAdmin
   - Import `air.sql` file
   - Database will be created with all necessary tables

3. **Configure Database Connection**
   - Update `db.php` with your database credentials:
     ```php
     $host = 'localhost';
     $user = 'root';
     $password = '';
     $database = 'air07';
     ```

4. **Start Services**
   - Start Apache and MySQL from XAMPP

5. **Access Application**
   - User Portal: `http://localhost/air07/`
   - Admin Portal: `http://localhost/air07/admin/admin_login.php`

## Database Schema

### Main Tables
- **users**: User accounts and profiles
- **flights**: Flight information and schedules
- **bookings**: Flight bookings
- **cargo**: Cargo shipments
- **cargo_bookings**: Cargo booking details
- **payments**: Payment transactions
- **feedback**: User feedback

## Usage

### For Users
1. Sign up for an account
2. Log in to dashboard
3. Search and book flights
4. Make cargo bookings
5. Process payments
6. View booking history

### For Administrators
1. Log in to admin portal with admin credentials
2. Access dashboard to monitor bookings
3. Manage flights and cargo
4. Review user feedback
5. Manage user accounts

## Key Files

| File | Purpose |
|------|---------|
| `db.php` | Database connection configuration |
| `login.php` | User authentication |
| `admin_login.php` | Admin authentication |
| `user_dashboard.php` | Main user interface |
| `admin_dashboard.php` | Admin main interface |
| `book_flight.php` | Flight booking process |
| `book_cargo.php` | Cargo booking process |
| `payment.php` | Payment processing |

## Default Credentials

### Admin Access
- Path: `/admin/admin_login.php`
- Username/Password: (Set during initial setup)

## Features in Development

- Enhanced payment gateway integration
- Real-time notification system
- Mobile app integration
- Advanced reporting system

## Support

For issues or questions, please contact the development team or refer to the admin dashboard for documentation.

## License

This project is proprietary software. Unauthorized copying or distribution is prohibited.

---

