<section>
    <h2 class="section-title"><?= t('section.register.title') ?></h2>
    <div class="auth-container">
        <form action="<?= route('register') ?>" method="POST">
            <input hidden="hidden" type="text" name="csrf" value="<?= csrf() ?>">
            <div class="form-group">
                <label for="name"><?= t('form.label.name') ?></label>
                <input type="text" id="name" name="name" placeholder="<?= t('form.placeholder.name') ?>" value="<?= old('name') ?>" required>
                <?php if (has('error_name')): ?>
                <p class="error"><?= old('error_name') ?></p>
                <?php endif; ?>
                <label for="surname"><?= t('form.label.surname') ?></label>
                <input type="text" id="surname" name="surname" placeholder="<?= t('form.placeholder.surname') ?>" value="<?= old('surname') ?>" required>
                <?php if (has('error_surname')): ?>
                <p class="error"><?= old('error_surname') ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email"><?= t('form.label.email') ?></label>
                <input type="email" id="email" name="email" placeholder="<?= t('form.placeholder.email') ?>" value="<?= old('email') ?>" required>
                <?php if (has('error_email')): ?>
                <p class="error"><?= old('error_email') ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="student_id"><?= t('form.label.student_id') ?></label>
                <input type="text" id="student_id" name="student_id" placeholder="<?= t('form.placeholder.student_id') ?>" value="<?= old('student_id') ?>">
                <?php if (has('error_student_id')): ?>
                <p class="error"><?= old('error_student_id') ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="phone"><?= t('form.label.phone') ?></label>
                <input type="tel" id="phone" name="phone" placeholder="<?= t('form.placeholder.phone') ?>" value="<?= old('phone') ?>">
                <?php if (has('error_phone')): ?>
                <p class="error"><?= old('error_phone') ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password"><?= t('form.label.password') ?></label>
                <input type="password" id="password" name="password" placeholder="<?= t('form.placeholder.password') ?>" required>
                <?php if (has('error_password')): ?>
                <p class="error"><?= old('error_password') ?></p>
                <?php endif; ?>
                <input type="password" id="password" name="password_confirm" placeholder="<?= t('form.placeholder.password_confirm') ?>" required>
                <?php if (has('error_password_confirm')): ?>
                <p class="error"><?= old('error_password_confirm') ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn"><?= t('form.btn.register') ?></button>
        </form>

        <p class="alt-option">
            <?= t('form.alt.is_account') ?> <a href="<?= route('login') ?>"><?= t('form.btn.login') ?></a>
        </p>
    </div>
</section>