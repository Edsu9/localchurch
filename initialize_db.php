<?php
// Database configuration
require_once 'config/db.php';

// Check if the database connection is successful
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Create users table if it doesn't exist
$users_table = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    reset_token VARCHAR(64) DEFAULT NULL,
    reset_expires DATETIME DEFAULT NULL
)";

if (mysqli_query($conn, $users_table)) {
    echo "Users table created successfully<br>";
} else {
    echo "Error creating users table: " . mysqli_error($conn) . "<br>";
}

// Create members table if it doesn't exist
$members_table = "CREATE TABLE IF NOT EXISTS members (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    date_of_birth DATE,
    join_date DATE,
    status ENUM('active', 'inactive', 'visitor', 'new member') NOT NULL DEFAULT 'active',
    notes TEXT,
    profile_photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $members_table)) {
    echo "Members table created successfully<br>";
} else {
    echo "Error creating members table: " . mysqli_error($conn) . "<br>";
}

// Create events table if it doesn't exist
$events_table = "CREATE TABLE IF NOT EXISTS events (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME,
    location VARCHAR(100),
    status ENUM('scheduled', 'cancelled', 'postponed', 'completed') NOT NULL DEFAULT 'scheduled',
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $events_table)) {
    echo "Events table created successfully<br>";
} else {
    echo "Error creating events table: " . mysqli_error($conn) . "<br>";
}

// Create donations table if it doesn't exist
$donations_table = "CREATE TABLE IF NOT EXISTS donations (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    member_id INT(11),
    amount DECIMAL(10,2) NOT NULL,
    donation_date DATE NOT NULL,
    payment_method ENUM('cash', 'check', 'credit card', 'bank transfer', 'other') NOT NULL DEFAULT 'cash',
    purpose VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE SET NULL
)";

if (mysqli_query($conn, $donations_table)) {
    echo "Donations table created successfully<br>";
} else {
    echo "Error creating donations table: " . mysqli_error($conn) . "<br>";
}

// Create settings table if it doesn't exist
$settings_table = "CREATE TABLE IF NOT EXISTS settings (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    setting_name VARCHAR(100) NOT NULL,
    setting_value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $settings_table)) {
    echo "Settings table created successfully<br>";
} else {
    echo "Error creating settings table: " . mysqli_error($conn) . "<br>";
}

// Check if admin user exists
$check_admin = "SELECT * FROM users WHERE username = 'admin'";
$result = mysqli_query($conn, $check_admin);

if (mysqli_num_rows($result) == 0) {
    // Create default admin user
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $create_admin = "INSERT INTO users (username, password, email, full_name, role) 
                     VALUES ('admin', '$admin_password', 'admin@example.com', 'System Administrator', 'admin')";
    
    if (mysqli_query($conn, $create_admin)) {
        echo "Default admin user created successfully<br>";
    } else {
        echo "Error creating default admin user: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "Admin user already exists<br>";
}

// Insert default church settings if they don't exist
$default_settings = [
    ['church_name', 'JIA Somal-ot Church'],
    ['church_address', '123 Church Street, City, Country'],
    ['church_phone', '+1234567890'],
    ['church_email', 'info@jiachurch.com']
];

foreach ($default_settings as $setting) {
    $name = $setting[0];
    $value = $setting[1];
    
    $check_setting = "SELECT * FROM settings WHERE setting_name = '$name'";
    $result = mysqli_query($conn, $check_setting);
    
    if (mysqli_num_rows($result) == 0) {
        $insert_setting = "INSERT INTO settings (setting_name, setting_value) VALUES ('$name', '$value')";
        
        if (mysqli_query($conn, $insert_setting)) {
            echo "Default setting '$name' created successfully<br>";
        } else {
            echo "Error creating default setting '$name': " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "Setting '$name' already exists<br>";
    }
}

echo "<br><strong>Database initialization completed!</strong><br>";
echo "<a href='login.php'>Go to Login Page</a>";

mysqli_close($conn);
?>

