<?php
/**
 * StarForge - Forgot Password Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Security session expired. Please reload.';
    } else {
        $email = trim($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            try {
                $db = Database::getConnection();
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    $up = $db->prepare("UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE id = ?");
                    $up->execute([$token, $expires, $user['id']]);
                }

                // Generic message to avoid email enumeration
                $message = "If that email exists in our records, a secure password reset link has been dispatched to your inbox.";
            } catch (Exception $e) {
                $error = 'Error processing request: ' . $e->getMessage();
            }
        }
    }
}

$pageTitle = 'Reset Password';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width: 460px; margin: 4rem auto;">
    <div class="card" style="padding: 2.5rem 2rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h2 style="font-size: 1.65rem; margin-bottom: 0.5rem;">Reset Password</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Enter your email to receive recovery instructions</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= e($message) ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(get_csrf_token()) ?>">

            <div class="form-group">
                <label class="form-label" for="email">Account Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="alex@starforge.dev" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 0.75rem;">
                Send Recovery Instructions
            </button>
        </form>

        <div style="text-align: center; margin-top: 2rem; font-size: 0.9rem; color: var(--text-muted);">
            Remembered your password? <a href="<?= BASE_URL ?>/auth/login.php" style="color: var(--accent-cyan); font-weight: 600;">Back to sign in</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
