<?php
require_once __DIR__ . '/../../config/paths.php';
?>
<nav>
  <a href="<?= url_page(PAGE_HOME) ?>">Home</a>
  <a href="<?= url_page(PAGE_MEDICINES) ?>" data-auth="1" style="display:none">Medicines</a>
  <a href="<?= url_page(PAGE_ADD_MEDICINE) ?>" data-admin="1" style="display:none">Add Medicine</a>
  <a href="<?= url_page(PAGE_PROFILE) ?>" data-auth="1" style="display:none">Profile</a>

  <span style="flex:1"></span>

  <a href="<?= url_page(PAGE_LOGIN) ?>" id="lnkLogin">Login</a>
  <a href="<?= url_page(PAGE_REGISTER) ?>" id="lnkRegister">Register</a>
  <a href="#" id="lnkLogout" style="display:none">Logout</a>
</nav>

<script>
try {
  const raw = localStorage.getItem('user');
  const user = raw ? JSON.parse(raw) : null;
  const authed = !!user;

  const show = el => el && (el.style.display = '');
  const hide = el => el && (el.style.display = 'none');

  (authed ? hide : show)(document.getElementById('lnkLogin'));
  (authed ? hide : show)(document.getElementById('lnkRegister'));
  (authed ? show : hide)(document.getElementById('lnkLogout'));

  document.querySelectorAll('[data-auth="1"]').forEach(el => authed ? show(el) : hide(el));

  const isAdmin = authed && user.role === 'admin';
  document.querySelectorAll('[data-admin="1"]').forEach(el => isAdmin ? show(el) : hide(el));

  document.getElementById('lnkLogout')?.addEventListener('click', (e) => {
    e.preventDefault();
    window.logoutUser();
  });
} catch(e){}
</script>