<?php /** @var array $events */ ?>
<!-- Hero Section -->
<section class="hero">
    <h1><?= t('section.home.title') ?></h1>
    <p><?= t('section.home.subtitle') ?></p>
    <a href="<?= route('event.list') ?>"><?= t('section.home.btn.explore') ?></a>
</section>

<!-- Events Section -->
<section>
    <h2 class="section-title"><?= t('section.events.title') ?></h2>
    <div class="card-container">
        <?php if (count($events) > 0): ?>
        <?php foreach ($events as $eventData): ?>
        <div class="card">
            <span class="tag"><?= htmlspecialchars($eventData['category_name']) ?></span>
            <span class="tag-opposite event-date"><?= htmlspecialchars($eventData['start_date']) ?></span>
            <h3><?= htmlspecialchars($eventData['name']) ?></h3>
            <p class="event-participants">
                <?= t($eventData['max_participant_count'] == 0 ? 'section.events.participants_unlimited' : 'section.events.participants_limited', [
                    'current' => htmlspecialchars($eventData['current_participant_count']),
                    'max' => htmlspecialchars($eventData['max_participant_count']),
                ]) ?>
            </p>
            <p class="event-description text"><?= htmlspecialchars($eventData['description']) ?></p>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <p class="no-events"><?= t('section.events.no_events') ?></p>
        <?php endif; ?>
    </div>
</section>