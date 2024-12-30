<?php
/** @var array $user */
use Services\Auth;
?>
<section>
    <h2 class="section-title"><?= t('section.profile.title') ?></h2>
    <p class="section-subtitle"><?= userId() === $user['id'] ? t('section.profile.subtitle.my_profile') : t('section.profile.subtitle.other_profile', $user) ?></p>
    <div class="profile-container">
        <form action="<?= userId() === $user['id'] ? route('profile.update') : route('profile.update.other', $user['id']) ?>" method="post" class="profile-form">
            <input type="hidden" name="csrf" value="<?= csrf() ?>">

            <div class="form-group">
                <label for="name"><?= t('form.label.name') ?></label>
                <input type="text" id="name" name="name" placeholder="<?= t('form.placeholder.name') ?>" value="<?= old('name', $user['name']) ?>" required>
                <?php if (has('error_name')): ?>
                <p class="error"><?= old('error_name') ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="surname"><?= t('form.label.surname') ?></label>
                <input type="text" id="surname" name="surname" placeholder="<?= t('form.placeholder.surname') ?>" value="<?= old('surname', $user['surname']) ?>" required>
                <?php if (has('error_surname')): ?>
                <p class="error"><?= old('error_surname') ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email"><?= t('form.label.email') ?></label>
                <input type="email" id="email" name="email" placeholder="<?= t('form.placeholder.email') ?>" value="<?= old('email', $user['email']) ?>" required>
                <?php if (has('error_email')): ?>
                <p class="error"><?= old('error_email') ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="student_id"><?= t('form.label.student_id') ?></label>
                <input type="text" id="student_id" name="student_id" placeholder="<?= t('form.placeholder.student_id') ?>" value="<?= old('student_id', $user['student_id']) ?>">
                <?php if (has('error_student_id')): ?>
                <p class="error"><?= old('error_student_id') ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone"><?= t('form.label.phone') ?></label>
                <input type="text" id="phone" name="phone" placeholder="<?= t('form.placeholder.phone') ?>" value="<?= old('phone', $user['phone']) ?>">
                <?php if (has('error_phone')): ?>
                <p class="error"><?= old('error_phone') ?></p>
                <?php endif; ?>
            </div>

            <?php if (isAdmin()): ?>
            <div class="form-group">
                <label for="role"><?= t('form.label.role') ?></label>
                <?php $selectedRole = old('role', $user['role']) ?>
                <select id="role" name="role">
                    <option value="<?= Auth::USER_ROLE_USER ?>" <?= $selectedRole === Auth::USER_ROLE_USER ? 'selected="selected"' : '' ?>><?= t('user.role.user') ?></option>
                    <option value="<?= Auth::USER_ROLE_ORGANIZER ?>" <?= $selectedRole === Auth::USER_ROLE_ORGANIZER ? 'selected="selected"' : '' ?>><?= t('user.role.organizer') ?></option>
                    <option value="<?= Auth::USER_ROLE_ADMIN ?>" <?= $selectedRole === Auth::USER_ROLE_ADMIN ? 'selected="selected"' : '' ?>><?= t('user.role.admin') ?></option>
                </select>
                <?php if (has('error_role')): ?>
                <p class="error"><?= old('error_role') ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="password"><?= t('form.label.password') ?></label>
                <input type="password" id="password" name="password" placeholder="<?= t('form.placeholder.password') ?>" <?= userId() !== $user['id'] && isAdmin() ? 'disabled="disabled"' : 'required' ?>>
                <?php if (has('error_password')): ?>
                <p class="error"><?= old('error_password') ?></p>
                <?php endif; ?>
                <label for="password_new"><?= t('form.label.password_change') ?></label>
                <input type="password" id="password_new" name="password_new" placeholder="<?= t('form.placeholder.password_new') ?>">
                <?php if (has('error_password_new')): ?>
                <p class="error"><?= old('error_password_new') ?></p>
                <?php endif; ?>
                <input type="password" id="password_new_confirm" name="password_new_confirm" placeholder="<?= t('form.placeholder.password_new_confirm') ?>">
                <?php if (has('error_password_new_confirm')): ?>
                <p class="error"><?= old('error_password_new_confirm') ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn"><?= t('form.btn.save_changes') ?></button>
        </form>
    </div>
</section>