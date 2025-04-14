<?php
$page_title = "Our Services";
include 'includes/public_header.php';

// Fetch active services
$query = "SELECT * FROM services WHERE is_active = 1 ORDER BY FIELD(service_day, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), service_time";
$result = $conn->query($query);
$services = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

// Group services by day
$servicesByDay = [];
foreach ($services as $service) {
    $day = $service['service_day'];
    if (!isset($servicesByDay[$day])) {
        $servicesByDay[$day] = [];
    }
    $servicesByDay[$day][] = $service;
}
?>

<!-- Services Section -->
<section id="services" class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-heading text-uppercase">Our Services</h2>
                <h3 class="section-subheading text-muted mb-5">Join us for worship and fellowship throughout the week.</h3>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card shadow">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>Day</th>
                                        <th>Service</th>
                                        <th>Time</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($services)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4">No services scheduled at this time. Please check back later.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($servicesByDay as $day => $dayServices): ?>
                                            <?php foreach ($dayServices as $index => $service): ?>
                                                <tr class="service-row" data-id="<?php echo $service['service_id']; ?>">
                                                    <?php if ($index === 0): ?>
                                                        <td class="font-weight-bold" rowspan="<?php echo count($dayServices); ?>"><?php echo htmlspecialchars($day); ?></td>
                                                    <?php endif; ?>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($service['service_title']); ?></strong>
                                                        <?php if (!empty($service['service_leader'])): ?>
                                                            <div class="small text-muted">Led by: <?php echo htmlspecialchars($service['service_leader']); ?></div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($service['service_time']); ?></td>
                                                    <td><?php echo htmlspecialchars($service['service_location']); ?></td>
                                                </tr>
                                                <?php if (!empty($service['service_description'])): ?>
                                                    <tr class="service-description-row bg-light">
                                                        <td colspan="<?php echo ($index === 0) ? 3 : 4; ?>" class="small pb-3">
                                                            <?php echo nl2br(htmlspecialchars($service['service_description'])); ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-5">
                    <p class="lead">We welcome everyone to join us in worship and fellowship.</p>
                    <a href="#contact" class="btn btn-primary btn-lg mt-3">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    // Add hover effect to service rows
    $('.service-row').hover(
        function() {
            $(this).addClass('bg-light');
            // Also highlight the description row if it exists
            $(this).next('.service-description-row').addClass('bg-light');
        },
        function() {
            $(this).removeClass('bg-light');
            // Remove highlight from description row
            $(this).next('.service-description-row').removeClass('bg-light');
        }
    );
    
    // Add animation when scrolling to the services section
    $(window).scroll(function() {
        var servicesSection = $('#services');
        if (servicesSection.length) {
            var position = servicesSection.offset().top;
            var scroll = $(window).scrollTop();
            var windowHeight = $(window).height();
            
            if (scroll + windowHeight > position) {
                $('.service-row').each(function(i) {
                    setTimeout(function() {
                        $('.service-row').eq(i).addClass('animate__animated animate__fadeInUp');
                    }, 150 * i);
                });
            }
        }
    });
});
</script>

<?php
include 'includes/public_footer.php';
?>

