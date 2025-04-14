<?php
session_start();
require_once 'config/db.php';

// Set the default timezone
date_default_timezone_set('Asia/Manila'); // Change to your timezone if needed

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get church settings
$query = "SELECT * FROM settings WHERE id = 1";
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);
$church_name = isset($settings['church_name']) ? $settings['church_name'] : 'JIA Somal-ot Church';
$church_logo = isset($settings['logo']) && $settings['logo'] ? 'uploads/logo/' . $settings['logo'] : 'img/jia.png';

$success_message = '';
$error_message = '';
$token = '';
$valid_token = false;
$user_id = 0;

// Check if token is provided
if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    
    // Check if token exists and is valid
    $current_time = date('Y-m-d H:i:s');
    $query = "SELECT * FROM password_resets WHERE token = '$token' AND expires_at > '$current_time'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $reset = mysqli_fetch_assoc($result);
        $user_id = $reset['user_id'];
        $valid_token = true;
    } else {
        $error_message = "Invalid or expired token. Please request a new password reset link.";
    }
} else {
    $error_message = "No reset token provided. Please request a password reset from the forgot password page.";
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate passwords
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } else {
        // Hash the new password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Update the user's password
        $query = "UPDATE users SET password = '$password_hash', updated_at = NOW() WHERE id = $user_id";
        
        if (mysqli_query($conn, $query)) {
            // Delete the used token
            $query = "DELETE FROM password_resets WHERE token = '$token'";
            mysqli_query($conn, $query);
            
            $success_message = "Your password has been reset successfully. You can now <a href='login.php'>login</a> with your new password.";
            $valid_token = false; // Hide the form
        } else {
            $error_message = "Error updating password: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Church Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/enhanced-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: rgb(33, 117, 44);
            --primary-hover: rgb(28, 100, 38);
            --secondary-color: #3498db;
            --accent-color: #f39c12;
            --text-color: #333;
            --text-muted: #7f8c8d;
            --bg-color: #ffffff;
            --card-bg: #ffffff;
            --border-color: #e0e0e0;
            --input-bg: #f8f9fa;
            --input-focus-bg: #ffffff;
            --shadow-sm: 0 2px 5px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 5px 15px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s ease;
            --border-radius: 10px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        @keyframes buttonGlow {
            0% { box-shadow: 0 0 5px rgba(33, 117, 44, 0.5); }
            50% { box-shadow: 0 0 20px rgba(33, 117, 44, 0.8); }
            100% { box-shadow: 0 0 5px rgba(33, 117, 44, 0.5); }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.3;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background-color: var(--primary-color);
            opacity: 0.3;
            animation: float 8s infinite ease-in-out;
        }

        .reset-password-container {
            width: 100%;
            max-width: 450px;
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            animation: fadeIn 0.6s ease-out;
            position: relative;
            z-index: 10;
        }

        .reset-password-header {
            position: relative;
            padding: 30px 20px;
            text-align: center;
            background-color: var(--primary-color);
            color: white;
            overflow: hidden;
        }

        .header-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            background-size: 200% 200%;
            animation: gradient 15s ease infinite;
            opacity: 1;
            z-index: -1;
        }

        .light-effect {
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
            z-index: 0;
        }

        .reset-password-header img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 50%;
            padding: 5px;
            background-color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
            animation: pulse 3s infinite ease-in-out;
        }

        .reset-password-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }

        .reset-password-header p {
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .gradient-divider {
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color), var(--primary-color));
            background-size: 300% 300%;
            animation: gradient 5s ease infinite;
        }

        .reset-password-form {
            padding: 30px;
        }

        .form-intro {
            margin-bottom: 25px;
            color: var(--text-color);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
            font-size: 0.95rem;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            transition: var(--transition);
        }

        .input-with-icon input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            background-color: var(--input-bg);
            color: var(--text-color);
        }

        .input-with-icon input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(33, 117, 44, 0.15);
            outline: none;
            background-color: var(--input-focus-bg);
            transform: scale(1.01);
        }

        .input-with-icon input:focus + i {
            color: var(--primary-color);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition);
            z-index: 2;
        }

        .toggle-password:hover {
            color: var(--primary-color);
        }

        .reset-btn {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .reset-btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(33, 117, 44, 0.3);
        }

        .reset-btn:active {
            transform: translateY(0);
        }

        .reset-btn i {
            margin-right: 8px;
        }

        .reset-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        .reset-btn:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(20, 20);
                opacity: 0;
            }
        }

        .reset-password-footer {
            text-align: center;
            padding: 0 30px 30px;
        }

        .reset-password-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
            display: inline-block;
        }

        .reset-password-footer a::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: var(--primary-color);
            transform: scaleX(0);
            transform-origin: bottom right;
            transition: transform 0.3s;
        }

        .reset-password-footer a:hover::after {
            transform: scaleX(1);
            transform-origin: bottom left;
        }

        .success-message,
        .error-message {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            font-size: 0.95rem;
            display: flex;
            align-items: flex-start;
            line-height: 1.5;
        }

        .success-message {
            background-color: rgba(39, 174, 96, 0.1);
            color: #27ae60;
            border-left: 4px solid #27ae60;
        }

        .error-message {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            border-left: 4px solid #e74c3c;
            animation: shake 0.5s ease-in-out;
        }

        .success-message i,
        .error-message i {
            margin-right: 10px;
            font-size: 1.1rem;
            margin-top: 2px;
        }

        .success-message a {
            color: #27ae60;
            font-weight: 600;
            text-decoration: underline;
        }

        .success-message a:hover {
            text-decoration: none;
        }

        .password-requirements {
            background-color: rgba(52, 152, 219, 0.05);
            padding: 15px;
            border-radius: var(--border-radius);
            margin-top: 10px;
            border-left: 3px solid var(--secondary-color);
        }

        .password-requirements h4 {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-color);
        }

        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
        }

        .password-requirements li {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        .password-requirements li:last-child {
            margin-bottom: 0;
        }

        /* Responsive styles */
        @media (max-width: 576px) {
            .reset-password-container {
                max-width: 100%;
                border-radius: var(--border-radius);
                margin: 0 10px;
            }

            .reset-password-header {
                padding: 25px 15px;
            }

            .reset-password-header img {
                width: 70px;
                height: 70px;
            }

            .reset-password-header h1 {
                font-size: 1.5rem;
            }

            .reset-password-form {
                padding: 25px 20px;
            }

            .input-with-icon input {
                padding: 12px 12px 12px 40px;
                font-size: 0.95rem;
            }

            .reset-btn {
                padding: 12px;
            }
        }

        /* Generate 20 particles with random positions and sizes */
        <?php for($i = 1; $i <= 20; $i++): ?>
        .particle:nth-child(<?php echo $i; ?>) {
            width: <?php echo rand(5, 20); ?>px;
            height: <?php echo rand(5, 20); ?>px;
            left: <?php echo rand(1, 99); ?>%;
            top: <?php echo rand(1, 99); ?>%;
            animation-delay: <?php echo $i * 0.3; ?>s;
            animation-duration: <?php echo rand(8, 15); ?>s;
        }
        <?php endfor; ?>
    </style>
</head>
<body>
    <div class="particles">
        <?php for($i = 1; $i <= 20; $i++): ?>
            <div class="particle"></div>
        <?php endfor; ?>
    </div>

    <div class="reset-password-container">
        <div class="reset-password-header">
            <div class="header-bg"></div>
            <div class="light-effect"></div>
            <img src="<?php echo $church_logo; ?>" alt="<?php echo $church_name; ?> Logo">
            <h1>Reset Password</h1>
            <p><?php echo $church_name; ?> Management System</p>
        </div>
        
        <div class="gradient-divider"></div>
        
        <div class="reset-password-form">
            <?php if($success_message): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <div><?php echo $success_message; ?></div>
                </div>
            <?php endif; ?>
            
            <?php if($error_message): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <div><?php echo $error_message; ?></div>
                </div>
            <?php endif; ?>
            
            <?php if($valid_token): ?>
                <p class="form-intro">Please enter your new password below to reset your account access.</p>
                
                <form action="reset_password.php?token=<?php echo $token; ?>" method="post">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <div class="input-with-icon">
                            <input type="password" id="password" name="password" placeholder="Enter your new password" required>
                            <i class="fas fa-lock"></i>
                            <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                        </div>
                        <div class="password-requirements">
                            <h4>Password Requirements:</h4>
                            <ul>
                                <li>At least 8 characters long</li>
                                <li>Include a mix of letters, numbers, and symbols</li>
                                <li>Avoid using easily guessable information</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <div class="input-with-icon">
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" required>
                            <i class="fas fa-lock"></i>
                            <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i>
                        </div>
                    </div>
                    
                    <button type="submit" class="reset-btn">
                        <i class="fas fa-key"></i> Reset Password
                    </button>
                </form>
            <?php endif; ?>
            
            <div class="reset-password-footer">
                <p><a href="login.php">Back to Login</a></p>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
        
        // Toggle confirm password visibility
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirm_password');
        
        if (toggleConfirmPassword && confirmPasswordInput) {
            toggleConfirmPassword.addEventListener('click', function() {
                const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
        
        // Add focus class to input container
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
        
        // Auto-hide success/error messages after 10 seconds
        const messages = document.querySelectorAll('.success-message, .error-message');
        messages.forEach(message => {
            setTimeout(() => {
                message.style.opacity = '0';
                setTimeout(() => {
                    message.style.display = 'none';
                }, 500);
            }, 10000);
        });
    });
    </script>
</body>
</html>

