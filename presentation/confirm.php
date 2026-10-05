<?php 
require_once __DIR__ . '/../config/paths.php';
include __DIR__ . '/includes/header.php'; 
?>

<?php
$ok = isset($_GET['ok']) ? $_GET['ok'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : 'operation';
$message = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<h2>Confirmation</h2>

<div class="card" style="max-width: 600px; margin: 0 auto; text-align: center; padding: 30px;">

    <?php if ($ok == 1): ?>
        <p style="color: #0f5132; background: #d1e7dd; padding: 15px; border-radius: 6px; font-size: 18px;">
            ✅ The <?= htmlspecialchars($action) ?> was completed successfully.
        </p>
    <?php else: ?>
        <p style="color: #842029; background: #f8d7da; padding: 15px; border-radius: 6px; font-size: 18px;">
            ❌ The <?= htmlspecialchars($action) ?> failed.<br><br>
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <div style="margin-top: 25px;">
        <a class="btn" href="<?= url_page(PAGE_HOME) ?>">Back to Home</a>
    </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>