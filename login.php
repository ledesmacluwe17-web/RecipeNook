<?php
require 'db.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - RecipeNook</title>
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
        .form-control {
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 12px;
            font-size: 0.9rem;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.9);
            border-color: #db2777;
            box-shadow: 0 0 0 3px rgba(219, 39, 119, 0.12);
        }
        .btn-glass {
            background: rgba(219, 39, 119, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: white;
            border-radius: 12px;
            padding: 12px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-glass:hover {
            background: rgba(190, 24, 93, 0.95);
            color: white;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
<div class="container px-3" style="max-width: 420px;">
    <div class="glass-card p-4 p-md-5">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark">Welcome Back</h3>
            <p class="text-muted small">Access your personal recipe box</p>
        </div>
        <?php if(isset($_GET['registered'])): ?><div class="alert alert-success py-2 small rounded-3" style="background: rgba(220, 252, 231, 0.8); border: none;">Registration successful! Please login.</div><?php endif; ?>
        <?php if($error): ?><div class="alert alert-danger py-2 small rounded-3" style="background: rgba(254, 226, 226, 0.8); border: none;"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter username" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-glass w-100 mb-3">Login</button>
            <p class="text-center small text-muted">New user? <a href="register.php" class="text-decoration-none fw-semibold" style="color: #db2777;">Create an account</a></p>
        </form>
    </div>
</div>
</body>
</html>