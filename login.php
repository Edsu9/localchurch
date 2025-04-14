<?php
session_start();
require_once 'config/db.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); // Redirect to dashboard instead of index.php
    exit();
}

// Process login form
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username) || empty ($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Check user credentials
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect to dashboard
                header("Location: dashboard.php"); // Redirect to dashboard instead of index.php
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }
    }
}

// Get church settings
$query = "SELECT * FROM settings WHERE id = 1";
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);
$church_name = isset($settings['church_name']) ? $settings['church_name'] : 'Church Management System';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo $church_name; ?> Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Add SameSite attribute to cookies -->
    <meta http-equiv="Set-Cookie" content="SameSite=None; Secure">
    <style>
    :root {
    --primary-color: rgb(33, 117, 44);
    --primary-dark: rgb(28, 100, 38);
    --secondary-color: #3498db;
    --accent-color: #f39c12;
    --text-color: #333;
    --text-light: #666;
    --text-muted: #7f8c8d;
    --white: #ffffff;
    --gray-light: #f8f9fa;
    --gray-medium: #e0e0e0;
    --error-color: #d32f2f;
    --bg-color: #ffffff;
    --card-bg: #ffffff;
    --border-color: #e0e0e0;
    --input-bg: #ffffff;
    --input-focus-bg: #ffffff;
    --shadow-sm: 0 2px 5px rgba(0, 0, 0, 0.05);
    --shadow-md: 0 5px 15px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.15);
    --transition: all 0.3s ease;
    --radius-sm: 4px;
    --radius-md: 8px;
    --radius-lg: 12px;
}

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #ffffff;
        color: var(--text-color);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        position: relative;
        overflow-x: hidden;
    }

    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('img/church-bg.jpg');
        background-size: cover;
        background-position: center;
        opacity: 0.05;
        z-index: -1;
    }

    .login-container {
        width: 100%;
        max-width: 450px;
        background-color: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        position: relative;
        transform: translateY(0);
        transition: transform 0.5s ease, box-shadow 0.5s ease;
        animation: fadeInUp 0.8s ease forwards;
    }

    .login-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: var(--white);
        padding: 35px 30px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .login-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        transform: rotate(30deg);
        z-index: 1;
    }

    .login-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        position: relative;
        z-index: 2;
    }

    .login-logo img {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
        }
    }

    .login-logo img:hover {
        transform: scale(1.05);
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.3);
    }

    .login-logo h1 {
        margin-left: 15px;
        font-size: 1.8rem;
        font-weight: 600;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .login-header p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 0;
        position: relative;
        z-index: 2;
    }

    .login-form {
        padding: 35px 30px;
        position: relative;
    }

    .login-form::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color), var(--primary-color));
        background-size: 300% 100%;
        animation: gradientShift 5s ease infinite;
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .form-group {
        margin-bottom: 25px;
        position: relative;
    }

    .form-group label {
        display: block;
        margin-bottom: 10px;
        font-weight: 500;
        color: var(--text-color);
        font-size: 0.95rem;
        transition: var(--transition);
        transform-origin: left;
    }

    .form-group .input-with-icon {
        position: relative;
        overflow: hidden;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }

    .form-group .input-with-icon:hover {
        box-shadow: var(--shadow-md);
    }

    .form-group .input-with-icon i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-light);
        transition: var(--transition);
        font-size: 1.1rem;
    }

    .form-group input {
    width: 100%;
    padding: 16px 16px 16px 50px;
    border: 2px solid var(--gray-medium);
    border-radius: var(--radius-md);
    background-color: var(--white);
    font-family: inherit;
    font-size: 1rem;
    color: var(--text-color);
    transition: var(--transition);
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
}

    .form-group input:focus {
        border-color: var(--primary-color);
        background-color: var(--white);
        outline: none;
        box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.15);
    }

    .form-group input:focus + i {
        color: var(--primary-color);
        transform: translateY(-50%) scale(1.1);
    }

    .form-group .error-message {
        color: var(--error-color);
        font-size: 0.85rem;
        margin-top: 8px;
        display: flex;
        align-items: center;
        animation: fadeIn 0.3s ease;
    }

    .form-group .error-message i {
        margin-right: 5px;
    }

    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        font-size: 0.9rem;
    }

    .remember-me {
        display: flex;
        align-items: center;
    }

    .remember-me input[type="checkbox"] {
        position: relative;
        width: 18px;
        height: 18px;
        margin-right: 8px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        border: 2px solid var(--gray-medium);
        border-radius: 4px;
        outline: none;
        transition: var(--transition);
        cursor: pointer;
    }

    .remember-me input[type="checkbox"]:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .remember-me input[type="checkbox"]:checked::before {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 12px;
    }

    .remember-me label {
        cursor: pointer;
    }

    .forgot-password {
        color: var(--primary-color);
        text-decoration: none;
        transition: var(--transition);
        position: relative;
        font-weight: 500;
    }

    .forgot-password::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background-color: var(--primary-color);
        transition: var(--transition);
    }

    .forgot-password:hover {
        color: var(--primary-dark);
    }

    .forgot-password:hover::after {
        width: 100%;
    }

    .login-button {
        width: 100%;
        padding: 16px 20px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: var(--white);
        border: none;
        border-radius: var(--radius-md);
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .login-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0) 100%);
        transition: all 0.6s ease;
        z-index: -1;
    }

    .login-button:hover {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(46, 125, 50, 0.4);
    }

    .login-button:hover::before {
        left: 100%;
    }

    .login-button:active {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(46, 125, 50, 0.3);
    }

    .login-button.loading {
        pointer-events: none;
        opacity: 0.8;
    }

    .login-button .spinner {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 24px;
        height: 24px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: var(--white);
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: translate(-50%, -50%) rotate(360deg); }
    }

    .login-button.loading .button-text {
        visibility: hidden;
    }

    .login-button.loading .spinner {
        display: block;
    }

    .login-footer {
        text-align: center;
        padding: 0 30px 30px;
        font-size: 0.95rem;
        color: var(--text-light);
    }

    .login-footer a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
        position: relative;
    }

    .login-footer a::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background-color: var(--primary-color);
        transition: var(--transition);
    }

    .login-footer a:hover {
        color: var(--primary-dark);
    }

    .login-footer a:hover::after {
        width: 100%;
    }

    .error-alert {
        background-color: rgba(211, 47, 47, 0.1);
        color: var(--error-color);
        padding: 15px;
        border-radius: var(--radius-md);
        margin-bottom: 25px;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        border-left: 4px solid var(--error-color);
        animation: shakeX 0.5s ease;
    }

    @keyframes shakeX {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    .error-alert i {
        margin-right: 10px;
        font-size: 1.2rem;
    }

    /* Floating particles animation */
    .particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        overflow: hidden;
    }

    .particle {
        position: absolute;
        border-radius: 50%;
        background-color: rgba(46, 125, 50, 0.1);
        pointer-events: none;
    }

    @media (max-width: 768px) {
        .login-container {
            max-width: 100%;
        }

        .login-header {
            padding: 30px 20px;
        }

        .login-form {
            padding: 30px 25px;
        }

        .login-logo h1 {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .login-container {
            border-radius: var(--radius-md);
        }

        .login-header {
            padding: 25px 20px;
        }

        .login-form {
            padding: 25px 20px;
        }

        .login-logo {
            flex-direction: column;
            gap: 15px;
        }

        .login-logo h1 {
            margin-left: 0;
            font-size: 1.4rem;
        }

        .form-options {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .login-button {
            padding: 14px 20px;
        }
    }

    /* Dark mode support - overridden to maintain white theme */
@media (prefers-color-scheme: dark) {
    body {
        background-color: #ffffff;
    }

    .login-container {
        background-color: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .form-group label,
    .login-footer,
    .remember-me label {
        color: var(--text-color);
    }

    .form-group input {
        background-color: #ffffff;
        border-color: var(--gray-medium);
        color: var(--text-color);
    }

    .form-group input:focus {
        background-color: #ffffff;
    }

    .error-alert {
        background-color: rgba(211, 47, 47, 0.1);
    }
}
</style>
</head>
<body>
    <div class="particles" id="particles"></div>
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <img src="<?php echo $church_logo; ?>" alt="<?php echo $church_name; ?> Logo" onerror="this.src='img/jia.png'; this.alt='Church Logo';">
            </div>
            <h1>Sign In</h1>

            <p>Jesus Is Alive Community<p>
        </div>
        
        <div class="login-form">
            <?php if (!empty($error)): ?>
            <div class="error-alert">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form action="login.php" method="post" id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-with-icon">
                        <input type="text" id="username" name="username" required autocomplete="username">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <input type="password" id="password" name="password" required autocomplete="current-password">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
                </div>
                
                <button type="submit" class="login-button" id="loginButton">
                    <span class="button-text">Sign In</span>
                    <span class="spinner"></span>
                </button>
            </form>
        </div>
        
        <div class="login-footer">
            <p>Return to <a href="public_index.php">Church Website</a></p>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form validation and loading state
        const loginForm = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');
        
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                const username = document.getElementById('username').value.trim();
                const password = document.getElementById('password').value.trim();
                let isValid = true;
                
                // Remove any existing error messages
                const existingErrors = document.querySelectorAll('.error-message');
                existingErrors.forEach(function(error) {
                    error.remove();
                });
                
                // Validate username
                if (username === '') {
                    isValid = false;
                    const usernameInput = document.getElementById('username');
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'error-message';
                    errorMessage.innerHTML = '<i class="fas fa-exclamation-circle"></i> Please enter your username';
                    usernameInput.parentNode.parentNode.appendChild(errorMessage);
                    usernameInput.focus();
                }
                
                // Validate password
                if (password === '') {
                    isValid = false;
                    const passwordInput = document.getElementById('password');
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'error-message';
                    errorMessage.innerHTML = '<i class="fas fa-exclamation-circle"></i> Please enter your password';
                    passwordInput.parentNode.parentNode.appendChild(errorMessage);
                    
                    if (username !== '') {
                        passwordInput.focus();
                    }
                }
                
                if (isValid) {
                    // Show loading state
                    loginButton.classList.add('loading');
                } else {
                    e.preventDefault();
                }
            });
        }
        
        // Fix for image loading errors
        const logoImage = document.querySelector('.login-logo img');
        if (logoImage) {
            logoImage.addEventListener('error', function() {
                if (!this.src.includes('jia.png')) {
                    this.src = 'img/jia.png';
                    this.alt = 'Church Logo';
                }
            });
        }
        
        // Create floating particles
        const particlesContainer = document.getElementById('particles');
        if (particlesContainer) {
            const particleCount = window.innerWidth < 768 ? 15 : 30;
            
            for (let i = 0; i < particleCount; i++) {
                createParticle();
            }
            
            function createParticle() {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                // Random size between 5px and 20px
                const size = Math.random() * 15 + 5;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                // Random position
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                
                // Random opacity
                particle.style.opacity = Math.random() * 0.5 + 0.1;
                
                // Random animation duration between 10s and 30s
                const duration = Math.random() * 20 + 10;
                particle.style.animation = `float ${duration}s linear infinite`;
                
                // Set animation properties
                particle.style.animationDelay = `${Math.random() * 5}s`;
                
                // Add keyframe animation dynamically
                const style = document.createElement('style');
                const x = Math.random() * 100 - 50; // Random x movement between -50% and 50%
                const y = Math.random() * 100 - 50; // Random y movement between -50% and 50%
                
                style.textContent = `
                    @keyframes float {
                        0% {
                            transform: translate(0, 0);
                        }
                        50% {
                            transform: translate(${x}px, ${y}px);
                        }
                        100% {
                            transform: translate(0, 0);
                        }
                    }
                `;
                
                document.head.appendChild(style);
                particlesContainer.appendChild(particle);
            }
        }
        
        // Add input focus effects
        const inputs = document.querySelectorAll('.form-group input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentNode.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentNode.style.transform = 'scale(1)';
            });
        });
    });
    </script>
    <script src="js/responsive.js"></script>
</body>
</html>

