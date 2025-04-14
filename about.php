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
    <title>About Us - <?php echo $church_name; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/website-style.css">
    <link rel="stylesheet" href="css/responsive-modern.css">
    <link rel="stylesheet" href="css/modern-animations.css">
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
                    <li><a href="public_index.php">Home</a></li>
                    <li><a href="about.php" class="active">About Us</a></li>
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
                    <li><a href="public_index.php">Home</a></li>
                    <li><a href="about.php" class="active">About Us</a></li>
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

    <!-- Page Header -->
    <section class="page-header">
        <div class="container page-header-content">
            <h1 class="animate-on-scroll slide-in-down">About Us</h1>
            <p class="animate-on-scroll slide-in-up">Learn about our history, mission, and vision for the future</p>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-text animate-on-scroll slide-in-left">
                    <h2>Our Story</h2>
                    <p><?php echo $church_name; ?> was founded in 1985 with a small group of dedicated believers who shared a vision for a church that would serve the community and spread God's love. From humble beginnings in a small rented space, our church has grown into a vibrant community of faith.</p>
                    <p>Over the years, we have remained committed to our founding principles of worship, discipleship, fellowship, ministry, and evangelism. Our church has been blessed with growth, not just in numbers but in spiritual depth and community impact.</p>
                    <p>Today, <?php echo $church_name; ?> continues to be a place where people from all walks of life can come together to worship, learn, and serve. We are grateful for our history and excited about what God has in store for our future.</p>
                </div>
                <div class="about-image animate-on-scroll slide-in-right hover-zoom">
                    <img src="img/church-history.jpg">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="values-section">
        <div class="container">
            <div class="section-header">
                <h2 class="animate-on-scroll fade-in">Our Mission & Vision</h2>
                <p class="animate-on-scroll fade-in">Guiding principles that shape our church community</p>
            </div>
            <div class="grid grid-2">
                <div class="value-card animate-on-scroll slide-in-up hover-lift">
                    <div class="value-icon">
                        <i class="fas fa-compass"></i>
                    </div>
                    <h3>Our Mission</h3>
                    <p>To glorify God by making disciples who love God, grow in community, and serve the world. We are committed to sharing the gospel, nurturing believers, and demonstrating Christ's love through service to others.</p>
                </div>
                <div class="value-card animate-on-scroll slide-in-up hover-lift delay-2">
                    <div class="value-icon">
                        <i class="far fa-eye"></i>
                    </div>
                    <h3>Our Vision</h3>
                    <p>To be a church that transforms lives, strengthens families, and impacts our community with the love and message of Jesus Christ. We envision a growing community of believers who are passionate about God and compassionate toward people.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values Section -->
    <section class="about-section">
        <div class="container">
            <div class="section-header">
                <h2 class="animate-on-scroll fade-in">Our Core Values</h2>
                <p class="animate-on-scroll fade-in">The principles that guide our ministry and community</p>
            </div>
            <div class="grid grid-3">
                <div class="value-card animate-on-scroll slide-in-up hover-lift">
                    <div class="value-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3>Biblical Teaching</h3>
                    <p>We are committed to the authority of Scripture and teaching the whole counsel of God's Word in a way that is relevant and applicable to daily life.</p>
                </div>
                
                <div class="value-card animate-on-scroll slide-in-up hover-lift delay-2">
                    <div class="value-icon">
                        <i class="fas fa-pray"></i>
                    </div>
                    <h3>Authentic Worship</h3>
                    <p>We value heartfelt, Spirit-led worship that honors God and inspires people to live for Him throughout the week.</p>
                </div>
                
                <div class="value-card animate-on-scroll slide-in-up hover-lift delay-3">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Loving Community</h3>
                    <p>We foster genuine relationships where people can find acceptance, support, and spiritual growth through meaningful connections.</p>
                </div>
                
                <div class="value-card animate-on-scroll slide-in-up hover-lift delay-1">
                    <div class="value-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h3>Compassionate Service</h3>
                    <p>We demonstrate God's love by serving others, meeting practical needs, and being the hands and feet of Jesus in our community and beyond.</p>
                </div>
                
                <div class="value-card animate-on-scroll slide-in-up hover-lift delay-2">
                    <div class="value-icon">
                        <i class="fas fa-globe-americas"></i>
                    </div>
                    <h3>Global Mission</h3>
                    <p>We are committed to sharing the gospel locally and globally, supporting missionaries, and participating in God's work around the world.</p>
                </div>
                
                <div class="value-card animate-on-scroll slide-in-up hover-lift delay-3">
                    <div class="value-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h3>Spiritual Growth</h3>
                    <p>We encourage continuous spiritual development through discipleship, Bible study, prayer, and the application of God's Word in daily life.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Leadership Team Section -->
    <section class="team-section">
        <div class="container">
            <div class="section-header">
                <h2 class="animate-on-scroll fade-in">Our Leadership Team</h2>
                <p class="animate-on-scroll fade-in">Meet the dedicated individuals who guide our church</p>
            </div>
            <div class="grid grid-3">
                <div class="team-card animate-on-scroll slide-in-up hover-lift">
                    <div class="team-image hover-zoom">
                        <img src="img/pastor.jpg">
                    </div>
                    <div class="team-info">
                        <h3><?php echo $pastor_name; ?></h3>
                        <p><?php echo $pastor_title; ?></p>
                        <p>Leading our church with wisdom and compassion since 2008.</p>
                    </div>
                </div>
                
                <div class="team-card animate-on-scroll slide-in-up hover-lift delay-2">
                    <div class="team-image hover-zoom">
                        <img src="img/elder1.jpg">
                    </div>
                    <div class="team-info">
                        <h3>James Wilson</h3>
                        <p>Elder</p>
                        <p>Overseeing our discipleship and small group ministries.</p>
                    </div>
                </div>
                
                <div class="team-card animate-on-scroll slide-in-up hover-lift delay-3">
                    <div class="team-image hover-zoom">
                        <img src="img/elder2.jpg">
                    </div>
                    <div class="team-info">
                        <h3>Sarah Johnson</h3>
                        <p>Elder</p>
                        <p>Leading our worship and creative arts ministries.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content animate-on-scroll scale-in">
                <h2>Join Our Church Family</h2>
                <p>We'd love to welcome you to our community of faith. Come worship with us this Sunday!</p>
                <a href="contact.php" class="btn btn-secondary hover-lift">Get in Touch</a>
                <a href="services.php" class="btn btn-outline hover-lift">View Service Times</a>
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

