<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Details</title>
</head>
<body>
<h1><?php echo htmlspecialchars($event['name']); ?></h1>
<p><?php echo htmlspecialchars($event['description']); ?></p>
<p>Category: <?php echo htmlspecialchars($event['category_name']); ?></p>
<p>Starts: <?php echo htmlspecialchars($event['start_date']); ?></p>
<p>Ends: <?php echo htmlspecialchars($event['end_date']); ?></p>
</body>
</html>