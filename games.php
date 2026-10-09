<?php
$csvPath = __DIR__ . '/games/games.csv';
$games = [];

if (file_exists($csvPath)) {
    $file = fopen($csvPath, 'r');

    while (($row = fgetcsv($file)) !== false) {
        $games[] = $row;
    }

    fclose($file);
}

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>GAME PROJ | Game Listings</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<?php require __DIR__ . '/includes/navigation.php'; ?>

<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold">Game Listings</h1>
            <p class="text-secondary mb-0">
                Browse your game inventory.
            </p>
        </div>

        <a href="index.php" class="btn btn-primary">
            + Add a Game
        </a>
    </div>

    <?php if (empty($games)): ?>
        <div class="alert alert-info text-center">
            No games have been added yet.
            <a href="index.php" class="alert-link">Add your first game.</a>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Image</th>
                            <th>Inventory Code</th>
                            <th>Game Name</th>
                            <th>Console</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($games as $game): ?>
                            <tr>
                                <td>
                                    <img
                                        src="uploads/<?= basename($game[4]) ?>"
                                        alt="<?= $game[1] ?>"
                                        width="70"
                                        height="70"
                                        style="object-fit: cover;"
                                    >
                                </td>
                                <td><?= e($game[0]) ?></td>
                                <td><?= e($game[1]) ?></td>
                                <td><?= e($game[2]) ?></td>
                                <td>
                                    $<?= number_format((float) $game[3], 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</main>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>
</body>
</html>