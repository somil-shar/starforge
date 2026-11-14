<?php
/**
 * StarForge - User Registration Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/dashboard/');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Security session expired. Please reload the page.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            $error = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please provide a valid email address.';
        } elseif (!preg_match('/^[a-zA-Z0-9_-]{3,30}$/', $username)) {
            $error = 'Username must be 3-30 characters long and contain only letters, numbers, hyphens or underscores.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            try {
                $db = Database::getConnection();
                
                // Check uniqueness
                $checkStmt = $db->prepare("SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1");
                $checkStmt->execute([$email, $username]);
                if ($checkStmt->fetch()) {
                    $error = 'A user with that username or email already exists.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $ins = $db->prepare("INSERT INTO users (username, email, password_hash, full_name, avatar_url, role, created_at) VALUES (?, ?, ?, ?, ?, 'user', ?)");
                    $defaultAvatar = "https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150";
                    $ins->execute([$username, $email, $hash, $fullName, $defaultAvatar, date('Y-m-d H:i:s')]);
                    $userId = (int)$db->lastInsertId();

                    login_user([
                        'id' => $userId,
                        'username' => $username,
                        'email' => $email,
                        'role' => 'user'
                    ]);

                    $_SESSION['flash_success'] = "Account created! Welcome to StarForge.";
                    header('Location: ' . BASE_URL . '/dashboard/');
                    exit;
                }
            } catch (Exception $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

$pageTitle = 'Create Account';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width: 500px; margin: 3.5rem auto;">
    <div class="card" style="padding: 2.5rem 2rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div class="brand-icon" style="margin: 0 auto 1rem; width: 44px; height: 44px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
            </div>
            <h2 style="font-size: 1.75rem; margin-bottom: 0.5rem;">Create Your Account</h2>
            <p style="color: var(--text-muted); font-size: 0.925rem;">Join developers optimizing repositories and earning genuine stars</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(get_csrf_token()) ?>">

            <div class="form-group">
                <label class="form-label" for="username">GitHub Username or Handle *</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="octocat" value="<?= e($_POST['username'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Ada Lovelace" value="<?= e($_POST['full_name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="you@company.dev" value="<?= e($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password *</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="At least 8 characters" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 0.75rem;">
                Create Free Account
            </button>
        </form>

        <div style="text-align: center; margin-top: 2rem; font-size: 0.9rem; color: var(--text-muted);">
            Already have an account? <a href="<?= BASE_URL ?>/auth/login.php" style="color: var(--accent-cyan); font-weight: 600;">Sign in here</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
