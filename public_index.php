<?php
session_start();
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $church_name; ?> - Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/website-style.css">
    <link rel="stylesheet" href="css/responsive-modern.css">
    <link rel="stylesheet" href="css/modern-animations.css">
    <style>
        /* Direct inline styles to ensure visibility */
        #service-times-section {
            position: relative;
            z-index: 1000 !important;
            padding: 80px 0;
            background-color: #2e7d32 !important;
            color: white !important;
            margin: 40px 0;
        }
        
        #service-times-section::before {
            content: none !important;
        }
        
        #service-times-section .section-header h2,
        #service-times-section .section-header p {
            color: white !important;
        }
        
        #service-times-container {
            background-color: rgba(0, 0, 0, 0.3);
            padding: 30px;
            border-radius: 10px;
            margin-top: 30px;
        }
        
        #service-times-container h3 {
            color: white !important;
            font-size: 28px;
            margin-bottom: 15px;
        }
        
        #service-times-container > p {
            color: white !important;
            font-size: 18px;
            margin-bottom: 25px;
        }
        
        #service-times-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .service-box {
            flex: 1;
            min-width: 250px;
            background-color: white !important;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .service-box-icon i {
            color: #2e7d32 !important;
            font-size: 28px;
            margin-bottom: 15px;
        }
        
        .service-box h4 {
            color: #2e7d32 !important;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 12px;
        }
        
        .service-box p {
            color: #333 !important;
            font-size: 16px;
            margin-bottom: 8px;
        }
        
        #service-times-cta {
            margin-top: 30px;
            text-align: center;
        }
        
        #service-times-cta a {
            display: inline-block;
            background-color: white;
            color: #2e7d32 !important;
            padding: 12px 30px;
            border-radius: 4px;
            font-weight: bold;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        #service-times-cta a:hover {
            background-color: #f5f5f5;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 768px) {
            .service-box {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="container header-inner">
            <a href="public_index.php" class="logo">
                <img src="<?php echo $church_logo; ?>" alt="<?php echo $church_name; ?> Logo">
                <div class="logo-text">
                    <h1>JIA Somal-ot Community</h1>
                </div>
            </a>
            <nav class="main-nav">
                <ul>
                    <li><a href="public_index.php" class="active">Home</a></li>
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
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay">
        <div class="close-mobile-menu">
            <i class="fas fa-times"></i>
        </div>
        <div class="mobile-menu-container">
            <div class="mobile-logo">
                <img src="<?php echo $church_logo; ?>" alt="<?php echo $church_name; ?> Logo">
                <h2><?php echo $church_name; ?></h2>
            </div>
            <nav class="mobile-nav">
                <ul class="mobile-nav-menu">
                    <li><a href="public_index.php" class="active">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="services.php">Services</a></li>
                    <li><a href="events_public.php">Events</a></li>
                    <li><a href="ministries.php">Ministries</a></li>
                    <li><a href="contact.php">Contact</a></li>
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="parallax-bg" data-speed="0.3"></div>
        <div class="hero-content">
            <h1 class="animate-on-scroll slide-in-down">Welcome to Jesus Is Alive Community<?php echo $church_name; ?></h1>
            <p class="animate-on-scroll slide-in-up"><?php echo $church_tagline; ?></p>
            <div class="hero-buttons" style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
                <a href="about.php" class="btn btn-primary" style="display: inline-block; padding: 12px 30px; background-color: #2e7d32; color: white; text-decoration: none; border-radius: 4px; font-weight: 600; transition: all 0.3s ease; text-align: center; min-width: 150px;">Learn More</a>
                <a href="contact.php" class="btn btn-secondary" style="display: inline-block; padding: 12px 30px; background-color: transparent; color: white; text-decoration: none; border-radius: 4px; border: 2px solid white; font-weight: 600; transition: all 0.3s ease; text-align: center; min-width: 150px;">Contact Us</a>
            </div>
        </div>
    </section>

    <!-- Welcome Section -->
    <section class="welcome-section">
        <div class="container">
            <div class="section-header">
                <h2 class="animate-on-scroll fade-in">Welcome to Jesus Is Alive Community</h2>
                <p class="animate-on-scroll fade-in">We're glad you're here. Join us as we grow together in faith and community.</p>
            </div>
            <div class="about-content">
                <div class="about-text animate-on-scroll slide-in-left">
                    <h3>A Place to Belong</h3>
                    <p>At <?php echo $church_name; ?>, we believe that everyone deserves a place where they can feel at home, find purpose, and grow in their relationship with God. Whether you're exploring faith for the first time or have been a believer for years, you'll find a welcoming community here.</p>
                    <p>Our church is built on the foundation of God's love and the teachings of Jesus Christ. We strive to create an environment where people can worship authentically, connect meaningfully, and serve passionately.</p>
                    <a href="about.php" class="btn btn-primary hover-lift">About Us</a>
                </div>
                <div class="about-image animate-on-scroll slide-in-right hover-zoom">
                    <img src="img/church-welcome.jpg" alt="Church Welcome">
                </div>
            </div>
        </div>
    </section>

    <!-- NEW Service Times Section with direct styling -->
    <section id="service-times-section" style="background-color: #2e7d32 !important; color: white !important; padding: 80px 0; position: relative; z-index: 1000;">
        <div class="container" style="position: relative; z-index: 1001;">
            <div class="section-header" style="text-align: center; margin-bottom: 40px;">
                <h2 style="color: white !important; font-size: 2.5rem; margin-bottom: 15px;">Service Times</h2>
                <p style="color: white !important; font-size: 1.125rem;">Join us for worship and fellowship</p>
            </div>
            
            <div id="service-times-container" style="background-color: rgba(0, 0, 0, 0.3); padding: 30px; border-radius: 10px; margin-top: 30px;">
                <h3 style="color: white !important; font-size: 28px; margin-bottom: 15px;">Weekly Service Schedule</h3>
                <p style="color: white !important; font-size: 18px; margin-bottom: 25px;">We invite you to join us for any of our regular worship services. All are welcome!</p>
                
                <div id="service-times-list" style="display: flex; flex-wrap: wrap; gap: 20px;">
                    <?php
                    // Fetch active services from database
                    $query = "SELECT * FROM services WHERE is_active = 1 ORDER BY FIELD(service_day, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), service_time LIMIT 3";
                    $result = mysqli_query($conn, $query);
                    
                    if (mysqli_num_rows($result) > 0) {
                        while ($service = mysqli_fetch_assoc($result)) {
                    ?>
                        <div class="service-box" style="flex: 1; min-width: 250px; background-color: white !important; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                            <div class="service-box-icon" style="margin-bottom: 15px;">
                                <i class="fas fa-church" style="color: #2e7d32 !important; font-size: 28px;"></i>
                            </div>
                            <h4 style="color: #2e7d32 !important; font-size: 22px; font-weight: bold; margin-bottom: 12px;"><?php echo htmlspecialchars($service['service_title']); ?></h4>
                            <p style="color: #333 !important; font-size: 16px; margin-bottom: 8px;"><?php echo htmlspecialchars($service['service_day']); ?>, <?php echo htmlspecialchars($service['service_time']); ?></p>
                            <?php if (!empty($service['service_location'])): ?>
                                <p style="color: #333 !important; font-size: 16px; margin-bottom: 8px;"><i class="fas fa-map-marker-alt" style="color: #2e7d32 !important; margin-right: 5px;"></i> <?php echo htmlspecialchars($service['service_location']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php
                        }
                    } else {
                    ?>
                        <div class="service-box" style="flex: 1; min-width: 250px; background-color: white !important; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                            <div class="service-box-icon" style="margin-bottom: 15px;">
                                <i class="fas fa-church" style="color: #2e7d32 !important; font-size: 28px;"></i>
                            </div>
                            <h4 style="color: #2e7d32 !important; font-size: 22px; font-weight: bold; margin-bottom: 12px;">Sunday Morning</h4>
                            <p style="color: #333 !important; font-size: 16px; margin-bottom: 8px;">Sunday, 9:00 AM - 10:30 AM</p>
                            <p style="color: #333 !important; font-size: 16px; margin-bottom: 8px;"><i class="fas fa-map-marker-alt" style="color: #2e7d32 !important; margin-right: 5px;"></i> Main Sanctuary</p>
                        </div>
                        
                        <div class="service-box" style="flex: 1; min-width: 250px; background-color: white !important; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                            <div class="service-box-icon" style="margin-bottom: 15px;">
                                <i class="fas fa-moon" style="color: #2e7d32 !important; font-size: 28px;"></i>
                            </div>
                            <h4 style="color: #2e7d32 !important; font-size: 22px; font-weight: bold; margin-bottom: 12px;">Sunday Evening</h4>
                            <p style="color: #333 !important; font-size: 16px; margin-bottom: 8px;">Sunday, 6:00 PM - 7:30 PM</p>
                            <p style="color: #333 !important; font-size: 16px; margin-bottom: 8px;"><i class="fas fa-map-marker-alt" style="color: #2e7d32 !important; margin-right: 5px;"></i> Main Sanctuary</p>
                        </div>
                        
                        <div class="service-box" style="flex: 1; min-width: 250px; background-color: white !important; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                            <div class="service-box-icon" style="margin-bottom: 15px;">
                                <i class="fas fa-book-open" style="color: #2e7d32 !important; font-size: 28px;"></i>
                            </div>
                            <h4 style="color: #2e7d32 !important; font-size: 22px; font-weight: bold; margin-bottom: 12px;">Wednesday Bible Study</h4>
                            <p style="color: #333 !important; font-size: 16px; margin-bottom: 8px;">Wednesday, 6:30 PM - 8:00 PM</p>
                            <p style="color: #333 !important; font-size: 16px; margin-bottom: 8px;"><i class="fas fa-map-marker-alt" style="color: #2e7d32 !important; margin-right: 5px;"></i> Fellowship Hall</p>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                
                <div id="service-times-cta" style="margin-top: 30px; text-align: center;">
                    <a href="services.php" style="display: inline-block; background-color: white; color: #2e7d32 !important; padding: 12px 30px; border-radius: 4px; font-weight: bold; text-decoration: none; font-size: 16px;">View All Services</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Ministries Section -->
    <section class="ministries-section">
        <div class="container">
            <div class="section-header">
                <h2 class="animate-on-scroll fade-in">Our Ministries</h2>
                <p class="animate-on-scroll fade-in">Serving God and our community in various ways</p>
            </div>
            <div class="grid grid-4">
                <div class="value-card animate-on-scroll slide-in-up hover-lift">
                    <div class="value-icon">
                        <i class="fas fa-child"></i>
                    </div>
                    <h3>Children's Ministry</h3>
                    <p>Nurturing the faith of our youngest members through age-appropriate teaching and activities.</p>
                    <a href="ministries.php" class="btn-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <div class="value-card animate-on-scroll slide-in-up hover-lift" style="animation-delay: 0.2s;">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Youth Ministry</h3>
                    <p>Empowering teenagers and young adults to grow in their faith and develop leadership skills.</p>
                    <a href="ministries.php" class="btn-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <div class="value-card animate-on-scroll slide-in-up hover-lift" style="animation-delay: 0.4s;">
                    <div class="value-icon">
                        <i class="fas fa-music"></i>
                    </div>
                    <h3>Worship Ministry</h3>
                    <p>Leading the congregation in worship through music, multimedia, and creative arts.</p>
                    <a href="ministries.php" class="btn-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <div class="value-card animate-on-scroll slide-in-up hover-lift" style="animation-delay: 0.6s;">
                    <div class="value-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h3>Outreach Ministry</h3>
                    <p>Serving our community and sharing God's love through practical assistance and support.</p>
                    <a href="ministries.php" class="btn-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="ministries-cta text-center animate-on-scroll fade-in">
                <a href="ministries.php" class="btn btn-primary hover-lift">View All Ministries</a>
            </div>
        </div>
    </section>

    <!-- Pastor Section -->
    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-image animate-on-scroll slide-in-left hover-zoom">
                    <img src="img/pastor.jpg" alt="<?php echo $pastor_name; ?>">
                </div>
                <div class="about-text animate-on-scroll slide-in-right">
                    <h2>Meet Our Pastor</h2>
                    <h3><?php echo $pastor_name; ?></h3>
                    <p class="pastor-title"><?php echo $pastor_title; ?></p>
                    <p>Pastor John has been leading our church with wisdom, compassion, and a deep love for God's Word since 2008. With over 20 years of ministry experience, he is dedicated to helping people grow in their relationship with Jesus Christ and find their purpose in God's plan.</p>
                    <p>His teaching style is engaging, practical, and firmly rooted in Scripture. Pastor John and his wife, Sarah, have three children and are passionate about strengthening families and building a church that makes a difference in our community.</p>
                    <a href="about.php" class="btn btn-primary hover-lift">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="<?php echo $church_logo; ?>" alt="<?php echo $church_name; ?> Logo">
                    <h3><?php echo $church_name; ?></h3>
                    <p>A place of worship, community, and spiritual growth.</p>
                    <div class="footer-social">
                        <a href="#" class="hover-lift"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="hover-lift"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover-lift"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="hover-lift"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="footer-links">
                    <h4 class="footer-heading">Quick Links</h4>
                    <ul>
                        <li><a href="public_index.php">Home</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="events_public.php">Events</a></li>
                        <li><a href="ministries.php">Ministries</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4 class="footer-heading">Ministries</h4>
                    <ul>
                        <li><a href="ministries.php">Children's Ministry</a></li>
                        <li><a href="ministries.php">Youth Ministry</a></li>
                        <li><a href="ministries.php">Worship Ministry</a></li>
                        <li><a href="ministries.php">Outreach Ministry</a></li>
                        <li><a href="ministries.php">Prayer Ministry</a></li>
                    </ul>
                </div>
                
                <div class="footer-contact">
                    <h4 class="footer-heading">Contact Us</h4>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo isset($settings['address']) ? $settings['address'] : '123 Church Street, City, State 12345'; ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo isset($settings['phone']) ? $settings['phone'] : '(123) 456-7890'; ?></p>
                    <p><i class="fas fa-envelope"></i> <?php echo isset($settings['email']) ? $settings['email'] : 'info@jiasomalotchurch.com'; ?></p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo $church_name; ?>. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="js/website.js"></script>
    <script src="js/responsive.js"></script>
</body>
</html>

