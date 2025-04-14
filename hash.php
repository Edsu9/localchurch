<?php
// Sample username and password
$username = 'admin';  // Replace with the username you want
$password = 'admin123';  // The plain-text password you want to hash
$email = 'admin@church.com';  // Replace with the email you want
$role = 'admin';  // Set the role (either 'admin' or 'staff')

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Output the hashed password
echo "Username: $username\n";
echo "Password: $hashed_password\n";  // This is the hashed password you can insert into the database
echo "Email: $email\n";
echo "Role: $role\n";

// You can now insert the values (username, hashed_password, email, and role) into your database manually
?>
