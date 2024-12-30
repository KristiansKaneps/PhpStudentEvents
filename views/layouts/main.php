<!doctype html>
<html lang="<?= locale() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <link rel="manifest" href="/site.webmanifest" />
    <title><?= htmlspecialchars($title ?? t('app.name')) ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
<!-- Header -->
<header>
    <a class="logo-a" href="<?= route('home') ?>">
        <img src="/assets/images/logo.svg" alt="<?= t('app.name') ?> logo">
        <span><?= t('nav.logo_title') ?></span>
    </a>
    <nav>
        <ul>
            <li><a href="<?= route('home') ?>"><?= t('nav.home') ?></a></li>
            <li><a href="<?= route('events') ?>"><?= t('nav.events') ?></a></li>
            <li><a href="<?= route('about') ?>"><?= t('nav.about') ?></a></li>
            <li><a href="<?= route('contact') ?>"><?= t('nav.contact') ?></a></li>
            <?php if (auth()): ?>
            <li><a href="<?= route('profile') ?>"><?= t('nav.profile') ?></a></li>
            <li><a href="<?= route('logout') ?>"><?= t('nav.logout') ?></a></li>
            <?php else: ?>
            <li><a href="<?= route('login') ?>"><?= t('nav.login') ?></a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <?= $content ?? '' ?>
    <ul class="toast-container">
        <?php foreach (toasts('success') as $toastText): ?>
        <li class="toast success">
            <span class="toast-icon">✔</span>
            <?= $toastText ?>
        </li>
        <?php endforeach; foreach(toasts('error') as $toastText): ?>
        <li class="toast error">
            <span class="toast-icon">⚠</span>
            <?= $toastText ?>
        </li>
        <?php endforeach; foreach(toasts('info') as $toastText): ?>
        <li class="toast info">
            <span class="toast-icon">ℹ</span>
            <?= $toastText ?>
        </li>
        <?php endforeach; ?>
    </ul>
</main>

<!-- Footer -->
<footer>
    <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(t('app.name')) ?>.</p>
    <a href="<?= route('contact') ?>"><?= t('nav.contact') ?></a>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toasts = document.querySelectorAll('.toast');
        toasts.forEach((toast) => setTimeout(toast.remove, 3500));
    });
</script>
</body>
</html>