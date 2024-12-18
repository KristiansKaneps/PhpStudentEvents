<!doctype html>
<html lang="en">
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
    <title><?= htmlspecialchars(isset($title) ? $title : config('APP.NAME')) ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
<!-- Header -->
<header>
    <img src="/assets/images/logo.svg" alt="<?= config('APP.NAME') ?> Logo">
    <nav>
        <a href="<?= route('home') ?>">Home</a>
        <a href="<?= route('events') ?>">Events</a>
        <a href="<?= route('about') ?>">About</a>
        <a href="<?= route('contact') ?>">Contact</a>
    </nav>
</header>

<main>
    <?= $content ?? '' ?>
</main>

<!-- Footer -->
<footer>
    <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(config('APP.NAME')) ?>.</p>
    <a href="<?= route('contact') ?>">Contact Us</a>
</footer>
</body>
</html>