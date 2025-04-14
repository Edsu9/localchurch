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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if email exists in the database
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Generate a unique token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store the token in the database
        $user_id = $user['id'];
        $query = "INSERT INTO password_resets (user_id, token, expires_at, created_at) 
                  VALUES ($user_id, '$token', '$expires', NOW())";
        
        if (mysqli_query($conn, $query)) {
            // In a real application, you would send an email with the reset link
            // For this local application, we'll just display the reset link
            $reset_link = "reset_password.php?token=$token";
            $success_message = "A request to reset your password has been made for your account. Please click the link below to set a new password: <a href='$reset_link'>Reset Password</a>";
        } else {
            $error_message = "Error generating reset token: " . mysqli_error($conn);
        }
    } else {
        $error_message = "No account found with that email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Church Management System</title>
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

        .forgot-password-container {
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

        .forgot-password-header {
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

        .forgot-password-header img {
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

        .forgot-password-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }

        .forgot-password-header p {
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

        .forgot-password-form {
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

        .forgot-password-footer {
            text-align: center;
            padding: 0 30px 30px;
        }

        .forgot-password-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
            display: inline-block;
        }

        .forgot-password-footer a::after {
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

        .forgot-password-footer a:hover::after {
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

        /* Responsive styles */
        @media (max-width: 576px) {
            .forgot-password-container {
                max-width: 100%;
                border-radius: var(--border-radius);
                margin: 0 10px;
            }

            .forgot-password-header {
                padding: 25px 15px;
            }

            .forgot-password-header img {
                width: 70px;
                height: 70px;
            }

            .forgot-password-header h1 {
                font-size: 1.5rem;
            }

            .forgot-password-form {
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

    <div class="forgot-password-container">
        <div class="forgot-password-header">
            <div class="header-bg"></div>
            <div class="light-effect"></div>
            <img src="<?php echo $church_logo; ?>" alt="<?php echo $church_name; ?> Logo">
            <h1>Forgot Password</h1>
            <p>Jesus Is Alive Community</p>
        </div>
        
        <div class="gradient-divider"></div>
        
        <div class="forgot-password-form">
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
            
            <p class="form-intro">Enter your email address below and we'll send you a link to reset your password.</p>
            
            <form action="forgot_password.php" method="post">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
                
                <button type="submit" class="reset-btn">
                    <i class="fas fa-paper-plane"></i> Send Reset Link
                </button>
            </form>
            
            <div class="forgot-password-footer">
                <p>Remember your password? <a href="login.php">Back to Login</a></p>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
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

