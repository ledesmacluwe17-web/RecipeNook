<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT r.*, c.category_name, d.level_name, u.username FROM recipes r 
                       JOIN categories c ON r.category_id = c.category_id 
                       JOIN difficulties d ON r.diff_id = d.diff_id 
                       JOIN users u ON r.user_id = u.user_id
                       WHERE r.user_id = ? ORDER BY r.recipe_id DESC");
$stmt->execute([$userId]);
$myRecipes = $stmt->fetchAll();

$commStmt = $pdo->prepare("SELECT r.*, c.category_name, d.level_name, u.username FROM recipes r 
                           JOIN categories c ON r.category_id = c.category_id 
                           JOIN difficulties d ON r.diff_id = d.diff_id 
                           JOIN users u ON r.user_id = u.user_id
                           WHERE r.user_id != ? ORDER BY r.recipe_id DESC");
$commStmt->execute([$userId]);
$communityRecipes = $commStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - RecipeNook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #fbcfe8 0%, #fed7aa 50%, #e0e7ff 100%);
            min-height: 100vh;
            color: #1e293b;
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.7);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .glass-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
            background: rgba(255, 255, 255, 0.75);
        }
        .btn-glass-pink {
            background: rgba(219, 39, 119, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: white;
            border-radius: 12px;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.3s;
        }
        .btn-glass-pink:hover {
            background: rgba(190, 24, 93, 0.95);
            color: white;
        }
        .badge-glass {
            background: rgba(251, 207, 232, 0.6);
            color: #be185d;
            border: 1px solid rgba(255, 255, 255, 0.6);
            font-weight: 600;
            font-size: 0.75rem;
            border-radius: 20px;
            padding: 5px 12px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg glass-nav px-4 py-3 sticky-top">
        <a class="navbar-brand fw-bold" href="dashboard.php" style="color: #be185d;">🍲 RecipeNook</a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="small text-secondary">Hello, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong></span>
            <a href="logout.php" class="btn btn-sm btn-outline-dark rounded-pill px-3 border-0" style="background: rgba(255,255,255,0.4);">Logout</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1 text-dark">Your Recipe Dashboard</h2>
                <p class="text-muted small mb-0">Your personal cloud recipe keeper</p>
            </div>
            <a href="add_recipe.php" class="btn btn-glass-pink shadow-sm">+ Add New Recipe</a>
        </div>

        <div class="row mb-5">
            <?php if (count($myRecipes) > 0): ?>
                <?php foreach ($myRecipes as $recipe): ?>
                    <div class="col-md-4 mb-4">
                        <div class="glass-card h-100 p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge badge-glass"><?= htmlspecialchars($recipe['category_name']); ?></span>
                                <span class="text-muted small"><?= $recipe['prep_time_mins']; ?> mins</span>
                            </div>
                            <h5 class="fw-bold mb-2 text-dark"><?= htmlspecialchars($recipe['title']); ?></h5>
                            <p class="text-muted small flex-grow-1"><?= substr(htmlspecialchars($recipe['instructions']), 0, 75); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-white">
                                <span class="badge bg-light text-secondary border"><?= htmlspecialchars($recipe['level_name']); ?></span>
                                <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                    <a href="view_recipe.php?id=<?= $recipe['recipe_id']; ?>" class="btn btn-sm btn-light border-0">View</a>
                                    <a href="edit_recipe.php?id=<?= $recipe['recipe_id']; ?>" class="btn btn-sm btn-light border-0 text-primary">Edit</a>
                                    <a href="delete_recipe.php?id=<?= $recipe['recipe_id']; ?>" class="btn btn-sm btn-light border-0 text-danger" onclick="return confirm('Delete this recipe?')">Del</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="glass-card p-4 text-center text-muted">
                        No recipes added yet. Click "+ Add New Recipe" to start!
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <h3 class="fw-bold mb-4 text-dark">Community Recipes</h3>
        <div class="row">
            <?php if (count($communityRecipes) > 0): ?>
                <?php foreach ($communityRecipes as $recipe): ?>
                    <div class="col-md-4 mb-4">
                        <div class="glass-card h-100 p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge badge-glass"><?= htmlspecialchars($recipe['category_name']); ?></span>
                                <span class="badge bg-white text-dark border">By <?= htmlspecialchars($recipe['username']); ?></span>
                            </div>
                            <h5 class="fw-bold mb-2 text-dark"><?= htmlspecialchars($recipe['title']); ?></h5>
                            <p class="text-muted small flex-grow-1"><?= substr(htmlspecialchars($recipe['instructions']), 0, 75); ?>...</p>
                            <div class="mt-3 pt-3 border-top border-white d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><?= $recipe['prep_time_mins']; ?> mins | <?= htmlspecialchars($recipe['level_name']); ?></span>
                                <a href="view_recipe.php?id=<?= $recipe['recipe_id']; ?>" class="btn btn-sm btn-dark px-3 rounded-pill shadow-sm">View Recipe</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="glass-card p-4 text-center text-muted">No community recipes available yet.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>