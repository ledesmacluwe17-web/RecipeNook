<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];$error = '';

$categories =$pdo->prepare("SELECT * FROM categories WHERE user_id = ?");
$categories->execute([$userId]);
$catList =$categories->fetchAll();

$difficulties =$pdo->query("SELECT * FROM difficulties")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $categoryId =$_POST['category_id'];
    $diffId =$_POST['diff_id'];
    $prepTime =$_POST['prep_time_mins'];
    $instructions = trim($_POST['instructions']);
    
    $ingredients =$_POST['ingredients'] ?? [];
    $quantities =$_POST['quantities'] ?? [];

    try {
        $pdo->beginTransaction();

        $stmt =$pdo->prepare("INSERT INTO recipes (user_id, category_id, diff_id, title, prep_time_mins, instructions) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId,$categoryId, $diffId,$title, $prepTime,$instructions]);
        $recipeId =$pdo->lastInsertId();

        if (!empty($ingredients)) {
            $ingStmt =$pdo->prepare("INSERT INTO ingredients (recipe_id, item_name, quantity) VALUES (?, ?, ?)");
            for ($i = 0; $i < count($ingredients);$i++) {
                $itemName = trim($ingredients[$i]);$itemQty = trim($quantities[$i] ?? '');
                
                if (!empty($itemName)) {
                    $ingStmt->execute([$recipeId, $itemName,$itemQty]);
                }
            }
        }

        $pdo->commit();
        header("Location: dashboard.php");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();$error = "Failed to save recipe: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Recipe - RecipeNook</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark mb-0">Add New Recipe</h3>
            <span class="badge bg-white text-secondary border">Transaction Safe</span>
        </div>
        <?php if($error): ?><div class="alert alert-danger py-2 small rounded-3" style="background: rgba(254, 226, 226, 0.8); border: none;"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Recipe Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Creamy Carbonara" required>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-semibold text-secondary">Category</label>
                    <select name="category_id" class="form-select" required>
                        <?php foreach($catList as$cat): ?>
                            <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-semibold text-secondary">Difficulty</label>
                    <select name="diff_id" class="form-select" required>
                        <?php foreach($difficulties as$diff): ?>
                            <option value="<?= $diff['diff_id'] ?>"><?= htmlspecialchars($diff['level_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-semibold text-secondary">Prep Time (mins)</label>
                    <input type="number" name="prep_time_mins" class="form-control" placeholder="15" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Instructions</label>
                <textarea name="instructions" class="form-control" rows="5" placeholder="Step 1..." required></textarea>
            </div>

            <hr class="my-4 text-muted opacity-25">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">Ingredients</h5>
                <button type="button" id="add-ingredient-btn" class="btn btn-sm btn-outline-secondary rounded-pill px-3 border-0 bg-white shadow-sm">+ Add Ingredient</button>
            </div>
            
            <div id="ingredients-container">
                <div class="row mb-2 ingredient-row">
                    <div class="col-7"><input type="text" name="ingredients[]" class="form-control" placeholder="Item Name" required></div>
                    <div class="col-5"><input type="text" name="quantities[]" class="form-control" placeholder="Quantity" required></div>
                </div>
            </div>

            <button type="submit" class="btn btn-glass-pink w-100 mt-4">Save Recipe</button>
            <a href="dashboard.php" class="btn btn-link text-muted w-100 mt-2 text-decoration-none small">Cancel & Return</a>
        </form>
    </div>
</div>

<script>
document.getElementById('add-ingredient-btn').addEventListener('click', function() {
    const container = document.getElementById('ingredients-container');
    const newRow = document.createElement('div');
    newRow.classList.add('row', 'mb-2', 'ingredient-row');
    newRow.innerHTML = `
        <div class="col-7"><input type="text" name="ingredients[]" class="form-control" placeholder="Item Name"></div>
        <div class="col-5">
            <div class="input-group">
                <input type="text" name="quantities[]" class="form-control" placeholder="Quantity" style="border-radius: 12px 0 0 12px;">
                <button type="button" class="btn btn-outline-danger remove-row" style="border-radius: 0 12px 12px 0; background: rgba(254,226,226,0.8); border-color: rgba(255,255,255,0.9); color: #dc2626;">×</button>
            </div>
        </div>
    `;
    container.appendChild(newRow);
});

document.getElementById('ingredients-container').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('.ingredient-row').remove();
    }
});
</script>
</body>
</html>