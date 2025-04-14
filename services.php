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
    <title>Services - <?php echo $church_name; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/website-style.css">
    <link rel="stylesheet" href="css/responsive-modern.css">
    <link rel="stylesheet" href="css/modern-animations.css">
    <style>
/* Service Times Section Improvements */
.service-times {
  background-color: var(--primary-color);
  color: var(--white);
  border-radius: var(--radius-md);
  padding: 40px;
  box-shadow: var(--shadow-md);
  text-align: center;
  margin-bottom: 50px;
}

.service-times h3 {
  color: var(--white);
  margin-bottom: 20px;
  font-size: 1.75rem;
}

.service-times p {
  color: rgba(255, 255, 255, 0.9);
}

.service-times-list {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 30px;
  margin-top: 30px;
}

.service-time-item {
  background-color: rgba(255, 255, 255, 0.1);
  padding: 20px;
  border-radius: var(--radius-sm);
  min-width: 200px;
  transition: var(--transition);
  opacity: 0;
  transform: translateY(20px);
}

.service-time-item.active {
  opacity: 1;
  transform: translateY(0);
}

.service-time-item:hover {
  background-color: rgba(255, 255, 255, 0.2);
  transform: translateY(-5px);
}

.service-time-item h4 {
  color: var(--white);
  margin-bottom: 10px;
  font-size: 1.25rem;
}

.service-time-item p {
  margin-bottom: 5px;
  font-size: 1rem;
  color: rgba(255, 255, 255, 0.9);
}

.service-time-icon {
  font-size: 24px;
  color: var(--white);
  margin-bottom: 15px;
}

/* Animation classes */
.animate-on-scroll {
  opacity: 0;
  transition: all 0.8s ease;
}

.animate-on-scroll.active {
  opacity: 1;
}

.slide-in-up {
  transform: translateY(50px);
}

.slide-in-up.active {
  transform: translateY(0);
}

.slide-in-down {
  transform: translateY(-50px);
}

.slide-in-down.active {
  transform: translateY(0);
}

.slide-in-left {
  transform: translateX(-50px);
}

.slide-in-left.active {
  transform: translateX(0);
}

.slide-in-right {
  transform: translateX(50px);
}

.slide-in-right.active {
  transform: translateX(0);
}

.fade-in {
  opacity: 0;
}

.fade-in.active {
  opacity: 1;
}

.scale-in {
  transform: scale(0.9);
}

.scale-in.active {
  transform: scale(1);
}

.stagger-item {
  opacity: 0;
  transform: translateY(20px);
  transition: all 0.5s ease;
}

.stagger-item.active {
  opacity: 1;
  transform: translateY(0);
}

.hover-lift {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Animation Delays */
.delay-1 {
  transition-delay: 0.1s;
}

.delay-2 {
  transition-delay: 0.2s;
}

.delay-3 {
  transition-delay: 0.3s;
}

.delay-4 {
  transition-delay: 0.4s;
}

.delay-5 {
  transition-delay: 0.5s;
}

@media (max-width: 768px) {
  .service-times-list {
    flex-direction: column;
  }
  
  .slide-in-left, .slide-in-right {
    transform: translateY(30px);
  }
  
  .slide-in-left.active, .slide-in-right.active {
    transform: translateY(0);
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
                    <li><a href="public_index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="services.php" class="active">Services</a></li>
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
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="services.php" class="active">Services</a></li>
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
            <h1 class="animate-on-scroll slide-in-down">Our Services</h1>
            <p class="animate-on-scroll slide-in-up">Join us for worship, fellowship, and spiritual growth</p>
        </div>
    </section>

    <!-- Service Times Section -->
    <section class="services-section">
        <div class="container">
            <div class="service-times animate-on-scroll scale-in">
                <h3>Weekly Service Schedule</h3>
                <p>We invite you to join us for any of our regular worship services. All are welcome!</p>
                
                <div class="service-times-list">
                    <?php
                    // Fetch active services from the database
                    $query = "SELECT * FROM services WHERE is_active = 1 ORDER BY FIELD(service_day, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), service_time";
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        $i = 0;
                        while ($service = $result->fetch_assoc()) {
                            $delay_class = "delay-" . min(($i % 5) + 1, 5);
                            ?>
                            <div class="service-time-item stagger-item <?php echo $delay_class; ?> hover-lift">
                                <div class="service-time-icon">
                                    <i class="fas fa-church"></i>
                                </div>
                                <h4><?php echo htmlspecialchars($service['service_title']); ?></h4>
                                <p><strong><?php echo htmlspecialchars($service['service_day']); ?></strong></p>
                                <p><?php echo htmlspecialchars($service['service_time']); ?></p>
                                <?php if (!empty($service['service_location'])): ?>
                                    <p><?php echo htmlspecialchars($service['service_location']); ?></p>
                                <?php endif; ?>
                            </div>
                            <?php
                            $i++;
                        }
                    } else {
                        // Fallback to static content if no services in database
                        ?>
                        <div class="service-time-item stagger-item">
                            <h4>Sunday Morning</h4>
                            <p>9:00 AM - 10:30 AM</p>
                            <p>Main Sanctuary</p>
                        </div>
                        
                        <div class="service-time-item stagger-item">
                            <h4>Sunday Evening</h4>
                            <p>6:00 PM - 7:30 PM</p>
                            <p>Main Sanctuary</p>
                        </div>
                        
                        <div class="service-time-item stagger-item">
                            <h4>Wednesday Bible Study</h4>
                            <p>7:00 PM - 8:30 PM</p>
                            <p>Fellowship Hall</p>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- What to Expect Section -->
    <section class="about-section">
        <div class="container">
            <div class="section-header">
                <h2 class="animate-on-scroll fade-in">What to Expect</h2>
                <p class="animate-on-scroll fade-in">Here's what you can expect when you visit us for the first time</p>
            </div>
            
            <div class="about-content">
                <div class="about-text animate-on-scroll slide-in-left">
                    <h3>A Warm Welcome</h3>
                    <p>When you arrive, our greeters will welcome you and help you find your way around. We have a welcome desk where you can get information about our church and ministries.</p>
                    
                    <h3>Worship Experience</h3>
                    <p>Our worship services include contemporary and traditional music, prayer, and Bible-based teaching. Services typically last about 90 minutes.</p>
                    
                    <h3>Children's Ministry</h3>
                    <p>We offer age-appropriate classes for children during our Sunday morning service. Your children will enjoy fun activities while learning biblical truths.</p>
                    
                    <h3>Dress Code</h3>
                    <p>There is no formal dress code. Some people dress casually while others prefer more formal attire. We want you to feel comfortable!</p>
                </div>
                <div class="about-image animate-on-scroll slide-in-right hover-zoom">
                    <img src="img/worship-service.jpg" alt="Worship Service">
                </div>
            </div>
        </div>
    </section>

    <!-- Service Types Section -->
    <section class="values-section">
        <div class="container">
            <div class="section-header">
                <h2 class="animate-on-scroll fade-in">Our Services</h2>
                <p class="animate-on-scroll fade-in">Different ways to worship and grow with us</p>
            </div>
            
            <div class="grid grid-3">
                <div class="value-card animate-on-scroll slide-in-up hover-lift">
                    <div class="value-icon">
                        <i class="fas fa-church"></i>
                    </div>
                    <h3>Sunday Worship</h3>
                    <p>Our main worship service includes contemporary music, prayer, and relevant Bible teaching. We celebrate communion on the first Sunday of each month.</p>
                </div>
                
                <div class="value-card animate-on-scroll slide-in-up hover-lift delay-2">
                    <div class="value-icon">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h3>Bible Study</h3>
                    <p>Our midweek Bible study offers in-depth teaching and discussion in a more intimate setting. It's a great way to deepen your understanding of Scripture.</p>
                </div>
                
                <div class="value-card animate-on-scroll slide-in-up hover-lift delay-3">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Small Groups</h3>
                    <p>We have various small groups that meet throughout the week in homes. These groups provide fellowship, prayer support, and Bible discussion.</p>
                </div>
                
                <div class="value-card animate-on-scroll slide-in-up hover-lift delay-1">
                    <div class="value-icon">
                        <i class="fas fa-child"></i>
                    </div>
                    <h3>Children's Church</h3>
                    <p>Our children's ministry provides age-appropriate teaching and activities for children during the Sunday morning service.</p>
                </div>
                
                <div class="value-card animate-on-scroll slide-in-up hover-lift delay-2">
                    <div class="value-icon">
                        <i class="fas fa-guitar"></i>
                    </div>
                    <h3>Worship Night</h3>
                    <p>Once a month, we host a special evening of extended worship and prayer. This is a powerful time of seeking God together.</p>
                </div>
                
                <div class="value-card animate-on-scroll slide-in-up hover-lift delay-3">
                    <div class="value-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h3>Prayer Meeting</h3>
                    <p>Join us for our weekly prayer meeting where we pray for our church, community, and world needs. All are welcome to participate.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Special Services Section -->
    <section class="about-section">
        <div class="container">
            <div class="section-header">
                <h2 class="animate-on-scroll fade-in">Special Services</h2>
                <p class="animate-on-scroll fade-in">Celebrating important moments in the Christian calendar</p>
            </div>
            
            <div class="grid grid-2">
                <div class="card animate-on-scroll slide-in-up hover-lift">
                    <img src="img/easter-service.jpg" alt="Easter Service" class="card-img">
                    <div class="card-body">
                        <h3 class="card-title">Easter Services</h3>
                        <p class="card-text">Join us for our special Easter services as we celebrate the resurrection of Jesus Christ. We offer a sunrise service, regular morning service, and activities for children.</p>
                    </div>
                </div>
                
                <div class="card animate-on-scroll slide-in-up hover-lift delay-2">
                    <img src="img/christmas-service.jpg" alt="Christmas Service" class="card-img">
                    <div class="card-body">
                        <h3 class="card-title">Christmas Services</h3>
                        <p class="card-text">Our Christmas Eve candlelight service is a beautiful time of worship and reflection on the birth of Christ. We also have a special Christmas morning celebration.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="team-section">
        <div class="container">
            <div class="section-header">
                <h2 class="animate-on-scroll fade-in">Testimonials</h2>
                <p class="animate-on-scroll fade-in">Hear from our church family</p>
            </div>
            
            <div class="grid grid-3">
                <div class="team-card animate-on-scroll slide-in-up hover-lift">
                    <div class="team-image hover-zoom">
                        <img src="img/testimonial1.jpg" alt="Maria Johnson">
                    </div>
                    <div class="team-info">
                        <h3>Maria Johnson</h3>
                        <p>"I've been attending <?php echo $church_name; ?> for over 5 years now, and it truly feels like family. The worship services are uplifting, and the teaching is both challenging and encouraging."</p>
                    </div>
                </div>
                
                <div class="team-card animate-on-scroll slide-in-up hover-lift delay-2">
                    <div class="team-image hover-zoom">
                        <img src="img/testimonial2.jpg" alt="David Williams">
                    </div>
                    <div class="team-info">
                        <h3>David Williams</h3>
                        <p>"When I first visited, I was immediately welcomed and made to feel at home. The people here genuinely care about each other and are committed to growing in their faith together."</p>
                    </div>
                </div>
                
                <div class="team-card animate-on-scroll slide-in-up hover-lift delay-3">
                    <div class="team-image hover-zoom">
                        <img src="img/testimonial3.jpg" alt="Sarah Thompson">
                    </div>
                    <div class="team-info">
                        <h3>Sarah Thompson</h3>
                        <p>"My children love coming to church here! The children's ministry is excellent, and as a parent, I appreciate that they're learning biblical truths in a fun and engaging way."</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content animate-on-scroll scale-in">
                <h2>Join Us This Sunday</h2>
                <p>We'd love to welcome you to our church family. Come experience worship, fellowship, and spiritual growth with us!</p>
                <a href="contact.php" class="btn btn-secondary hover-lift">Get Directions</a>
                <a href="events_public.php" class="btn btn-outline hover-lift">View Calendar</a>
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
    <script>
document.addEventListener("DOMContentLoaded", function() {
    // Animation on scroll
    const animateElements = document.querySelectorAll(".animate-on-scroll");
    const staggerItems = document.querySelectorAll(".stagger-item");

    const animateOnScroll = () => {
        const windowHeight = window.innerHeight;

        animateElements.forEach((element) => {
            const elementPosition = element.getBoundingClientRect().top;
            const elementVisible = 150;

            if (elementPosition < windowHeight - elementVisible) {
                element.classList.add("active");
            }
        });
        
        staggerItems.forEach((item, index) => {
            const itemPosition = item.getBoundingClientRect().top;
            const itemVisible = 150;
            
            if (itemPosition < windowHeight - itemVisible) {
                setTimeout(() => {
                    item.classList.add("active");
                }, 100 * (index % 10)); // Limit the delay to avoid too long delays
            }
        });
    };

    // Run animation check on load and scroll
    window.addEventListener("load", animateOnScroll);
    window.addEventListener("scroll", animateOnScroll);
    
    // Trigger initial animations
    setTimeout(animateOnScroll, 300);
});
</script>
</body>
</html>

