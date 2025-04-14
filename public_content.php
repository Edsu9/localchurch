<?php
// Start session only if one doesn't already exist
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'config/db.php';

// Get church settings
$query = "SELECT * FROM settings WHERE id = 1";
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);
$church_name = isset($settings['church_name']) ? $settings['church_name'] : 'JIA Somal-ot Church';
$church_tagline = isset($settings['tagline']) ? $settings['tagline'] : 'Transforming Lives Through Christ';
$church_logo = isset($settings['logo']) && $settings['logo'] ? 'uploads/logo/' . $settings['logo'] : 'img/jia.png';
$pastor_name = isset($settings['pastor_name']) ? $settings['pastor_name'] : 'Pastor John Doe';
$pastor_title = isset($settings['pastor_title']) ? $settings['pastor_title'] : 'Senior Pastor';

// Get website settings
$hero_title = isset($settings['hero_title']) ? $settings['hero_title'] : 'Welcome to ' . $church_name;
$hero_subtitle = isset($settings['hero_subtitle']) ? $settings['hero_subtitle'] : $church_tagline;
$hero_background = isset($settings['hero_background']) && $settings['hero_background'] ? 'uploads/website/' . $settings['hero_background'] : 'img/church-bg.jpg';
$welcome_title = isset($settings['welcome_title']) ? $settings['welcome_title'] : 'Welcome to Our Church';
$welcome_content = isset($settings['welcome_content']) ? $settings['welcome_content'] : 'At ' . $church_name . ', we believe in creating a community where people can experience God\'s love, grow in their faith, and find purpose in serving others.';
$ministries_title = isset($settings['ministries_title']) ? $settings['ministries_title'] : 'Our Ministries';
$ministries_subtitle = isset($settings['ministries_subtitle']) ? $settings['ministries_subtitle'] : 'Find your place to serve and grow';

// Social media links
$facebook_url = isset($settings['facebook_url']) ? $settings['facebook_url'] : '#';
$twitter_url = isset($settings['twitter_url']) ? $settings['twitter_url'] : '#';
$instagram_url = isset($settings['instagram_url']) ? $settings['instagram_url'] : '#';
$youtube_url = isset($settings['youtube_url']) ? $settings['youtube_url'] : '#';

// Service times
$sunday_morning_time = isset($settings['sunday_morning_time']) ? $settings['sunday_morning_time'] : '9:00 AM - 10:30 AM';
$sunday_morning_desc = isset($settings['sunday_morning_desc']) ? $settings['sunday_morning_desc'] : 'Contemporary Worship';
$sunday_afternoon_time = isset($settings['sunday_afternoon_time']) ? $settings['sunday_afternoon_time'] : '11:00 AM - 12:30 PM';
$sunday_afternoon_desc = isset($settings['sunday_afternoon_desc']) ? $settings['sunday_afternoon_desc'] : 'Family Service';
$midweek_time = isset($settings['midweek_time']) ? $settings['midweek_time'] : '7:00 PM - 8:30 PM';
$midweek_desc = isset($settings['midweek_desc']) ? $settings['midweek_desc'] : 'Bible Study & Prayer';

// Get upcoming events
$query = "SELECT id, event_name, event_date, event_time, location, description, status 
        FROM events 
        WHERE event_date >= CURDATE() AND status != 'Cancelled'
        ORDER BY event_date ASC LIMIT 3";
$upcoming_events = mysqli_query($conn, $query);

// Get ministries (you may need to create this table)
$ministry_names = ['Youth Ministry', 'Women\'s Ministry', 'Men\'s Ministry', 'Children\'s Ministry', 'Worship Ministry', 'Outreach Ministry'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($settings['meta_title']) ? $settings['meta_title'] : $church_name; ?> - Home</title>
  <meta name="description" content="<?php echo isset($settings['meta_description']) ? $settings['meta_description'] : 'Welcome to ' . $church_name . '. ' . $church_tagline; ?>">
  <meta name="keywords" content="<?php echo isset($settings['meta_keywords']) ? $settings['meta_keywords'] : 'church, worship, faith, community, ' . $church_name; ?>">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* Basic styles to ensure content displays */
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      line-height: 1.6;
      color: #333;
      background-color: #fff;
    }
    
    .container {
      width: 90%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 15px;
    }
    
    /* Header styles */
    .site-header {
      background-color: #fff;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 15px 0;
      position: relative;
      z-index: 100;
    }
    
    .header-inner {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .logo {
      display: flex;
      align-items: center;
      text-decoration: none;
    }
    
    .logo img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      margin-right: 15px;
      object-fit: cover;
    }
    
    .logo-text h1 {
      color: #2e7d32;
      margin: 0;
      font-size: 24px;
      font-weight: 700;
    }
    
    .logo-text p {
      margin: 0;
      font-size: 14px;
      color: #666;
    }
    
    .main-nav ul {
      display: flex;
      list-style: none;
      margin: 0;
      padding: 0;
    }
    
    .main-nav li {
      margin-left: 25px;
    }
    
    .main-nav a {
      color: #333;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s;
    }
    
    .main-nav a:hover, .main-nav a.active {
      color: #2e7d32;
    }
    
    .main-nav a.active {
      position: relative;
    }
    
    .main-nav a.active::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 100%;
      height: 2px;
      background-color: #2e7d32;
    }
    
    .btn-login {
      background-color: #2e7d32;
      color: white !important;
      padding: 8px 16px;
      border-radius: 4px;
      text-decoration: none;
    }
    
    .btn-login:hover {
      background-color: #1b5e20;
    }
    
    /* Hero section */
    .hero {
      background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('img/church-bg.jpg');
      background-size: cover;
      background-position: center;
      color: white;
      text-align: center;
      padding: 100px 0;
      margin-bottom: 60px;
    }
    
    .hero h1 {
      font-size: 48px;
      margin-bottom: 20px;
      font-family: 'Playfair Display', serif;
    }
    
    .hero p {
      font-size: 20px;
      margin-bottom: 30px;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .hero-buttons {
      margin-top: 30px;
    }
    
    .btn {
      display: inline-block;
      padding: 12px 24px;
      border-radius: 4px;
      text-decoration: none;
      font-weight: 600;
      margin: 0 10px;
      transition: all 0.3s;
    }
    
    .btn-primary {
      background-color: #2e7d32;
      color: white;
    }
    
    .btn-primary:hover {
      background-color: #1b5e20;
    }
    
    .btn-secondary {
      background-color: #f9a825;
      color: #333;
    }
    
    .btn-secondary:hover {
      background-color: #f57f17;
    }
    
    /* Section styles */
    section {
      padding: 60px 0;
    }
    
    .section-header {
      text-align: center;
      margin-bottom: 50px;
    }
    
    .section-header h2 {
      font-size: 36px;
      margin-bottom: 15px;
      font-family: 'Playfair Display', serif;
      color: #333;
    }
    
    .section-header p {
      font-size: 18px;
      color: #666;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }
    
    /* Welcome section */
    .welcome-content {
      display: flex;
      align-items: center;
      gap: 40px;
    }
    
    .welcome-text, .welcome-image {
      flex: 1;
    }
    
    .welcome-text h3 {
      font-size: 28px;
      margin-bottom: 20px;
      color: #2e7d32;
      font-family: 'Playfair Display', serif;
    }
    
    .welcome-image img {
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      width: 100%;
      height: auto;
      max-height: 400px;
      object-fit: cover;
    }
    
    /* Service times */
    .service-times {
      background-color: #2e7d32;
      color: white;
    }
    
    .service-times-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
      margin-bottom: 40px;
    }
    
    .service-time-card {
      background-color: rgba(255,255,255,0.1);
      padding: 30px;
      border-radius: 8px;
      text-align: center;
    }
    
    .service-icon {
      font-size: 36px;
      margin-bottom: 20px;
    }
    
    /* Events section */
    .events-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
      margin-bottom: 40px;
    }
    
    .event-card {
      background-color: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    
    .event-date {
      background-color: #2e7d32;
      color: white;
      text-align: center;
      padding: 15px;
    }
    
    .event-date .day {
      font-size: 28px;
      font-weight: 700;
      display: block;
    }
    
    .event-details {
      padding: 20px;
    }
    
    /* Footer */
    .site-footer {
      background-color: #222;
      color: white;
      padding: 60px 0 20px;
    }
    
    .footer-content {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 40px;
      margin-bottom: 40px;
    }
    
    .footer-bottom {
      text-align: center;
      padding-top: 20px;
      border-top: 1px solid rgba(255,255,255,0.1);
    }
    
    /* Mobile menu */
    .mobile-menu-toggle {
      display: none;
      flex-direction: column;
      cursor: pointer;
    }
    
    .mobile-menu-toggle span {
      width: 25px;
      height: 3px;
      background-color: #333;
      margin: 3px 0;
      border-radius: 3px;
    }
    
    @media (max-width: 768px) {
      .main-nav {
        display: none;
      }
      
      .mobile-menu-toggle {
        display: flex;
      }
      
      .welcome-content {
        flex-direction: column;
      }
      
      .welcome-image {
        order: 1;
        margin-bottom: 30px;
      }
      
      .welcome-text {
        order: 2;
      }
      
      .hero h1 {
        font-size: 36px;
      }
      
      .hero p {
        font-size: 18px;
      }
      
      .btn {
        display: block;
        width: 80%;
        margin: 10px auto;
      }
    }
    
    /* Mobile menu overlay */
    .mobile-menu-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.9);
      z-index: 1000;
      display: none;
      overflow-y: auto;
    }
    
    .close-mobile-menu {
      position: absolute;
      top: 20px;
      right: 20px;
      color: white;
      font-size: 24px;
      cursor: pointer;
    }
    
    .mobile-menu-container {
      padding: 60px 20px 40px;
      text-align: center;
    }
    
    .mobile-logo {
      margin-bottom: 30px;
    }
    
    .mobile-logo img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      margin-bottom: 10px;
    }
    
    .mobile-logo h2 {
      color: white;
      font-family: 'Playfair Display', serif;
    }
    
    .mobile-nav-menu {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .mobile-nav-menu li {
      margin-bottom: 15px;
    }
    
    .mobile-nav-menu a {
      color: white;
      text-decoration: none;
      font-size: 18px;
      font-weight: 500;
    }
    
    .mobile-nav-menu a:hover, .mobile-nav-menu a.active {
      color: #4caf50;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="site-header">
      <div class="container">
          <div class="header-inner">
              <a href="index.php" class="logo">
                  <img src="<?php echo $church_logo; ?>" alt="<?php echo $church_name; ?> Logo">
                  <div class="logo-text">
                      <h1><?php echo $church_name; ?></h1>
                      <p><?php echo $church_tagline; ?></p>
                  </div>
              </a>
              <nav class="main-nav">
                  <ul>
                      <li><a href="index.php" class="active">Home</a></li>
                      <li><a href="about.php">About Us</a></li>
                      <li><a href="services.php">Services</a></li>
                      <li><a href="events_public.php">Events</a></li>
                      <li><a href="ministries.php">Ministries</a></li>
                      <li><a href="contact.php">Contact</a></li>
                      <li><a href="login.php" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                  </ul>
              </nav>
              <div class="mobile-menu-toggle">
                  <span></span>
                  <span></span>
                  <span></span>
              </div>
          </div>
      </div>
  </header>

  <!-- Hero Section -->
  <section class="hero" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('<?php echo $hero_background; ?>');">
    <div class="container">
        <h1><?php echo $hero_title; ?></h1>
        <p><?php echo $hero_subtitle; ?></p>
        <div class="hero-buttons">
            <a href="services.php" class="btn btn-primary">Join Us Sunday</a>
            <a href="about.php" class="btn btn-secondary">Learn More</a>
        </div>
    </div>
</section>

  <!-- Welcome Section -->
  <section class="welcome-section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo $welcome_title; ?></h2>
            <p>A place where everyone is welcome to worship and grow in faith</p>
        </div>
        <div class="welcome-content">
            <div class="welcome-text">
                <h3>We're Glad You're Here</h3>
                <p><?php echo $welcome_content; ?></p>
                <p>Our services are designed to be relevant, engaging, and transformative. We offer a variety of ministries for all ages and stages of life.</p>
                <a href="about.php" class="btn btn-primary">About Us</a>
            </div>
            <div class="welcome-image">
                <img src="img/church-welcome.jpg" alt="Welcome to our church">
            </div>
        </div>
    </div>
</section>

  <!-- Service Times Section -->
  <section class="service-times">
    <div class="container">
        <div class="section-header">
            <h2>Join Us This Sunday</h2>
            <p>We'd love to see you at one of our services</p>
        </div>
        <div class="service-times-grid">
            <div class="service-time-card">
                <div class="service-icon">
                    <i class="fas fa-sun"></i>
                </div>
                <h3>Sunday Morning</h3>
                <p class="time"><?php echo $sunday_morning_time; ?></p>
                <p><?php echo $sunday_morning_desc; ?></p>
            </div>
            <div class="service-time-card">
                <div class="service-icon">
                    <i class="fas fa-cloud-sun"></i>
                </div>
                <h3>Sunday Afternoon</h3>
                <p class="time"><?php echo $sunday_afternoon_time; ?></p>
                <p><?php echo $sunday_afternoon_desc; ?></p>
            </div>
            <div class="service-time-card">
                <div class="service-icon">
                    <i class="fas fa-moon"></i>
                </div>
                <h3>Wednesday Night</h3>
                <p class="time"><?php echo $midweek_time; ?></p>
                <p><?php echo $midweek_desc; ?></p>
            </div>
        </div>
        <div class="service-cta" style="text-align: center; margin-top: 30px;">
            <a href="services.php" class="btn btn-primary" style="background-color: white; color: #2e7d32;">View All Services</a>
        </div>
    </div>
</section>

  <!-- Upcoming Events Section -->
  <section class="events-section">
      <div class="container">
          <div class="section-header">
              <h2>Upcoming Events</h2>
              <p>Join us for these special events and activities</p>
          </div>
          <div class="events-grid">
              <?php 
              if (mysqli_num_rows($upcoming_events) > 0) {
                  while($event = mysqli_fetch_assoc($upcoming_events)): 
                      $event_date = new DateTime($event['event_date']);
              ?>
              <div class="event-card">
                  <div class="event-date">
                      <span class="day"><?php echo $event_date->format('d'); ?></span>
                      <span class="month"><?php echo $event_date->format('M'); ?></span>
                  </div>
                  <div class="event-details">
                      <h3><?php echo $event['event_name']; ?></h3>
                      <p class="event-time"><i class="far fa-clock"></i> <?php echo date('g:i A', strtotime($event['event_time'])); ?></p>
                      <p class="event-location"><i class="fas fa-map-marker-alt"></i> <?php echo $event['location']; ?></p>
                      <p class="event-description"><?php echo substr($event['description'] ?? 'Join us for this special event.', 0, 100) . '...'; ?></p>
                      <a href="event_details.php?id=<?php echo $event['id']; ?>" class="btn btn-primary" style="display: inline-block; padding: 8px 16px; font-size: 14px; margin-top: 15px;">Learn More</a>
                  </div>
              </div>
              <?php 
                  endwhile;
              } else {
              ?>
              <div style="grid-column: 1/-1; text-align: center; padding: 40px; background-color: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                  <p>No upcoming events at this time. Please check back soon!</p>
              </div>
              <?php } ?>
          </div>
          <div style="text-align: center; margin-top: 30px;">
              <a href="events_public.php" class="btn btn-primary">View All Events</a>
          </div>
      </div>
  </section>

  <!-- Ministries Section -->
  <section class="ministries-section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo $ministries_title; ?></h2>
            <p><?php echo $ministries_subtitle; ?></p>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 40px;">
            <?php foreach($ministry_names as $index => $ministry): ?>
            <div style="background-color: #f5f5f5; padding: 30px; border-radius: 8px; text-align: center; transition: transform 0.3s, box-shadow 0.3s;">
                <div style="font-size: 36px; color: #2e7d32; margin-bottom: 20px;">
                    <i class="<?php 
                        $icons = ['fas fa-users', 'fas fa-female', 'fas fa-male', 'fas fa-child', 'fas fa-music', 'fas fa-hands-helping'];
                        echo $icons[$index % count($icons)];
                    ?>"></i>
                </div>
                <h3 style="font-size: 20px; margin-bottom: 15px;"><?php echo $ministry; ?></h3>
                <p style="margin-bottom: 20px; color: #666;">Our <?php echo strtolower($ministry); ?> provides opportunities for growth, fellowship, and service.</p>
                <a href="ministry_details.php?name=<?php echo urlencode($ministry); ?>" class="btn btn-primary" style="display: inline-block; padding: 8px 16px; font-size: 14px;">Learn More</a>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center;">
            <a href="ministries.php" class="btn btn-primary">View All Ministries</a>
        </div>
    </div>
</section>

  <!-- Footer -->
  <footer class="site-footer">
      <div class="container">
          <div class="footer-content">
              <div>
                  <img src="<?php echo $church_logo; ?>" alt="<?php echo $church_name; ?> Logo" style="width: 60px; height: 60px; border-radius: 50%; margin-bottom: 15px;">
                  <h3 style="font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 10px;"><?php echo $church_name; ?></h3>
                  <p style="font-size: 14px; opacity: 0.8; margin-bottom: 20px;"><?php echo $church_tagline; ?></p>
              </div>
              <div>
                  <h4 style="font-size: 18px; margin-bottom: 20px; position: relative; padding-bottom: 10px;">Quick Links</h4>
                  <ul style="list-style: none; padding: 0;">
                      <li style="margin-bottom: 10px;"><a href="index.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
                      <li style="margin-bottom: 10px;"><a href="about.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">About Us</a></li>
                      <li style="margin-bottom: 10px;"><a href="services.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Services</a></li>
                      <li style="margin-bottom: 10px;"><a href="events_public.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Events</a></li>
                      <li style="margin-bottom: 10px;"><a href="ministries.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Ministries</a></li>
                      <li style="margin-bottom: 10px;"><a href="contact.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Contact</a></li>
                  </ul>
              </div>
              <div>
                  <h4 style="font-size: 18px; margin-bottom: 20px; position: relative; padding-bottom: 10px;">Contact Us</h4>
                  <p style="margin-bottom: 15px;"><i class="fas fa-map-marker-alt" style="margin-right: 10px; color: #2e7d32;"></i> <?php echo isset($settings['address']) ? $settings['address'] : '123 Church Street, City'; ?></p>
                  <p style="margin-bottom: 15px;"><i class="fas fa-phone" style="margin-right: 10px; color: #2e7d32;"></i> <?php echo isset($settings['phone']) ? $settings['phone'] : '(123) 456-7890'; ?></p>
                  <p style="margin-bottom: 15px;"><i class="fas fa-envelope" style="margin-right: 10px; color: #2e7d32;"></i> <?php echo isset($settings['email']) ? $settings['email'] : 'info@jiachurch.com'; ?></p>
              </div>
              <div>
                  <h4 style="font-size: 18px; margin-bottom: 20px; position: relative; padding-bottom: 10px;">Connect With Us</h4>
                  <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                      <a href="<?php echo $facebook_url; ?>" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: rgba(255,255,255,0.1); color: white; border-radius: 50%; text-decoration: none;"><i class="fab fa-facebook-f"></i></a>
                      <a href="<?php echo $twitter_url; ?>" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: rgba(255,255,255,0.1); color: white; border-radius: 50%; text-decoration: none;"><i class="fab fa-twitter"></i></a>
                      <a href="<?php echo $instagram_url; ?>" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: rgba(255,255,255,0.1); color: white; border-radius: 50%; text-decoration: none;"><i class="fab fa-instagram"></i></a>
                      <a href="<?php echo $youtube_url; ?>" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: rgba(255,255,255,0.1); color: white; border-radius: 50%; text-decoration: none;"><i class="fab fa-youtube"></i></a>
                  </div>
              </div>
          </div>
          <div class="footer-bottom">
              <p style="font-size: 14px; opacity: 0.8; margin-bottom: 10px;">&copy; <?php echo date('Y'); ?> <?php echo $church_name; ?>. All Rights Reserved.</p>
              <p style="font-size: 14px; opacity: 0.8; margin-bottom: 10px;">Designed with <i class="fas fa-heart" style="color: #e25555;"></i> for the glory of God</p>
          </div>
      </div>
  </footer>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
      // Mobile menu toggle
      const menuToggle = document.querySelector('.mobile-menu-toggle');
      const mobileMenu = document.createElement('div');
      mobileMenu.className = 'mobile-menu-overlay';
      mobileMenu.innerHTML = `
          <div class="close-mobile-menu">
              <i class="fas fa-times"></i>
          </div>
          <div class="mobile-menu-container">
              <div class="mobile-logo">
                  <img src="${document.querySelector('.logo img').src}" alt="Logo">
                  <h2>${document.querySelector('.logo-text h1').textContent}</h2>
              </div>
              <nav class="mobile-nav">
                  <ul class="mobile-nav-menu">
                      <li><a href="index.php" class="active">Home</a></li>
                      <li><a href="about.php">About Us</a></li>
                      <li><a href="services.php">Services</a></li>
                      <li><a href="events_public.php">Events</a></li>
                      <li><a href="ministries.php">Ministries</a></li>
                      <li><a href="contact.php">Contact</a></li>
                      <li><a href="login.php" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                  </ul>
              </nav>
          </div>
      `;
      
      document.body.appendChild(mobileMenu);
      
      const closeMenu = document.querySelector('.close-mobile-menu');
      
      menuToggle.addEventListener('click', function() {
          mobileMenu.style.display = 'block';
          document.body.style.overflow = 'hidden';
      });
      
      closeMenu.addEventListener('click', function() {
          mobileMenu.style.display = 'none';
          document.body.style.overflow = 'auto';
      });
  });
  </script>
</body>
</html>

