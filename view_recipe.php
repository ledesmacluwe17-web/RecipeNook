<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$recipeId = $_GET['id'] ?? null;
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT r.*, c.category_name, d.level_name, u.username FROM recipes r 
                       JOIN categories c ON r.category_id = c.category_id 
                       JOIN difficulties d ON r.diff_id = d.diff_id 
                       JOIN users u ON r.user_id = u.user_id
                       WHERE r.recipe_id = ?");
$stmt->execute([$recipeId]);
$recipe = $stmt->fetch();

if (!$recipe) {
    echo "<div class='container my-5 text-center'><h3>Recipe not found!</h3><a href='dashboard.php' class='btn btn-dark mt-3'>Back</a></div>";
    exit();
}

$ingStmt = $pdo->prepare("SELECT * FROM ingredients WHERE recipe_id = ?");
$ingStmt->execute([$recipeId]);
$ingredients = $ingStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><= htmlspecialchars($recipe['title']) ?> - RecipeNook</title>
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
        .badge-glass {
            background: rgba(251, 207, 232, 0.6);
            color: #be185d;
            border: 1px solid rgba(255, 255, 255, 0.6);
            font-weight: 600;
            font-size: 0.8rem;
            border-radius: 20px;
            padding: 6px 14px;
        }
        .glass-table {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            overflow: hidden;
        }
    </style>
</head>
<body class="py-5">
<div class="container px-3" style="max-width: 750px;">
    <div class="glass-card p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge badge-glass"><?= htmlspecialchars($recipe['category_name']) ?></span>
            <span class="text-muted small">By <strong><?= htmlspecialchars($recipe['username']) ?></strong> | Prep: <?= $recipe['prep_time_mins'] ?> mins | Level: <?= $recipe['level_name'] ?></span>
        </div>
        <h1 class="fw-bold text-dark mb-4"><?= htmlspecialchars($recipe['title']) ?></h1>
        
        <h5 class="fw-bold text-dark mb-3">Ingredients</h5>
        <?php if (count($ingredients) > 0): ?>
            <div class="table-responsive mb-4 glass-table border border-white">
                <table class="table table-hover align-middle mb-0" style="background: transparent;">
                    <thead style="background: rgba(255,255,255,0.6);">
                        <tr>
                            <th class="py-3 px-3 text-secondary">Ingredient Item</th>
                            <th class="py-3 px-3 text-secondary">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($ingredients as $ing): ?>
                            <tr style="border-color: rgba(255,255,255,0.4);">
                                <td class="px-3"><?= htmlspecialchars($ing['item_name']) ?></td>
                                <td class="px-3 fw-semibold text-secondary"><?= htmlspecialchars($ing['quantity']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted small">No ingredients listed.</p>
        <?php endif; ?>

        <h5 class="fw-bold text-dark mb-3">Instructions</h5>
        <div class="p-4 rounded-4 bg-white bg-opacity-50 mb-4 text-secondary border border-white" style="white-space: pre-line; line-height: 1.7;"><?= htmlspecialchars($recipe['instructions']) ?></div>

        <div class="d-flex gap-2 pt-3 border-top border-white">
            <a href="dashboard.php" class="btn btn-dark px-4 rounded-3 shadow-sm">Back</a>
            <?php if ($recipe['user_id'] == $userId): ?>
                <a href="edit_recipe.php?id=<?= $recipe['recipe_id'] ?>" class="btn btn-outline-primary px-4 rounded-3 bg-white bg-opacity-50">Edit</a>
                <a href="delete_recipe.php?id=<?= $recipe['recipe_id'] ?>" class="btn btn-outline-danger px-4 rounded-3 bg-white bg-opacity-50" onclick="return confirm('Delete this recipe?')">Delete</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>