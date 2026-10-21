<?php
session_start();
// Unset all session variables
$_SESSION = array();
// Destroy the session
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logged Out - RecipeNook</title>
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
        .btn-glass-primary {
            background: rgba(219, 39, 119, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: white;
            border-radius: 12px;
            padding: 12px 28px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-glass-primary:hover {
            background: rgba(190, 24, 93, 0.95);
            color: white;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="container text-center px-3" style="max-width: 450px;">
        <div class="glass-card p-5">
            <div class="mb-3 fs-1"></div>
            <h3 class="fw-bold text-dark mb-2">You've Been Logged Out</h3>
            <p class="text-muted small mb-4">Thank you for using RecipeNook to organize your meals today.</p>
            <div class="d-grid gap-2">
                <a href="login.php" class="btn btn-glass-primary">Log Back In</a>
                <a href="index.php" class="btn btn-sm btn-link text-muted text-decoration-none mt-2">Return to Home</a>
            </div>
        </div>
    </div>
</body>
</html>