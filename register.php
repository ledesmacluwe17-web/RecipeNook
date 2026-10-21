<?php
require 'db.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        // TRANSACTION 2: User registration + Default category provisioning
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        $userId = $pdo->lastInsertId();

        $catStmt = $pdo->prepare("INSERT INTO categories (user_id, category_name) VALUES (?, 'General')");
        $catStmt->execute([$userId]);

        $pdo->commit();

        header("Location: login.php?registered=success");
        exit();
    } catch (\PDOException $e) {
        $pdo->rollBack();
        $error = "Username or Email already exists.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - RecipeNook</title>
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
            <h3 class="fw-bold text-dark">Create Account</h3>
            <p class="text-muted small">Begin your culinary journey</p>
        </div>
        <?php if($error): ?><div class="alert alert-danger py-2 small rounded-3" style="background: rgba(254, 226, 226, 0.8); border: none;"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Choose a username" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-glass w-100 mb-3">Sign Up</button>
            <p class="text-center small text-muted">Already registered? <a href="login.php" class="text-decoration-none fw-semibold" style="color: #db2777;">Login here</a></p>
        </form>
    </div>
</div>
</body>
</html>