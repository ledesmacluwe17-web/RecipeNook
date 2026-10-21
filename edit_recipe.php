<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$recipeId = $_GET['id'] ?? null;
$userId = $_SESSION['user_id'];
$error = '';

$stmt = $pdo->prepare("SELECT * FROM recipes WHERE recipe_id = ? AND user_id = ?");
$stmt->execute([$recipeId, $userId]);
$recipe = $stmt->fetch();

if (!$recipe) {
    header("Location: dashboard.php");
    exit();
}

$categories = $pdo->prepare("SELECT * FROM categories WHERE user_id = ?");
$categories->execute([$userId]);
$catList = $categories->fetchAll();
$difficulties = $pdo->query("SELECT * FROM difficulties")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $categoryId = $_POST['category_id'];
    $diffId = $_POST['diff_id'];
    $prepTime = $_POST['prep_time_mins'];
    $instructions = trim($_POST['instructions']);

    try {
        $update = $pdo->prepare("UPDATE recipes SET category_id = ?, diff_id = ?, title = ?, prep_time_mins = ?, instructions = ? WHERE recipe_id = ? AND user_id = ?");
        $update->execute([$categoryId, $diffId, $title, $prepTime, $instructions, $recipeId, $userId]);

        header("Location: view_recipe.php?id=" . $recipeId);
        exit();
    } catch (Exception $e) {
        $error = "Failed to update recipe: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Recipe - RecipeNook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #fbcfe8 0%, #fed7aa 50%, #e0e7ff 100%);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        }
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 12px;
            font-size: 0.9rem;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.9);
            border-color: #db2777;
            box-shadow: 0 0 0 3px rgba(219, 39, 119, 0.12);
        }
        .btn-glass-pink {
            background: rgba(219, 39, 119, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: white;
            border-radius: 12px;
            font-weight: 500;
            padding: 12px;
            transition: all 0.3s;
        }
        .btn-glass-pink:hover {
            background: rgba(190, 24, 93, 0.95);
            color: white;
        }
    </style>
</head>
<body class="py-5">
<div class="container px-3" style="max-width: 650px;">
    <div class="glass-card p-4 p-md-5">
        <h3 class="fw-bold text-dark mb-4">Edit Recipe</h3>
        <?php if($error): ?><div class="alert alert-danger py-2 small rounded-3" style="background: rgba(254, 226, 226, 0.8); border: none;"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Recipe Title</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($recipe['title']) ?>" required>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-semibold text-secondary">Category</label>
                    <select name="category_id" class="form-select" required>
                        <?php foreach($catList as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>" <?= $cat['category_id'] == $recipe['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-semibold text-secondary">Difficulty</label>
                    <select name="diff_id" class="form-select" required>
                        <?php foreach($difficulties as $diff): ?>
                            <option value="<?= $diff['diff_id'] ?>" <?= $diff['diff_id'] == $recipe['diff_id'] ? 'selected' : '' ?>><?= htmlspecialchars($diff['level_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-semibold text-secondary">Prep Time (mins)</label>
                    <input type="number" name="prep_time_mins" class="form-control" value="<?= $recipe['prep_time_mins'] ?>" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Instructions</label>
                <textarea name="instructions" class="form-control" rows="5" required><?= htmlspecialchars($recipe['instructions']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-glass-pink w-100">Update Changes</button>
            <a href="dashboard.php" class="btn btn-link text-muted w-100 mt-2 text-decoration-none small">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>