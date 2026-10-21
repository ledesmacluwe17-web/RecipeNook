<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$recipeId = $_GET['id'] ?? null;
$userId = $_SESSION['user_id'];

if ($recipeId) {
    try {
        // TRANSACTION 3: Safe cascading recipe deletion
        $pdo->beginTransaction();

        // 1. Remove ingredients belonging to the recipe
        $delIng = $pdo->prepare("DELETE FROM ingredients WHERE recipe_id = ?");
        $delIng->execute([$recipeId]);

        // 2. Remove meal plan entries referencing the recipe (if table exists)
        $delPlan = $pdo->prepare("DELETE FROM meal_plans WHERE recipe_id = ?");
        $delPlan->execute([$recipeId]);

        // 3. Remove favorites referencing the recipe (if table exists)
        $delFav = $pdo->prepare("DELETE FROM favorites WHERE recipe_id = ?");
        $delFav->execute([$recipeId]);

        // 4. Delete the recipe record itself
        $delRecipe = $pdo->prepare("DELETE FROM recipes WHERE recipe_id = ? AND user_id = ?");
        $delRecipe->execute([$recipeId, $userId]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
    }
}

header("Location: dashboard.php");
exit();
?>