<?php
// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Get church settings if available
$church_name = 'JIA Somal-ot Community';
$church_tagline = '';
$church_logo = '';

if (file_exists('config/db.php')) {
    require_once 'config/db.php';
    
    // Get settings from database if table exists
    $check_table = $conn->query("SHOW TABLES LIKE 'settings'");
    if ($check_table && $check_table->num_rows > 0) {
        $settings_query = "SELECT * FROM settings WHERE id = 1";
        $settings_result = $conn->query($settings_query);
        
        if ($settings_result && $settings_result->num_rows > 0) {
            $settings = $settings_result->fetch_assoc();
            $church_name = $settings['church_name'] ?? 'JIA Somal-ot Community';
            $church_tagline = $settings['church_tagline'] ?? '';
            $church_logo = $settings['church_logo'] ?? '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : $church_name; ?></title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/enhanced-style.css">
    <link rel="stylesheet" href="css/responsive-modern.css">
    <link rel="stylesheet" href="css/modern-animations.css">
    
    <style>
        /* Basic styles for the public site */
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
        }
        
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
        }
        
        .navbar-brand img {
            max-height: 50px;
            margin-right: 10px;
        }
        
        .navbar-brand .brand-text {
            display: flex;
            flex-direction: column;
        }
        
        .navbar-brand .church-name {
            font-weight: 700;
            color: #2e7d32;
            font-size: 1.2rem;
            line-height: 1.2;
        }
        
        .navbar-brand .church-tagline {
            font-size: 0.8rem;
            color: #666;
        }
        
        .nav-link {
            color: #333;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: color 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            color: #2e7d32;
        }
        
        .btn-login {
            background-color: #2e7d32;
            color: white;
            border-radius: 4px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn-login:hover {
            background-color: #1b5e20;
            color: white;
        }
        
        footer {
            background-color: #333;
            color: #fff;
            padding: 3rem 0;
        }
        
        footer h5 {
            color: #fff;
            margin-bottom: 1.5rem;
        }
        
        footer a {
            color: #ccc;
            transition: color 0.3s;
        }
        
        footer a:hover {
            color: #fff;
            text-decoration: none;
        }
        
        .footer-bottom {
            background-color: #222;
            padding: 1rem 0;
            color: #aaa;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="public_index.php">
                <?php if (!empty($church_logo)): ?>
                    <img src="<?php echo htmlspecialchars($church_logo); ?>" alt="<?php echo htmlspecialchars($church_name); ?> Logo">
                <?php else: ?>
                    <img src="img/church-logo.png" alt="Church Logo">
                <?php endif; ?>
                <div class="brand-text">
                    <span class="church-name"><?php echo htmlspecialchars($church_name); ?></span>
                    <?php if (!empty($church_tagline)): ?>
                        <span class="church-tagline"><?php echo htmlspecialchars($church_tagline); ?></span>
                    <?php endif; ?>
                </div>
            </a>
            
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'public_index.php' ? 'active' : ''; ?>" href="public_index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'about.php' ? 'active' : ''; ?>" href="about.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'services_public.php' ? 'active' : ''; ?>" href="services_public.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'events_public.php' ? 'active' : ''; ?>" href="events_public.php">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'ministries.php' ? 'active' : ''; ?>" href="ministries.php">Ministries</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>" href="contact.php">Contact</a>
                    </li>
                </ul>
                <a href="login.php" class="btn btn-login ml-3">Login</a>
            </div>
        </div>
    </nav>

