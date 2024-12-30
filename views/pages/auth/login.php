<section>
    <h2 class="section-title"><?= t('section.login.title') ?></h2>
    <div class="auth-container">
        <form action="<?= route('login') ?>" method="POST">
            <input hidden="hidden" type="text" name="csrf" value="<?= csrf() ?>">
            <div class="form-group">
                <label for="email"><?= t('form.label.email') ?></label>
                <input type="email" id="email" name="email" placeholder="<?= t('form.placeholder.email') ?>" value="<?= old('email') ?>" required>
                <?php if (has('error_email')): ?>
                <p class="error"><?= old('error_email') ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password"><?= t('form.label.password') ?></label>
                <input type="password" id="password" name="password" placeholder="<?= t('form.placeholder.password') ?>" required>
                <?php if (has('error_password')): ?>
                <p class="error"><?= old('error_password') ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn"><?= t('form.btn.login') ?></button>
        </form>

        <p class="alt-option">
            <?= t('form.alt.no_account') ?> <a href="<?= route('register') ?>"><?= t('form.btn.register') ?></a>
        </p>
    </div>
</section>