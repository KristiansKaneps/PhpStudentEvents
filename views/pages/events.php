<!-- Hero Section -->
<section class="hero">
    <h1>Discover Student Events</h1>
    <p>Engage, participate, and grow with opportunities at our university!</p>
    <a href="#">Explore Events</a>
</section>

<section>
    <h2 class="section-title">Upcoming Events</h2>
    <h1>Trending Events</h1>
    <ul>
        <?php /** @var array $events */ ?>
        <?php foreach ($events as $event): ?>
            <li>
                <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                <p><?php echo htmlspecialchars($event['description']); ?></p>
                <p>Starts: <?php echo htmlspecialchars($event['start_date']); ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
</section>