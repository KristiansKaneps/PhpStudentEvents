<!-- Hero Section -->
<section class="hero">
    <h1>Discover Student Events</h1>
    <p>Engage, participate, and grow with opportunities at our university!</p>
    <a href="#">Explore Events</a>
</section>

<section>
    <h2 class="section-title"><?= htmlspecialchars($event['name']) ?></h2>
    <h1><?= htmlspecialchars($event['name']) ?></h1>
    <p><?= htmlspecialchars($event['description']) ?></p>
    <p>Category: <?= htmlspecialchars($event['category_name']) ?></p>
    <p>Starts: <?= htmlspecialchars($event['start_date']) ?></p>
    <p>Ends: <?= htmlspecialchars($event['end_date']) ?></p>
</section>