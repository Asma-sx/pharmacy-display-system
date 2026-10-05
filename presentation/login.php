<?php 
require_once __DIR__ . '/../config/paths.php';
include __DIR__ . '/includes/header.php'; 
?>

<h2>Login</h2>

<form id="loginForm" class="auth-form" novalidate>
  <label for="username">Username</label>
  <input class="input" type="text" id="username" name="username" required autocomplete="username" placeholder="Enter your username">

  <label for="password">Password</label>
  <div style="position:relative">
    <input class="input" type="password" id="password" name="password" required minlength="6" autocomplete="current-password" placeholder="Enter your password">
    <button type="button" id="togglePw" aria-label="Show/Hide password" class="btn secondary" style="position:absolute; right:6px; top:6px; padding:6px 10px;">Show</button>
  </div>

  <div id="loginError" class="error-box" style="display:none;margin-top:10px;"></div>

  <div class="actions" style="margin-top:14px;">
    <button class="btn" id="loginBtn" type="submit">Login</button>
    <a class="btn secondary" href="<?= url_page(PAGE_REGISTER) ?>">Create account</a>
  </div>
</form>

<script>
// إذا المستخدم مسجّل دخولاً بالفعل، وديّه للهوم
(() => {
  try { if (localStorage.getItem('user')) location.href = '<?= url_page(PAGE_HOME) ?>'; } catch(e){}
})();

// إظهار/إخفاء كلمة المرور
document.getElementById('togglePw').addEventListener('click', () => {
  const pw = document.getElementById('password');
  const btn = document.getElementById('togglePw');
  const isText = pw.type === 'text';
  pw.type = isText ? 'password' : 'text';
  btn.textContent = isText ? 'Show' : 'Hide';
});

// إرسال النموذج
document.getElementById('loginForm').addEventListener('submit', async (ev) => {
  ev.preventDefault();
  const u = document.getElementById('username').value.trim();
  const p = document.getElementById('password').value;
  const err = document.getElementById('loginError');
  const btn = document.getElementById('loginBtn');

  err.style.display = 'none'; err.textContent = '';
  if (!u || !p) { err.textContent = 'Fill all fields.'; err.style.display = 'block'; return; }
  if (p.length < 6) { err.textContent = 'Password must be at least 6 chars.'; err.style.display = 'block'; return; }

  btn.disabled = true;

  try {
    const res = await fetch(`<?= BASE_PATH . API_AUTH ?>`, {
      method: 'POST',
      headers: { 'Content-Type':'application/json' },
      body: JSON.stringify({ username: u, password: p })
    });
    const data = await res.json().catch(()=>({}));

    if (!res.ok || !data?.success) {
      err.textContent = (data && data.error) ? data.error : 'Invalid credentials.';
      err.style.display = 'block';
      btn.disabled = false;
      return;
    }

    localStorage.setItem('user', JSON.stringify(data.user));
    location.href = '<?= url_page(PAGE_HOME) ?>?ok=1&action=login';

  } catch (e) {
    err.textContent = 'Network/Server error.';
    err.style.display = 'block';
    btn.disabled = false;
  }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>