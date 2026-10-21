<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RecipeNook - Glassmorphism Edition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #fbcfe8 0%, #fed7aa 50%, #e0e7ff 100%);
            min-height: 100vh;
            color: #1e293b;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        }
        .btn-glass-primary {
            background: rgba(219, 39, 119, 0.85);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: white;
            border-radius: 14px;
            padding: 12px 28px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-glass-primary:hover {
            background: rgba(190, 24, 93, 0.95);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(219, 39, 119, 0.25);
        }
        .btn-glass-outline {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(219, 39, 119, 0.3);
            color: #db2777;
            border-radius: 14px;
            padding: 12px 28px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-glass-outline:hover {
            background: rgba(255, 255, 255, 0.8);
            color: #be185d;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="container text-center px-3">
        <div class="glass-card p-5 mx-auto" style="max-width: 600px;">
            <span class="badge rounded-pill px-3 py-2 mb-3" style="background: rgba(251, 207, 232, 0.6); color: #be185d; font-weight: 600; border: 1px solid rgba(255,255,255,0.8);">✨ Minimalist Kitchen Assistant</span>
            <h1 class="display-5 fw-bold mb-3" style="color: #0f172a;">RecipeNook</h1>
            <p class="text-muted mb-4" style="font-size: 0.95rem;">Organize your favorite recipes, plan weekly meals, and eliminate daily decision fatigue[cite: 2] in a serene digital space.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="login.php" class="btn btn-glass-primary">Login</a>
                <a href="register.php" class="btn btn-glass-outline">Register</a>
            </div>
        </div>
    </div>
</body>
</html>