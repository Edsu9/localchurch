<?php
// Include database connection if not already included
if (!isset($conn)) {
    include_once 'config/db.php';  // Changed from 'db_connect.php' to 'config/db.php'
}

// Fetch website settings
$settings_query = "SELECT * FROM settings LIMIT 1";
$settings_result = mysqli_query($conn, $settings_query);
$settings = mysqli_fetch_assoc($settings_result);

// Default values if settings are not found
$church_name = isset($settings['church_name']) ? $settings['church_name'] : 'JIA Somal-ot';
$church_address = isset($settings['church_address']) ? $settings['church_address'] : '123 Church Street, City, Country';
$church_email = isset($settings['email']) ? $settings['email'] : 'info@jiasomalot.org';
$church_phone = isset($settings['phone']) ? $settings['phone'] : '+1 234 567 8900';

// Social media links
$facebook_url = isset($settings['facebook_url']) ? $settings['facebook_url'] : '#';
$twitter_url = isset($settings['twitter_url']) ? $settings['twitter_url'] : '#';
$instagram_url = isset($settings['instagram_url']) ? $settings['instagram_url'] : '#';
$youtube_url = isset($settings['youtube_url']) ? $settings['youtube_url'] : '#';
?>

<footer class="public-footer">
    <div class="footer-container">
        <div class="footer-section about-section">
            <h3>About <?php echo $church_name; ?></h3>
            <p>We are a community of believers dedicated to spreading the love of Christ and serving our community.</p>
            <div class="social-links">
                <?php if (!empty($facebook_url) && $facebook_url != '#'): ?>
                    <a href="<?php echo $facebook_url; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <?php endif; ?>
                
                <?php if (!empty($twitter_url) && $twitter_url != '#'): ?>
                    <a href="<?php echo $twitter_url; ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                <?php endif; ?>
                
                <?php if (!empty($instagram_url) && $instagram_url != '#'): ?>
                    <a href="<?php echo $instagram_url; ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                
                <?php if (!empty($youtube_url) && $youtube_url != '#'): ?>
                    <a href="<?php echo $youtube_url; ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer-section quick-links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="events_public.php">Events</a></li>
                <li><a href="ministries.php">Ministries</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
        
        <div class="footer-section contact-info">
            <h3>Contact Us</h3>
            <p><i class="fas fa-map-marker-alt"></i> <?php echo $church_address; ?></p>
            <p><i class="fas fa-envelope"></i> <a href="mailto:<?php echo $church_email; ?>"><?php echo $church_email; ?></a></p>
            <p><i class="fas fa-phone"></i> <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $church_phone); ?>"><?php echo $church_phone; ?></a></p>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <?php echo $church_name; ?>. All Rights Reserved.</p>
    </div>
</footer>

<style>
.public-footer {
    background-color: #2c3e50;
    color: #ecf0f1;
    padding: 50px 0 20px;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    padding: 0 20px;
}

.footer-section h3 {
    color: #fff;
    margin-bottom: 20px;
    font-size: 1.3rem;
    position: relative;
    padding-bottom: 10px;
}

.footer-section h3::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 50px;
    height: 2px;
    background-color: var(--primary-color, #4a6da7);
}

.about-section p {
    margin-bottom: 20px;
    line-height: 1.6;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-links a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background-color: rgba(255, 255, 255, 0.1);
    color: #fff;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.social-links a:hover {
    background-color: var(--primary-color, #4a6da7);
    transform: translateY(-3px);
}

.quick-links ul {
    list-style: none;
    padding: 0;
}

.quick-links li {
    margin-bottom: 10px;
}

.quick-links a {
    color: #ecf0f1;
    text-decoration: none;
    transition: color 0.3s ease;
    display: inline-block;
    position: relative;
}

.quick-links a::after {
    content: '';
    position: absolute;
    width: 0;
    height: 1px;
    bottom: -2px;
    left: 0;
    background-color: var(--primary-color, #4a6da7);
    transition: width 0.3s ease;
}

.quick-links a:hover {
    color: var(--primary-color, #4a6da7);
}

.quick-links a:hover::after {
    width: 100%;
}

.contact-info p {
    margin-bottom: 15px;
    display: flex;
    align-items: flex-start;
}

.contact-info i {
    margin-right: 10px;
    color: var(--primary-color, #4a6da7);
    font-size: 1.1rem;
    margin-top: 3px;
}

.contact-info a {
    color: #ecf0f1;
    text-decoration: none;
    transition: color 0.3s ease;
}

.contact-info a:hover {
    color: var(--primary-color, #4a6da7);
}

.footer-bottom {
    text-align: center;
    padding-top: 30px;
    margin-top: 30px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-bottom p {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7);
}

@media (max-width: 768px) {
    .footer-container {
        grid-template-columns: 1fr;
    }
    
    .footer-section {
        margin-bottom: 30px;
    }
}
</style>

