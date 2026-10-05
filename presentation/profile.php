<?php 
require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../business/session.php';
require_login_page();
include __DIR__ . '/includes/header.php'; 
?>

<script>
try{
  if(!localStorage.getItem('user')) location.href='<?= url_page(PAGE_LOGIN) ?>';
}catch(e){}
</script>

<h2>User Profile</h2>

<div class="card" style="max-width:700px; margin:0 auto; text-align:left;">
  <div id="uInfo" style="line-height:1.8;">Loading…</div>

  <div class="actions" style="margin-top:16px;">
    <a class="btn" href="<?= url_page(PAGE_MEDICINES) ?>">Go to Medicines</a>
    <button class="btn secondary" id="logoutBtn">Logout</button>
  </div>
</div>

<script>
(function(){
  try{
    const u = JSON.parse(localStorage.getItem('user') || '{}');
    const box = document.getElementById('uInfo');
    if(!u || !u.username){
      box.innerHTML = '<div class="error-box">No user info found.</div>';
      return;
    }
    box.innerHTML = `
      <p><strong>Full name:</strong> ${u.full_name || '—'}</p>
      <p><strong>Username:</strong> ${u.username}</p>
      <p><strong>Role:</strong> ${u.role || '—'}</p>
      <p><strong>User ID:</strong> ${u.user_id || '—'}</p>
    `;
  }catch(e){}
})();

document.getElementById('logoutBtn').addEventListener('click', (e)=>{
  e.preventDefault();
  window.logoutUser();
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>