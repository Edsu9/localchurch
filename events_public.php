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

// Get upcoming events - FIXED QUERY to match the fields in the events table
$current_date = date('Y-m-d');
$events_query = "SELECT * FROM events WHERE event_date >= '$current_date' AND (public_display = 1 OR public_display IS NULL) AND status != 'Cancelled' ORDER BY event_date ASC LIMIT 6";
$events_result = mysqli_query($conn, $events_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - <?php echo $church_name; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/website-style.css">
    <link rel="stylesheet" href="css/responsive-modern.css">
    <link rel="stylesheet" href="css/modern-animations.css">
    <style>
    /* Additional styles for event cards */
    .event-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        background-color: #fff;
        border-radius: var(--radius-md);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }
    
    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }
    
    .event-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }
    
    .event-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .event-card:hover .event-image img {
        transform: scale(1.05);
    }
    
    .event-date {
        position: absolute;
        top: 15px;
        left: 15px;
        background-color: var(--primary-color);
        color: white;
        padding: 10px 15px;
        border-radius: var(--radius-sm);
        text-align: center;
        font-weight: 600;
        box-shadow: var(--shadow-sm);
    }
    
    .event-date .day {
        font-size: 1.5rem;
        line-height: 1;
        display: block;
    }
    
    .event-date .month {
        font-size: 0.9rem;
        text-transform: uppercase;
    }
    
    .event-content {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    
    .event-content h3 {
        margin-bottom: 10px;
        font-size: 1.25rem;
        color: var(--text-color);
    }
    
    .event-meta {
        margin-bottom: 15px;
    }
    
    .event-meta p {
        margin-bottom: 5px;
        color: var(--text-light);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
    }
    
    .event-meta i {
        color: var(--primary-color);
        margin-right: 8px;
        width: 16px;
    }
    
    .event-excerpt {
        color: var(--text-light);
        margin-bottom: 15px;
        font-size: 0.95rem;
        flex-grow: 1;
    }
    
    .btn-link {
        align-self: flex-start;
        color: var(--primary-color);
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: var(--transition);
    }
    
    .btn-link i {
        margin-left: 5px;
        transition: transform 0.3s ease;
    }
    
    .btn-link:hover {
        color: var(--primary-dark);
    }
    
    .btn-link:hover i {
        transform: translateX(3px);
    }
    
    /* Event Modal Styles */
    .event-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.8);
    }
    
    .event-modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 0;
        width: 90%;
        max-width: 800px;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        position: relative;
        animation: modalFadeIn 0.3s ease;
    }
    
    @keyframes modalFadeIn {
        from {opacity: 0; transform: translateY(-20px);}
        to {opacity: 1; transform: translateY(0);}
    }
    
    .close-modal {
        position: absolute;
        top: 15px;
        right: 15px;
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        z-index: 10;
        transition: var(--transition);
    }
    
    .close-modal:hover {
        color: var(--primary-color);
    }
    
    .event-modal-body {
        padding: 30px;
    }
    
    .event-modal-body h2 {
        color: var(--primary-color);
        margin-bottom: 20px;
        font-size: 2rem;
    }
    
    .event-modal-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: var(--radius-md) var(--radius-md) 0 0;
        margin-bottom: 20px;
    }
    
    .event-modal-details {
        margin-bottom: 30px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .event-modal-details p {
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }
    
    .event-modal-details i {
        color: var(--primary-color);
        margin-right: 10px;
        width: 20px;
    }
    
    .event-modal-description h3 {
        color: var(--text-color);
        margin-bottom: 15px;
        font-size: 1.5rem;
    }
    
    .event-modal-description p {
        color: var(--text-light);
        line-height: 1.7;
    }
    
    .event-modal-actions {
        margin-top: 30px;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .event-loading {
        text-align: center;
        padding: 50px 0;
        color: var(--text-light);
    }
    
    .event-loading i {
        font-size: 2rem;
        color: var(--primary-color);
        margin-bottom: 15px;
        display: block;
    }
    
    .error-message {
        text-align: center;
        padding: 30px;
        color: #d32f2f;
    }
    
    .error-message i {
        font-size: 2rem;
        margin-bottom: 15px;
        display: block;
    }
    
    @media (max-width: 768px) {
        .event-modal-content {
            width: 95%;
            margin: 10% auto;
        }
        
        .event-modal-details {
            grid-template-columns: 1fr;
        }
        
        .event-modal-actions {
            flex-direction: column;
        }
        
        .event-modal-actions .btn {
            width: 100%;
            margin: 0 0 10px 0;
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
                    <li><a href="services.php">Services</a></li>
                    <li><a href="events_public.php" class="active">Events</a></li>
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
                    <li><a href="services.php">Services</a></li>
                    <li><a href="events_public.php" class="active">Events</a></li>
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
            <h1 class="animate-on-scroll slide-in-down">Upcoming Events</h1>
            <p class="animate-on-scroll slide-in-up">Join us for these special gatherings and activities</p>
        </div>
    </section>

    <!-- Events Section -->
    <section class="events-section">
        <div class="container">
            <div class="section-header">
                <h2 class="animate-on-scroll fade-in">Church Events</h2>
                <p class="animate-on-scroll fade-in">Connect, grow, and serve with us at these upcoming events</p>
            </div>
            
            <div class="events-grid grid-3">
                <?php 
                if (mysqli_num_rows($events_result) > 0) {
                    $delay = 1;
                    while ($event = mysqli_fetch_assoc($events_result)) {
                        // Get event image
                        $event_image = 'img/default-event.jpg';
                        if (!empty($event['event_image']) && file_exists('uploads/event_images/' . $event['event_image'])) {
                            $event_image = 'uploads/event_images/' . $event['event_image'];
                        }
                        
                        // Format times
                        $start_time = date('g:i A', strtotime($event['start_time'] ?? '00:00:00'));
                        $time_display = $start_time;
                        if (!empty($event['end_time'])) {
                            $end_time = date('g:i A', strtotime($event['end_time']));
                            $time_display .= ' - ' . $end_time;
                        }
                ?>
                <div class="event-card animate-on-scroll slide-in-up hover-lift delay-<?php echo $delay; ?>">
                    <div class="event-image">
                        <img src="<?php echo $event_image; ?>" alt="<?php echo htmlspecialchars($event['event_name']); ?>">
                        <div class="event-date">
                            <span class="day"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                            <span class="month"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                        </div>
                    </div>
                    <div class="event-content">
                        <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
                        <div class="event-meta">
                            <p><i class="fas fa-clock"></i> <?php echo $time_display; ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                        </div>
                        <p class="event-excerpt">
                            <?php 
                            $description = isset($event['description']) ? $event['description'] : '';
                            echo htmlspecialchars(substr($description, 0, 100)) . (strlen($description) > 100 ? '...' : ''); 
                            ?>
                        </p>
                        <a href="#" class="btn-link" data-event-id="<?php echo $event['id']; ?>">Learn More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <?php
                        $delay = $delay == 3 ? 1 : $delay + 1;
                    }
                } else {
                ?>
                <div class="no-events animate-on-scroll fade-in">
                    <p>No upcoming events at this time. Please check back soon!</p>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- Event Modal -->
    <div class="event-modal" id="eventModal">
        <div class="event-modal-content">
            <span class="close-modal">&times;</span>
            <div class="event-modal-body">
                <!-- Event details will be loaded here via AJAX -->
                <div class="event-loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading event details...
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Section -->
    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-text animate-on-scroll slide-in-left">
                    <h2>Church Calendar</h2>
                    <p>Our church calendar is filled with opportunities for worship, fellowship, service, and spiritual growth. We invite you to participate in the life of our church community.</p>
                    <p>In addition to our regular weekly services, we host special events throughout the year, including holiday celebrations, community outreach projects, youth activities, and more.</p>
                    <p>To stay updated on all our events, you can:</p>
                    <ul>
                        <li>Check this events page regularly</li>
                        <li>Subscribe to our email newsletter</li>
                        <li>Follow us on social media</li>
                        <li>Pick up a printed calendar at the church</li>
                    </ul>
                    <a href="#" class="btn btn-primary hover-lift">Subscribe to Updates</a>
                </div>
                <div class="about-image animate-on-scroll slide-in-right hover-zoom">
                    <img src="img/church-calendar.jpg" alt="Church Calendar">
                </div>
            </div>
        </div>
    </section>

    <!-- Past Events Section -->
    <section class="values-section">
        <div class="container">
            <div class="section-header">
                <h2 class="animate-on-scroll fade-in">Recent Events</h2>
                <p class="animate-on-scroll fade-in">Take a look at some of our past gatherings</p>
            </div>
            
            <div class="grid grid-3">
                <div class="value-card animate-on-scroll slide-in-up hover-lift">
                    <div class="value-image hover-zoom">
                        <img src="img/past-event1.jpg" alt="Youth Retreat">
                    </div>
                    <h3>Youth Summer Retreat</h3>
                    <p>Our youth had an amazing time at the annual summer retreat, with worship, games, and spiritual growth activities.</p>
                </div>
                
                <div class="value-card animate-on-scroll slide-in-up hover-lift delay-2">
                    <div class="value-image hover-zoom">
                        <img src="img/past-event2.jpg" alt="Community Outreach">
                    </div>
                    <h3>Community Outreach Day</h3>
                    <p>Church members came together to serve our community through various projects, including food distribution and home repairs.</p>
                </div>
                
                <div class="value-card animate-on-scroll slide-in-up hover-lift delay-3">
                    <div class="value-image hover-zoom">
                        <img src="img/past-event3.jpg" alt="Worship Night">
                    </div>
                    <h3>Worship Night</h3>
                    <p>A special evening of extended worship and prayer that brought our congregation together in God's presence.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content animate-on-scroll scale-in">
                <h2>Get Involved</h2>
                <p>There are many ways to participate in our church community. Join us for an upcoming event!</p>
                <a href="contact.php" class="btn btn-secondary hover-lift">Contact Us</a>
                <a href="ministries.php" class="btn btn-outline hover-lift">Explore Ministries</a>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event modal functionality
        const modal = document.getElementById('eventModal');
        const modalBody = modal.querySelector('.event-modal-body');
        const closeModal = modal.querySelector('.close-modal');
        const eventLinks = document.querySelectorAll('.btn-link[data-event-id]');
        
        eventLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const eventId = this.getAttribute('data-event-id');
                
                // Show loading indicator
                modalBody.innerHTML = '<div class="event-loading"><i class="fas fa-spinner fa-spin"></i> Loading event details...</div>';
                modal.style.display = 'block';
                
                // Fetch event details using AJAX
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'get_event_details.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (this.status === 200) {
                        modalBody.innerHTML = this.responseText;
                    } else {
                        modalBody.innerHTML = '<div class="error-message"><i class="fas fa-exclamation-circle"></i> An error occurred while loading event details.</div>';
                    }
                };
                xhr.onerror = function() {
                    modalBody.innerHTML = '<div class="error-message"><i class="fas fa-exclamation-circle"></i> An error occurred while loading event details.</div>';
                };
                xhr.send('event_id=' + eventId);
            });
        });
        
        closeModal.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>

