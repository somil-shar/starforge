<?php
/**
 * StarForge - User Login Page
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
$redirect = $_GET['redirect'] ?? (BASE_URL . '/dashboard/');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Security session expired. Please reload the page.';
    } else {
        $loginInput = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($loginInput) || empty($password)) {
            $error = 'Please provide both your username/email and password.';
        } else {
            try {
                $db = Database::getConnection();
                $stmt = $db->prepare("SELECT id, username, email, password_hash, full_name, role, avatar_url FROM users WHERE email = ? OR username = ? LIMIT 1");
                $stmt->execute([$loginInput, $loginInput]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    login_user($user);
                    $_SESSION['flash_success'] = "Welcome back, " . $user['username'] . "!";
                    
                    // Validate redirect URL to prevent open redirect attacks
                    $target = (strpos($redirect, BASE_URL) === 0 || strpos($redirect, '/') === 0) ? $redirect : (BASE_URL . '/dashboard/');
                    header('Location: ' . $target);
                    exit;
                } else {
                    $error = 'Invalid credentials. Please verify your username/email and password.';
                }
            } catch (Exception $e) {
                $error = 'Database connection error: ' . $e->getMessage();
            }
        }
    }
}

$pageTitle = 'Sign In';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width: 480px; margin: 4rem auto;">
    <div class="card" style="padding: 2.5rem 2rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div class="brand-icon" style="margin: 0 auto 1rem; width: 44px; height: 44px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
            </div>
            <h2 style="font-size: 1.75rem; margin-bottom: 0.5rem;">Welcome to StarForge</h2>
            <p style="color: var(--text-muted); font-size: 0.925rem;">Sign in to manage and optimize your repositories</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <!-- Quick Demo Login Pill -->
        <div style="margin-bottom: 1.5rem; padding: 0.85rem 1rem; background: rgba(130, 87, 255, 0.08); border: 1px dashed var(--border-bright); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 0.8rem; font-weight: 600; color: #C4B5FD;">Demo Account Available</div>
                <div style="font-size: 0.75rem; color: var(--text-dim); font-family: var(--font-mono);">alex@starforge.dev / StarForge2026!</div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" onclick="fillDemoCredentials()">Use Demo</button>
        </div>

        <form action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(get_csrf_token()) ?>">

            <div class="form-group">
                <label class="form-label" for="login">Email or Username</label>
                <input type="text" id="login" name="login" class="form-control" placeholder="alex@starforge.dev" required autofocus>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                    <label class="form-label" for="password" style="margin-bottom: 0;">Password</label>
                    <a href="<?= BASE_URL ?>/auth/forgot-password.php" style="font-size: 0.8rem; color: var(--accent-cyan);">Forgot?</a>
                </div>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 0.75rem;">
                Sign In to Dashboard
            </button>
        </form>

        <div style="text-align: center; margin-top: 2rem; font-size: 0.9rem; color: var(--text-muted);">
            Don't have an account? <a href="<?= BASE_URL ?>/auth/register.php" style="color: var(--accent-cyan); font-weight: 600;">Sign up free</a>
        </div>
    </div>
</div>

<script>
function fillDemoCredentials() {
    document.getElementById('login').value = 'alex@starforge.dev';
    document.getElementById('password').value = 'StarForge2026!';
    showToast('Demo credentials inserted!', 'success');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
