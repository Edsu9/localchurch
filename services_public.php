<?php
// Include database connection
require_once 'config/db.php';

// Get active services
$query = "SELECT * FROM services WHERE is_active = 1 ORDER BY FIELD(service_day, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), service_time";
$result = $conn->query($query);

// Get church settings
$query = "SELECT * FROM settings WHERE id = 1";
$settings_result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($settings_result);
$church_name = isset($settings['church_name']) ? $settings['church_name'] : 'JIA Somal-ot Church';

$page_title = "Weekly Services - " . $church_name;
include 'includes/public_header.php';
?>

<div class="page-banner services-banner">
    <div class="container">
        <h1 class="animate-on-scroll fade-in">Weekly Service Schedule</h1>
        <p class="animate-on-scroll fade-in delay-1">We invite you to join us for any of our regular worship services. All are welcome!</p>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="service-schedule-container animate-on-scroll fade-in">
                <?php
                if ($result && $result->num_rows > 0) {
                    $current_day = '';
                    $i = 0;
                    
                    while ($service = $result->fetch_assoc()) {
                        // Group services by day
                        if ($service['service_day'] != $current_day) {
                            if ($current_day != '') {
                                echo '</div>'; // Close previous day's services
                            }
                            $current_day = $service['service_day'];
                            $delay_class = "delay-" . min(($i % 5) + 1, 5);
                            echo '<div class="service-day-group animate-on-scroll slide-in-left ' . $delay_class . '">';
                            echo '<h3 class="service-day">' . htmlspecialchars($service['service_day']) . '</h3>';
                            $i++;
                        }
                        
                        $delay_class = "delay-" . min(($i % 5) + 1, 5);
                        ?>
                        <div class="service-item animate-on-scroll slide-in-up <?php echo $delay_class; ?> hover-lift">
                            <div class="service-details">
                                <h4><?php echo htmlspecialchars($service['service_title']); ?></h4>
                                <p class="service-time"><i class="fas fa-clock"></i> <strong><?php echo htmlspecialchars($service['service_time']); ?></strong></p>
                                <?php if (!empty($service['service_location'])): ?>
                                    <p class="service-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($service['service_location']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($service['service_leader'])): ?>
                                    <p class="service-leader"><i class="fas fa-user"></i> <?php echo htmlspecialchars($service['service_leader']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($service['service_description'])): ?>
                                    <p class="service-description"><?php echo nl2br(htmlspecialchars($service['service_description'])); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                        $i++;
                    }
                    
                    if ($current_day != '') {
                        echo '</div>'; // Close the last day's services
                    }
                } else {
                    echo '<div class="no-services-message animate-on-scroll fade-in">';
                    echo '<p>No services scheduled at this time. Please check back later.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<style>
.services-banner {
    background-color: #2e7d32;
    color: white;
    padding: 60px 0;
    text-align: center;
    margin-bottom: 30px;
}

.services-banner h1 {
    font-size: 2.5rem;
    margin-bottom: 15px;
    color: white;
}

.services-banner p {
    font-size: 1.2rem;
    max-width: 800px;
    margin: 0 auto;
    color: rgba(255, 255, 255, 0.9);
}

.service-schedule-container {
    background-color: #f9f9f9;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.service-day-group {
    margin-bottom: 30px;
}

.service-day {
    color: #2e7d32;
    border-bottom: 2px solid #2e7d32;
    padding-bottom: 10px;
    margin-bottom: 20px;
    font-weight: 600;
}

.service-item {
    background-color: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 1px 5px rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s;
}

.service-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.service-details h4 {
    color: #2e7d32;
    margin-bottom: 10px;
    font-weight: 600;
}

.service-time, .service-location, .service-leader {
    margin-bottom: 8px;
    color: #555;
}

.service-time i, .service-location i, .service-leader i {
    margin-right: 8px;
    color: #2e7d32;
}

.service-description {
    margin-top: 15px;
    font-style: italic;
    color: #666;
}

.no-services-message {
    text-align: center;
    padding: 40px 0;
    color: #666;
}

.no-services-message p {
    font-size: 1.1rem;
}

/* Animation classes from modern-animations.css */
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

.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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
    .services-banner {
        padding: 40px 0;
    }
    
    .services-banner h1 {
        font-size: 2rem;
    }
    
    .service-schedule-container {
        padding: 20px;
    }
    
    .slide-in-left, .slide-in-right {
        transform: translateY(30px);
    }
    
    .slide-in-left.active, .slide-in-right.active {
        transform: translateY(0);
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Animation on scroll
    const animateElements = document.querySelectorAll(".animate-on-scroll");

    const animateOnScroll = () => {
        const windowHeight = window.innerHeight;

        animateElements.forEach((element) => {
            const elementPosition = element.getBoundingClientRect().top;
            const elementVisible = 150;

            if (elementPosition < windowHeight - elementVisible) {
                element.classList.add("active");
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

<?php include 'includes/public_footer.php'; ?>

