<?php
require_once 'config/db.php';

// SQL to create the password_resets table
$sql = "
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Execute the query
if (mysqli_query($conn, $sql)) {
    echo "Password resets table created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

// Close the connection
mysqli_close($conn);
?>

