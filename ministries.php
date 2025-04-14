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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ministries - <?php echo $church_name; ?></title>
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
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="services.php">Services</a></li>
                    <li><a href="events_public.php">Events</a></li>
                    <li><a href="ministries.php" class="active">Ministries</a></li>
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
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="services.php">Services</a></li>
                    <li><a href="events_public.php">Events</a></li>
                    <li><a href="ministries.php" class="active">Ministries</a></li>
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
            <h1 class="animate-on-scroll slide-in-down">Our Ministries</h1>
            <p class="animate-on-scroll slide-in-up">Serving God and our community through various ministries</p>
        </div>
    </section>

    <!-- Ministries Overview Section -->
    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-text animate-on-scroll slide-in-left">
                    <h2>Ministry Overview</h2>
                    <p>At <?php echo $church_name; ?>, we believe that every member has unique gifts and talents that can be used to serve God and others. Our ministries provide opportunities for spiritual growth, fellowship, and service.</p>
                    <p>Whether you're passionate about worship, teaching, serving the community, or supporting missions, there's a place for you to get involved and make a difference.</p>
                    <p>We encourage all members to discover their spiritual gifts and find a ministry where they can serve and grow. Not sure where to start? Contact us, and we'll help you find the right fit!</p>
                    <a href="contact.php" class="btn btn-primary hover-lift">Get Involved</a>
                </div>
                <div class="about-image animate-on-scroll slide-in-right hover-zoom">
                    <img src="img/ministry-overview.jpg" alt="Ministry Overview">
                </div>
            </div>
        </div>
    </section>

    <!-- Main Ministries Section -->
    <section class="values-section">
        <div class="container">
            <div class="section-header">
                <h2 class="animate-on-scroll fade-in">Our Main Ministries</h2>
                <p class="animate-on-scroll fade-in">Explore the various ways we serve and grow together</p>
            </div>
            
            <div class="grid grid-3">
                <div class="ministry-card animate-on-scroll slide-in-up hover-lift">
                    <div class="ministry-icon">
                        <i class="fas fa-child"></i>
                    </div>
                    <h3>Children's Ministry</h3>
                    <p>Our children's ministry provides age-appropriate teaching and activities that help children learn about God's love and develop a strong foundation of faith.</p>
                    <ul class="ministry-details">
                        <li>Sunday School (Ages 3-12)</li>
                        <li>Children's Church</li>
                        <li>Vacation Bible School</li>
                        <li>Children's Choir</li>
                    </ul>
                    <a href="#" class="btn-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <div class="ministry-card animate-on-scroll slide-in-up hover-lift delay-2">
                    <div class="ministry-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Youth Ministry</h3>
                    <p>Our youth ministry provides a safe and fun environment where teenagers can grow in their faith, build meaningful relationships, and develop leadership skills.</p>
                    <ul class="ministry-details">
                        <li>Weekly Youth Group (Ages 13-18)</li>
                        <li>Bible Study</li>
                        <li>Retreats and Camps</li>
                        <li>Service Projects</li>
                    </ul>
                    <a href="#" class="btn-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <div class="ministry-card animate-on-scroll slide-in-up hover-lift delay-3">
                    <div class="ministry-icon">
                        <i class="fas fa-music"></i>
                    </div>
                    <h3>Worship Ministry</h3>
                    <p>Our worship ministry leads the congregation in praising God through music and creates an atmosphere where people can encounter God's presence.</p>
                    <ul class="ministry-details">
                        <li>Worship Team</li>
                        <li>Choir</li>
                        <li>Audio/Visual Team</li>
                        <li>Special Music Events</li>
                    </ul>
                    <a href="#" class="btn-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <div class="ministry-card animate-on-scroll slide-in-up hover-lift">
                    <div class="ministry-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h3>Outreach Ministry</h3>
                    <p>Our outreach ministry serves the local community and shares God's love through practical assistance, relationship building, and sharing the gospel.</p>
                    <ul class="ministry-details">
                        <li>Food Pantry</li>
                        <li>Community Service Projects</li>
                        <li>Evangelism Team</li>
                        <li>Prison Ministry</li>
                    </ul>
                    <a href="#" class="btn-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <div class="ministry-card animate-on-scroll slide-in-up hover-lift delay-2">
                    <div class="ministry-icon">
                        <i class="fas fa-pray"></i>
                    </div>
                    <h3>Prayer Ministry</h3>
                    <p>Our prayer ministry is committed to interceding for the church, community, and world needs, believing that prayer is powerful and effective.</p>
                    <ul class="ministry-details">
                        <li>Prayer Chain</li>
                        <li>Weekly Prayer Meetings</li>
                        <li>Prayer Counseling</li>
                        <li>Prayer Retreats</li>
                    </ul>
                    <a href="#" class="btn-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <div class="ministry-card animate-on-scroll slide-in-up hover-lift delay-3">
                    <div class="ministry-icon">
                        <i class="fas fa-globe-americas"></i>
                    </div>
                    <h3>Missions Ministry</h3>
                    <p>Our missions ministry supports and participates in God's work around the world through prayer, financial support, and short-term mission trips.</p>
                    <ul class="ministry-details">
                        <li>Missionary Support</li>
                        <li>Short-Term Mission Trips</li>
                        <li>Missions Education</li>
                        <li>International Partnerships</li>
                    </ul>
                    <a href="#" class="btn-link">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Small Groups Section -->
    <section class="about-section">
        <div class="container">
            <div class="section-header">
                <h2 class="animate-on-scroll fade-in">Small Groups</h2>
                <p class="animate-on-scroll fade-in">Connect with others in a more intimate setting</p>
            </div>
            
            <div class="about-content">
                <div class="about-image animate-on-scroll slide-in-left hover-zoom">
                    <img src="img/small-groups.jpg" alt="Small Groups">
                </div>
                <div class="about-text animate-on-scroll slide-in-right">
                    <h3>Life is Better Together</h3>
                    <p>Small groups are at the heart of our church community. These groups of 8-12 people meet regularly in homes for Bible study, prayer, and fellowship.</p>
                    <p>In a small group, you'll build meaningful relationships with others who will encourage you in your faith journey, pray for you, and support you through life's challenges and celebrations.</p>
                    <p>We have various types of small groups to meet different needs and interests:</p>
                    <ul>
                        <li>Family Groups</li>
                        <li>Men's Groups</li>
                        <li>Women's Groups</li>
                        <li>Young Adult Groups</li>
                        <li>Senior Adult Groups</li>
                        <li>Interest-Based Groups</li>
                    </ul>
                    <a href="#" class="btn btn-primary hover-lift">Find a Group</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Volunteer Section -->
    <section class="team-section">
        <div class="container">
            <div class="section-header">
                <h2 class="animate-on-scroll fade-in">Volunteer Opportunities</h2>
                <p class="animate-on-scroll fade-in">Use your gifts to serve others</p>
            </div>
            
            <div class="grid grid-3">
                <div class="team-card animate-on-scroll slide-in-up hover-lift">
                    <div class="team-image hover-zoom">
                        <img src="img/volunteer1.jpg" alt="Greeter Team">
                    </div>
                    <div class="team-info">
                        <h3>Welcome Team</h3>
                        <p>Help create a warm and welcoming environment for visitors and members by serving as a greeter, usher, or at the welcome desk.</p>
                        <a href="#" class="btn-link">Join Team <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                
                <div class="team-card animate-on-scroll slide-in-up hover-lift delay-2">
                    <div class="team-image hover-zoom">
                        <img src="img/volunteer2.jpg" alt="Tech Team">
                    </div>
                    <div class="team-info">
                        <h3>Tech Team</h3>
                        <p>Support our worship services by operating sound, lighting, video, or presentation software. Training is provided for all positions.</p>
                        <a href="#" class="btn-link">Join Team <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                
                <div class="team-card animate-on-scroll slide-in-up hover-lift delay-3">
                    <div class="team-image hover-zoom">
                        <img src="img/volunteer3.jpg" alt="Children's Ministry Volunteer">
                    </div>
                    <div class="team-info">
                        <h3>Children's Ministry</h3>
                        <p>Invest in the next generation by serving as a Sunday School teacher, children's church helper, or nursery worker.</p>
                        <a href="#" class="btn-link">Join Team <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content animate-on-scroll scale-in">
                <h2>Find Your Place to Serve</h2>
                <p>God has given you unique gifts and talents to use for His glory and the good of others. We'd love to help you find your place in ministry!</p>
                <a href="contact.php" class="btn btn-secondary hover-lift">Contact Us</a>
                <a href="#" class="btn btn-outline hover-lift">Take Spiritual Gifts Assessment</a>
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

