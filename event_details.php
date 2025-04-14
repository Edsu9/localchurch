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

// Get event details
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$event = array();
$has_event = false;

if ($event_id > 0) {
    $event_query = "SELECT * FROM events WHERE id = $event_id";
    $event_result = mysqli_query($conn, $event_query);
    
    if (mysqli_num_rows($event_result) > 0) {
        $event = mysqli_fetch_assoc($event_result);
        $has_event = true;
    }
}

// Format event date and time
$event_date = $has_event ? date('F d, Y', strtotime($event['event_date'])) : '';
$start_time = '';
$end_time = '';
$time_display = '';

if ($has_event) {
    $start_time = isset($event['start_time']) ? date('g:i A', strtotime($event['start_time'])) : 
                 (isset($event['event_time']) ? date('g:i A', strtotime($event['event_time'])) : '');
    $end_time = isset($event['end_time']) ? date('g:i A', strtotime($event['end_time'])) : '';
    $time_display = $start_time;
    if (!empty($end_time)) {
        $time_display .= ' - ' . $end_time;
    }
}

// Get event image
$event_image = 'img/event-default.jpg';
if ($has_event && !empty($event['event_image']) && file_exists('uploads/event_images/' . $event['event_image'])) {
    $event_image = 'uploads/event_images/' . $event['event_image'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $has_event ? $event['event_name'] : 'Event Details'; ?> - <?php echo $church_name; ?></title>
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
                    <h1><?php echo $church_name; ?></h1>
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
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="fade-in-up"><?php echo $has_event ? $event['event_name'] : 'Event Details'; ?></h1>
            <p class="fade-in-up">Join us for this special event and be part of our community</p>
        </div>
    </div>

    <?php if ($has_event): ?>
    <section class="fade-in-up">
        <div class="container">
            <div class="grid-2" style="align-items: start;">
                <div class="event-image-container hover-lift">
                    <img src="<?php echo $event_image; ?>" alt="<?php echo $event['event_name']; ?>" class="card-img" style="height: auto; max-height: 500px; width: 100%; object-fit: cover; border-radius: var(--radius-md); box-shadow: var(--shadow-md);">
                </div>
                
                <div class="event-details">
                    <div class="event-date-badge" style="display: inline-block; background-color: var(--primary-color); color: white; padding: 8px 15px; border-radius: var(--radius-sm); margin-bottom: 20px; font-weight: 600;">
                        <i class="far fa-calendar-alt"></i> <?php echo $event_date; ?>
                    </div>
                    
                    <h2 style="margin-bottom: 20px; color: var(--primary-dark);"><?php echo $event['event_name']; ?></h2>
                    
                    <div class="event-meta" style="margin-bottom: 30px;">
                        <div class="event-meta-item" style="display: flex; align-items: center; margin-bottom: 15px;">
                            <i class="far fa-clock" style="color: var(--primary-color); margin-right: 10px; font-size: 1.2rem;"></i>
                            <span><?php echo $time_display; ?></span>
                        </div>
                        
                        <div class="event-meta-item" style="display: flex; align-items: center; margin-bottom: 15px;">
                            <i class="fas fa-map-marker-alt" style="color: var(--primary-color); margin-right: 10px; font-size: 1.2rem;"></i>
                            <span><?php echo $event['location'] ?? $event['event_venue'] ?? 'Location TBA'; ?></span>
                        </div>
                        
                        <?php if (isset($event['organizer']) && !empty($event['organizer'])): ?>
                        <div class="event-meta-item" style="display: flex; align-items: center; margin-bottom: 15px;">
                            <i class="fas fa-user" style="color: var(--primary-color); margin-right: 10px; font-size: 1.2rem;"></i>
                            <span>Organized by: <?php echo $event['organizer']; ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="event-description" style="margin-bottom: 30px; line-height: 1.8;">
                        <h3 style="margin-bottom: 15px; color: var(--text-color);">About This Event</h3>
                        <?php 
                        $description = $event['description'] ?? $event['event_description'] ?? '';
                        echo nl2br(htmlspecialchars($description)); 
                        ?>
                    </div>
                    
                    <?php if (isset($event['registration_required']) && $event['registration_required'] == 1): ?>
                    <div class="event-registration" style="background-color: var(--gray-light); padding: 20px; border-radius: var(--radius-md); margin-bottom: 30px;">
                        <h3 style="margin-bottom: 15px; color: var(--text-color);">Registration Information</h3>
                        <p>This event requires registration. Please contact us for more information on how to register.</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="event-cta" style="margin-top: 30px;">
                        <a href="events_public.php" class="btn btn-primary hover-lift"><i class="fas fa-arrow-left"></i> Back to Events</a>
                        <a href="contact.php" class="btn btn-outline hover-lift"><i class="fas fa-envelope"></i> Contact Us</a>
                        
                        <?php if (isset($event['registration_required']) && $event['registration_required'] == 1): ?>
                        <a href="#" class="btn btn-secondary hover-lift"><i class="fas fa-user-plus"></i> Register Now</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Related Events Section -->
            <?php
            // Get related events (same category or upcoming events)
            $related_query = "SELECT * FROM events 
                             WHERE id != $event_id 
                             AND event_date >= CURDATE() 
                             ORDER BY event_date ASC LIMIT 3";
            $related_result = mysqli_query($conn, $related_query);
            
            if (mysqli_num_rows($related_result) > 0):
            ?>
            <div class="related-events" style="margin-top: 80px;">
                <div class="section-header">
                    <h2>Upcoming Events</h2>
                    <p>Check out these other events you might be interested in</p>
                </div>
                
                <div class="grid-3">
                    <?php while($related_event = mysqli_fetch_assoc($related_result)): ?>
                    <?php
                    // Get related event image
                    $related_image = 'img/event-default.jpg';
                    if (!empty($related_event['event_image']) && file_exists('uploads/event_images/' . $related_event['event_image'])) {
                        $related_image = 'uploads/event_images/' . $related_event['event_image'];
                    }
                    
                    // Format related event time
                    $related_start = isset($related_event['start_time']) ? date('g:i A', strtotime($related_event['start_time'])) : 
                                    (isset($related_event['event_time']) ? date('g:i A', strtotime($related_event['event_time'])) : '');
                    $related_end = isset($related_event['end_time']) ? date('g:i A', strtotime($related_event['end_time'])) : '';
                    $related_time = $related_start;
                    if (!empty($related_end)) {
                        $related_time .= ' - ' . $related_end;
                    }
                    ?>
                    <div class="card event-card hover-lift">
                        <div class="event-date">
                            <?php echo date('F d, Y', strtotime($related_event['event_date'])); ?>
                        </div>
                        <img src="<?php echo $related_image; ?>" alt="<?php echo $related_event['event_name']; ?>" class="card-img">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo $related_event['event_name']; ?></h3>
                            <div class="event-time">
                                <i class="far fa-clock"></i> <?php echo $related_time; ?>
                            </div>
                            <div class="event-location">
                                <i class="fas fa-map-marker-alt"></i> <?php echo $related_event['location'] ?? $related_event['event_venue'] ?? 'Location TBA'; ?>
                            </div>
                            <a href="event_details.php?id=<?php echo $related_event['id']; ?>" class="btn btn-outline">Learn More</a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php else: ?>
    <section class="fade-in-up">
        <div class="container" style="text-align: center; padding: 80px 0;">
            <div class="not-found-icon" style="font-size: 5rem; color: var(--primary-color); margin-bottom: 20px;">
                <i class="fas fa-calendar-times"></i>
            </div>
            <h2 style="margin-bottom: 20px; color: var(--primary-dark);">Event Not Found</h2>
            <p style="max-width: 600px; margin: 0 auto 30px;">We're sorry, but the event you're looking for is not available or may have been removed.</p>
            <a href="events_public.php" class="btn btn-primary hover-lift">View All Events</a>
        </div>
    </section>
    <?php endif; ?>

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
</body>
</html>

