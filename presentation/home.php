<?php 
require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../business/session.php';
require_login_page();
include __DIR__ . '/includes/header.php'; 
?>

<script>
// حارس: لو ما فيه مستخدم، حوّله للّوجن
try {
  if (!localStorage.getItem('user')) {
    location.href = '<?= url_page(PAGE_LOGIN) ?>';
  }
} catch(e){}
</script>

<h1>Welcome to the Pharmacy Display System</h1>
<p style="max-width:600px; margin:15px auto; font-size:17px; line-height:1.6;">
 A simple and organized platform to view, manage, and track pharmacy medicines.
  Use the navigation bar above to browse medicines, add new items, or manage your account.
</p>

<a class="btn" href="<?= url_page(PAGE_MEDICINES) ?>">View Medicines</a>

<?php include __DIR__ . '/includes/footer.php'; ?>