<section>
    <h2 class="section-title"><?= t('section.events.title') ?></h2>
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