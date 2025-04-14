<?php
// Include browser compatibility check
include_once 'browser-check.php';

// Get church settings
$query = "SELECT * FROM settings WHERE id = 1";
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);
$church_name = isset($settings['church_name']) ? $settings['church_name'] : 'JIA Somal-ot Church';
$church_tagline = isset($settings['tagline']) ? $settings['tagline'] : 'Transforming Lives Through Christ';
$church_logo = isset($settings['logo']) && $settings['logo'] ? 'uploads/logo/' . $settings['logo'] : 'img/jia.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $page_title; ?> - <?php echo $church_name; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/website-style.css">
    <link rel="stylesheet" href="css/responsive-modern.css">
    <link rel="stylesheet" href="css/modern-animations.css">
    <?php if (isset($additional_css)) echo $additional_css; ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="container header-inner">
            <a href="public_index.php" class="logo">
                <img src="<?php echo $church_logo; ?>" alt="<?php echo $church_name; ?> Logo">
                <div class="logo-text">
                    <h1><?php echo $church_name; ?></h1>
                </div>
            </a>
            <nav class="main-nav">
                <ul>
                    <li><a href="public_index.php"<?php if($current_page == 'home') echo ' class="active"'; ?>>Home</a></li>
                    <li><a href="about.php"<?php if($current_page == 'about') echo ' class="active"'; ?>>About Us</a></li>
                    <li><a href="services.php"<?php if($current_page == 'services') echo ' class="active"'; ?>>Services</a></li>
                    <li><a href="events_public.php"<?php if($current_page == 'events') echo ' class="active"'; ?>>Events</a></li>
                    <li><a href="ministries.php"<?php if($current_page == 'ministries') echo ' class="active"'; ?>>Ministries</a></li>
                    <li><a href="contact.php"<?php if($current_page == 'contact') echo ' class="active"'; ?>>Contact</a></li>
                    <li><a href="login.php" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                </ul>
            </nav>
            <div class="mobile-menu-toggle" aria-label="Toggle mobile menu" role="button" tabindex="0">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" aria-hidden="true">
        <div class="close-mobile-menu" aria-label="Close mobile menu" role="button" tabindex="0">
            <i class="fas fa-times"></i>
        </div>
        <div class="mobile-menu-container">
            <div class="mobile-logo">
                <img src="<?php echo $church_logo; ?>" alt="<?php echo $church_name; ?> Logo">
                <h2><?php echo $church_name; ?></h2>
            </div>
            <nav class="mobile-nav">
                <ul class="mobile-nav-menu">
                    <li><a href="public_index.php"<?php if($current_page == 'home') echo ' class="active"'; ?>>Home</a></li>
                    <li><a href="about.php"<?php if($current_page == 'about') echo ' class="active"'; ?>>About Us</a></li>
                    <li><a href="services.php"<?php if($current_page == 'services') echo ' class="active"'; ?>>Services</a></li>
                    <li><a href="events_public.php"<?php if($current_page == 'events') echo ' class="active"'; ?>>Events</a></li>
                    <li><a href="ministries.php"<?php if($current_page == 'ministries') echo ' class="active"'; ?>>Ministries</a></li>
                    <li><a href="contact.php"<?php if($current_page == 'contact') echo ' class="active"'; ?>>Contact</a></li>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                </ul>
            </nav>
            <div class="mobile-contact">
                <p><i class="fas fa-map-marker-alt"></i> <?php echo isset($settings['address']) ? $settings['address'] : '123 Church Street, City'; ?></p>
                <p><i class="fas fa-phone"></i> <?php echo isset($settings['phone']) ? $settings['phone'] : '(123) 456-7890'; ?></p>
                <p><i class="fas fa-envelope"></i> <?php echo isset($settings['email']) ? $settings['email'] : 'info@jiachurch.com'; ?></p>
            </div>
            <div class="mobile-social">
                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>

<script>
// Ensure mobile menu works properly
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const mobileOverlay = document.querySelector('.mobile-menu-overlay');
    const closeButton = document.querySelector('.close-mobile-menu');
    
    if (mobileToggle && mobileOverlay && closeButton) {
        // Handle click and keyboard events for better accessibility
        mobileToggle.addEventListener('click', openMobileMenu);
        mobileToggle.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                openMobileMenu();
            }
        });
        
        closeButton.addEventListener('click', closeMobileMenu);
        closeButton.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                closeMobileMenu();
            }
        });
        
        // Close when clicking outside menu
        mobileOverlay.addEventListener('click', function(e) {
            if (e.target === mobileOverlay) {
                closeMobileMenu();
            }
        });
        
        // Close when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileOverlay.classList.contains('active')) {
                closeMobileMenu();
            }
        });
    }
    
    function openMobileMenu() {
        mobileOverlay.classList.add('active');
        mobileOverlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        
        // Focus on close button for accessibility
        setTimeout(() => {
            closeButton.focus();
        }, 100);
    }
    
    function closeMobileMenu() {
        mobileOverlay.classList.remove('active');
        mobileOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        
        // Return focus to toggle button
        mobileToggle.focus();
    }
});
</script>

