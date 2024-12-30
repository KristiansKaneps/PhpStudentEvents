<?php
/** @var array $events */
/** @var array $categories */
use Services\Auth;
?>
<section>
    <h2 class="section-title"><?= t('section.events.title') ?></h2>
    <p class="section-subtitle"><?= isOrganizer() ? t('section.events.subtitle.organizer') : t('section.events.subtitle.user') ?></p>

    <div class="events-container">
        <div class="events-list">
            <h3 class="section-subtitle"><?= t('section.events.list_title') ?></h3>
            <ul>
                <?php if (count($events) > 0): ?>
                <?php foreach ($events as $eventData): ?>
                <li class="event-item">
                    <h4 class="event-title"><?= htmlspecialchars($eventData['name']) ?></h4>
                    <p class="event-date"><?= htmlspecialchars($eventData['start_date']) ?> – <?= htmlspecialchars($eventData['end_date']) ?></p>
                    <p class="event-description"><?= htmlspecialchars($eventData['description']) ?></p>
                </li>
                <?php endforeach; ?>
                <?php else: ?>
                <p class="no-events"><?= t('section.events.no_events') ?></p>
                <?php endif; ?>
            </ul>
        </div>

        <?php if (isOrganizer()): ?>
        <div class="new-event-form">
            <h3 class="section-subtitle"><?= t('section.events.create_title') ?></h3>
            <form action="<?= route('event.create') ?>" method="POST">
                <input type="hidden" name="csrf" value="<?= csrf() ?>">

                <!-- Event Name -->
                <div class="form-group">
                    <label for="name"><?= t('form.label.event.name') ?></label>
                    <input type="text" id="name" name="name" placeholder="<?= t('form.placeholder.event.name') ?>" value="<?= old('name', $event['name'] ?? '') ?>" maxlength="127" required>
                    <?php if (has('error_name')): ?>
                    <p class="error"><?= old('error_name') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description"><?= t('form.label.event.description') ?></label>
                    <textarea id="description" name="description" placeholder="<?= t('form.placeholder.event.description') ?>" rows="10" required><?= old('description', $event['description'] ?? '') ?></textarea>
                    <?php if (has('error_description')): ?>
                    <p class="error"><?= old('error_description') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Category -->
                <div class="form-group">
                    <label for="category_id"><?= t('form.label.event.category') ?></label>
                    <select id="category_id" name="category_id" required>
                        <?php $selectedCategory = old('category_id', $event['category_id'] ?? ''); ?>
                        <option value="" <?= empty($selectedCategory) ? 'disabled="disabled" selected="selected"' : '' ?>><?= t('form.placeholder.event.category') ?></option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $selectedCategory == $category['id'] ? 'selected="selected"' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (has('error_category_id')): ?>
                    <p class="error"><?= old('error_category_id') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Max Participants -->
                <div class="form-group">
                    <label for="max_participant_count"><?= t('form.label.event.max_participant_count') ?></label>
                    <input type="number" id="max_participant_count" name="max_participant_count" placeholder="<?= t('form.placeholder.event.max_participant_count') ?>" value="<?= old('max_participant_count', $event['max_participant_count'] ?? '') ?>" min="0" required>
                    <?php if (has('error_max_participant_count')): ?>
                    <p class="error"><?= old('error_max_participant_count') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Start Date -->
                <div class="form-group">
                    <label for="start_date"><?= t('form.label.event.start_date') ?></label>
                    <input type="datetime-local" id="start_date" name="start_date" value="<?= old('start_date', $event['start_date'] ?? '') ?>" required>
                    <?php if (has('error_start_date')): ?>
                    <p class="error"><?= old('error_start_date') ?></p>
                    <?php endif; ?>
                </div>

                <!-- End Date -->
                <div class="form-group">
                    <label for="end_date"><?= t('form.label.event.end_date') ?></label>
                    <input type="datetime-local" id="end_date" name="end_date" value="<?= old('end_date', $event['end_date'] ?? '') ?>" required>
                    <?php if (has('error_end_date')): ?>
                    <p class="error"><?= old('error_end_date') ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn"><?= t('form.btn.create') ?></button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</section>