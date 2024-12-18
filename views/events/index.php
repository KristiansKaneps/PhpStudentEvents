<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trending Events</title>
</head>
<body>
<h1>Trending Events</h1>
<ul>
    <?php foreach ($events as $event): ?>
        <li>
            <h3><?php echo htmlspecialchars($event['name']); ?></h3>
            <p><?php echo htmlspecialchars($event['description']); ?></p>
            <p>Starts: <?php echo htmlspecialchars($event['start_date']); ?></p>
        </li>
    <?php endforeach; ?>
</ul>
</body>
</html>