<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= htmlspecialchars(isset($title) ? $title : config('APP.NAME')) ?></title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
<!-- Header -->
<header>
    <img src="logo.png" alt="University Logo">
    <nav>
        <a href="<?= route('home') ?>">Home</a>
        <a href="<?= route('event.list') ?>">Events</a>
        <a href="#">About</a>
        <a href="#">Contact</a>
    </nav>
</header>

<main>
    <?= $content ?? '' ?>
</main>

<!-- Footer -->
<footer>
    <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(config('APP.NAME')) ?>. All rights reserved.</p>
    <a href="#">Privacy Policy</a>
    <a href="#">Contact Us</a>
</footer>
</body>
</html>