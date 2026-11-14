<?php
/**
 * StarForge - User Profile Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();
$user = get_current_user_data();
$db = Database::getConnection();

$message = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Security session expired. Please reload.';
    } else {
        $fullName = trim($_POST['full_name'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $githubUrl = trim($_POST['github_url'] ?? '');
        $avatarUrl = trim($_POST['avatar_url'] ?? '');

        try {
            $stmt = $db->prepare("UPDATE users SET full_name = ?, bio = ?, github_url = ?, avatar_url = ?, updated_at = ? WHERE id = ?");
            $stmt->execute([$fullName, $bio, $githubUrl, $avatarUrl, date('Y-m-d H:i:s'), $user['id']]);
            $message = 'Profile updated successfully!';
            $user = get_current_user_data(); // refresh
        } catch (Exception $e) {
            $error = 'Error updating profile: ' . $e->getMessage();
        }
    }
}

// Fetch user's analyzed reports
$reportsStmt = $db->prepare("
    SELECT r.id AS report_id, r.overall_score, r.created_at, repo.full_name, repo.language, repo.stars_count, repo.html_url
    FROM analysis_reports r
    JOIN repositories repo ON r.repository_id = repo.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$reportsStmt->execute([$user['id']]);
$userReports = $reportsStmt->fetchAll();

// Fetch user's generated READMEs
$readmeStmt = $db->prepare("SELECT * FROM readme_generations WHERE user_id = ? ORDER BY created_at DESC");
$readmeStmt->execute([$user['id']]);
$userReadmes = $readmeStmt->fetchAll();

$pageTitle = 'Developer Profile';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?= e($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <!-- Profile Header Card -->
    <div class="card" style="margin-bottom: 2.5rem; padding: 2.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 2rem;">
            <div style="display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap;">
                <img src="<?= e($user['avatar_url'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150') ?>" alt="avatar" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent-violet);">
                <div>
                    <h1 style="font-size: 1.85rem; margin-bottom: 0.25rem;"><?= e($user['full_name'] ?: $user['username']) ?></h1>
                    <div style="color: var(--text-dim); font-size: 0.9rem; margin-bottom: 0.5rem;">
                        @<?= e($user['username']) ?> &bull; Joined <?= date('M Y', strtotime($user['created_at'])) ?>
                    </div>
                    <?php if (!empty($user['bio'])): ?>
                        <p style="color: var(--text-secondary); font-size: 0.95rem; max-width: 600px;"><?= e($user['bio']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEditProfile()">
                    ✏ Edit Profile
                </button>
                <?php if (!empty($user['github_url'])): ?>
                    <a href="<?= e($user['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">
                        GitHub Profile ↗
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Hidden Profile Edit Form -->
        <div id="profile-edit-box" style="display: none; margin-top: 2rem; border-top: 1px solid var(--border-subtle); padding-top: 2rem;">
            <h3 style="font-size: 1.15rem; margin-bottom: 1.25rem;">Edit Profile</h3>
            <form action="" method="POST">
                <input type="hidden" name="csrf_token" value="<?= e(get_csrf_token()) ?>">
                <input type="hidden" name="update_profile" value="1">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" value="<?= e($user['full_name']) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="avatar_url">Avatar Image URL</label>
                        <input type="url" id="avatar_url" name="avatar_url" class="form-control" value="<?= e($user['avatar_url']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="github_url">GitHub Profile URL</label>
                    <input type="url" id="github_url" name="github_url" class="form-control" value="<?= e($user['github_url']) ?>" placeholder="https://github.com/username">
                </div>

                <div class="form-group">
                    <label class="form-label" for="bio">Bio</label>
                    <textarea id="bio" name="bio" class="form-control" style="min-height: 80px;"><?= e($user['bio']) ?></textarea>
                </div>

                <div style="display: flex; gap: 0.75rem;">
                    <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEditProfile()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Profile Tabs -->
    <div style="display: flex; gap: 1rem; margin-bottom: 2rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;">
        <button class="btn btn-sm btn-primary" onclick="showTab('tab-audits', this)">Audited Repositories (<?= count($userReports) ?>)</button>
        <button class="btn btn-sm btn-secondary" onclick="showTab('tab-readmes', this)">Saved READMEs (<?= count($userReadmes) ?>)</button>
    </div>

    <!-- Tab 1: Audited Repositories -->
    <div id="tab-audits" class="profile-tab-content">
        <?php if (empty($userReports)): ?>
            <div class="card" style="text-align: center; padding: 3rem 1rem;">
                <p style="color: var(--text-muted); font-size: 0.95rem;">You haven't audited any repositories yet.</p>
                <a href="<?= BASE_URL ?>/dashboard/analyzer.php" class="btn btn-primary btn-sm" style="margin-top: 1rem;">Run Audit</a>
            </div>
        <?php else: ?>
            <div class="grid-2">
                <?php foreach ($userReports as $rep): ?>
                    <?php $tier = get_score_tier((int)$rep['overall_score']); ?>
                    <div class="card card-interactive">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <h3 style="font-size: 1.15rem; font-family: var(--font-mono);"><?= e($rep['full_name']) ?></h3>
                            <span style="font-family: var(--font-mono); font-weight: 800; color: <?= $tier['color'] ?>; font-size: 1.15rem;">
                                <?= (int)$rep['overall_score'] ?>/100
                            </span>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-dim); margin-bottom: 1rem; display: flex; gap: 0.75rem;">
                            <span><?= e($rep['language'] ?: 'Mixed') ?></span>
                            <span>★ <?= format_number((int)$rep['stars_count']) ?></span>
                            <span><?= time_ago($rep['created_at']) ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="tech-badge" style="color: <?= $tier['color'] ?>;"><?= $tier['badge'] ?></span>
                            <a href="<?= BASE_URL ?>/dashboard/analyzer.php?repo=<?= urlencode($rep['full_name']) ?>" class="btn btn-secondary btn-sm">
                                Open Audit &rarr;
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tab 2: Saved READMEs -->
    <div id="tab-readmes" class="profile-tab-content" style="display: none;">
        <?php if (empty($userReadmes)): ?>
            <div class="card" style="text-align: center; padding: 3rem 1rem;">
                <p style="color: var(--text-muted); font-size: 0.95rem;">No saved READMEs generated yet.</p>
                <a href="<?= BASE_URL ?>/dashboard/readme-generator.php" class="btn btn-primary btn-sm" style="margin-top: 1rem;">Open README Studio</a>
            </div>
        <?php else: ?>
            <div class="grid-2">
                <?php foreach ($userReadmes as $r): ?>
                    <div class="card">
                        <h4 style="font-size: 1.15rem; margin-bottom: 0.35rem;"><?= e($r['project_name']) ?></h4>
                        <div style="font-size: 0.8rem; color: var(--text-dim); margin-bottom: 1rem;">Created <?= time_ago($r['created_at']) ?></div>
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="btn btn-secondary btn-sm" data-copy="<?= e($r['generated_markdown']) ?>">Copy Markdown</button>
                            <a href="<?= BASE_URL ?>/dashboard/readme-generator.php" class="btn btn-primary btn-sm">Edit in Studio</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleEditProfile() {
    const box = document.getElementById('profile-edit-box');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
}

function showTab(tabId, btn) {
    document.querySelectorAll('.profile-tab-content').forEach(el => el.style.display = 'none');
    document.getElementById(tabId).style.display = 'block';
    
    btn.parentElement.querySelectorAll('button').forEach(b => {
        b.className = 'btn btn-sm btn-secondary';
    });
    btn.className = 'btn btn-sm btn-primary';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
