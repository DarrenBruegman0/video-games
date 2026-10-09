<?php
    define('ALLOWED_CONSOLES', [
        'PlayStation 5',
        'PlayStation 4',
        'Xbox Series X/S',
        'Xbox One',
        'Nintendo Switch',
        'PC'
    ]);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = [];
        $inventoryCode = trim($_POST['inventory_code']) ?? '';
        $gameName = trim($_POST['game_name']) ?? '';
        $console = trim($_POST['console']) ?? '';
        $priceInput = trim($_POST['price']) ?? 0;
        $image = $_FILES['game_image'] ?? null;

        if (!preg_match('/^GAME-[0-9]{4}$/', $inventoryCode)) {
            $errors['inventory_code'] = 'Use the format GAME-1234.';
        }
        if ($gameName === '') {
            $errors['game_name'] = 'Game name is required.';
        }
        if (!in_array($console, ALLOWED_CONSOLES, true)) {
            $errors['console'] = 'Select a valid console.';
        }
        if (
            $priceInput === '' ||
            !is_numeric($priceInput) ||
            !is_finite((float) $priceInput) ||
            (float) $priceInput <= 0
        ) {
            $errors['price'] = 'Enter a valid price greater than zero.';
        } else {
            $price = (float) $priceInput;
        }

        $gamesFile = __DIR__ . '/games/games.csv';

        if (!is_dir(dirname($gamesFile))) {
            mkdir(dirname($gamesFile), 0777, true);
        }

        $fileHandle = fopen($gamesFile, 'a+');

        rewind($fileHandle);

        while (($row = fgetcsv($fileHandle)) !== false) {
            if (($row[0] ?? '') === $inventoryCode) {
                $errors['inventory_code'] = 'This inventory code already exists.';
                break;
            }
        }
        if (empty($errors)) {
            $upDIR = __DIR__ . '/uploads/';
            if (!is_dir($upDIR)) {
                mkdir($upDIR, 0777, true);
            }
            $imagePath = $upDIR . $inventoryCode . '_' . floor(microtime(true) * 1000) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            if (move_uploaded_file($image['tmp_name'], $imagePath)) {
                $gameData = [
                    $inventoryCode,
                    $gameName,
                    $console,
                    number_format($price, 2),
                    basename($imagePath)
                ];
            fputcsv($fileHandle, $gameData);


            fclose($fileHandle);
        }
    }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>GAME PROJ</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

    <!-- Shared navigation -->
    <?php require 'includes/navigation.php'; ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">

                <div class="mb-4">
                    <h1 class="fw-bold">Add a Game</h1>
                    <p class="text-secondary">
                        Add a video game to your inventory.
                    </p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">

                        <form
                            action="index.php"
                            method="POST"
                            enctype="multipart/form-data"
                        >
                            <!-- Inventory Code -->
                            <div class="mb-4">
                                <label
                                    for="inventory_code"
                                    class="form-label fw-semibold"
                                >
                                    Inventory Code
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="inventory_code"
                                    name="inventory_code"
                                    placeholder="GAME-1234"
                                    required
                                >

                                <div class="form-text">
                                    Format: GAME- followed by four digits.
                                </div>
                                <?php if (isset($errors['inventory_code'])): ?>
                                    <div class="text-danger">
                                        <?= htmlspecialchars($errors['inventory_code']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Video Game Name -->
                            <div class="mb-4">
                                <label
                                    for="game_name"
                                    class="form-label fw-semibold"
                                >
                                    Video Game Name
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="game_name"
                                    name="game_name"
                                    placeholder="Enter the game name"
                                    required
                                >
                                <?php if (isset($errors['game_name'])): ?>
                                    <div class="text-danger">
                                        <?= htmlspecialchars($errors['game_name']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Console -->
                            <div class="mb-4">
                                <label
                                    for="console"
                                    class="form-label fw-semibold"
                                >
                                    Console
                                </label>

                                <select
                                    class="form-select"
                                    id="console"
                                    name="console"
                                    required
                                >
                                    <option value="" selected disabled>
                                        Select a console
                                    </option>
                                    <option value="PlayStation 5">
                                        PlayStation 5
                                    </option>
                                    <option value="PlayStation 4">
                                        PlayStation 4
                                    </option>
                                    <option value="Xbox Series X/S">
                                        Xbox Series X/S
                                    </option>
                                    <option value="Xbox One">
                                        Xbox One
                                    </option>
                                    <option value="Nintendo Switch">
                                        Nintendo Switch
                                    </option>
                                    <option value="PC">
                                        PC
                                    </option>
                                </select>
                                <?php if (isset($errors['console'])): ?>
                                    <div class="text-danger">
                                        <?= htmlspecialchars($errors['console']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Price -->
                            <div class="mb-4">
                                <label
                                    for="price"
                                    class="form-label fw-semibold"
                                >
                                    Price (USD)
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">$</span>

                                    <input
                                        type="number"
                                        class="form-control"
                                        id="price"
                                        name="price"
                                        placeholder="59.99"
                                        min="0"
                                        step="0.01"
                                        required
                                    >
                                </div>
                                <?php if (isset($errors['price'])): ?>
                                    <div class="text-danger">
                                        <?= htmlspecialchars($errors['price']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Game Image -->
                            <div class="mb-4">
                                <label
                                    for="game_image"
                                    class="form-label fw-semibold"
                                >
                                    Game Image
                                </label>

                                <input
                                    type="file"
                                    class="form-control"
                                    id="game_image"
                                    name="game_image"
                                    accept="image/*"
                                    required
                                >

                                <div class="form-text">
                                    Select an image of the game.
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="d-grid">
                                <button
                                    type="submit"
                                    class="btn btn-primary btn-lg"
                                >
                                    Add Game
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

</body>
</html>
