<?php
// Script to add new columns to the settings table
require_once 'config/db.php';

// Check if the columns already exist
$query = "SHOW COLUMNS FROM settings LIKE 'hero_title'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    // Columns don't exist, add them
    $query = "ALTER TABLE settings
              ADD COLUMN hero_title VARCHAR(255) DEFAULT NULL,
              ADD COLUMN hero_subtitle VARCHAR(255) DEFAULT NULL,
              ADD COLUMN hero_background VARCHAR(255) DEFAULT NULL,
              ADD COLUMN welcome_title VARCHAR(255) DEFAULT NULL,
              ADD COLUMN welcome_content TEXT DEFAULT NULL,
              ADD COLUMN sunday_morning_time VARCHAR(50) DEFAULT NULL,
              ADD COLUMN sunday_morning_desc VARCHAR(100) DEFAULT NULL,
              ADD COLUMN sunday_afternoon_time VARCHAR(50) DEFAULT NULL,
              ADD COLUMN sunday_afternoon_desc VARCHAR(100) DEFAULT NULL,
              ADD COLUMN midweek_time VARCHAR(50) DEFAULT NULL,
              ADD COLUMN midweek_desc VARCHAR(100) DEFAULT NULL,
              ADD COLUMN ministries_title VARCHAR(255) DEFAULT NULL,
              ADD COLUMN ministries_subtitle VARCHAR(255) DEFAULT NULL,
              ADD COLUMN meta_title VARCHAR(255) DEFAULT NULL,
              ADD COLUMN meta_description TEXT DEFAULT NULL,
              ADD COLUMN meta_keywords TEXT DEFAULT NULL,
              ADD COLUMN facebook_url VARCHAR(255) DEFAULT NULL,
              ADD COLUMN twitter_url VARCHAR(255) DEFAULT NULL,
              ADD COLUMN instagram_url VARCHAR(255) DEFAULT NULL,
              ADD COLUMN youtube_url VARCHAR(255) DEFAULT NULL";
    
    if (mysqli_query($conn, $query)) {
        echo "Website settings columns added successfully!";
    } else {
        echo "Error adding website settings columns: " . mysqli_error($conn);
    }
} else {
    echo "Website settings columns already exist.";
}

// Create uploads/website directory if it doesn't exist
$upload_dir = 'uploads/website/';
if (!file_exists($upload_dir)) {
    if (mkdir($upload_dir, 0777, true)) {
        echo "<br>Website uploads directory created successfully!";
    } else {
        echo "<br>Error creating website uploads directory.";
    }
} else {
    echo "<br>Website uploads directory already exists.";
}
?>

