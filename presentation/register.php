<?php 
require_once __DIR__ . '/../config/paths.php';
include __DIR__ . '/includes/header.php'; 
?>

<h2>Register</h2>

<form id="regForm" class="auth-form" novalidate>
  <label for="full_name">Full name</label>
  <input class="input" type="text" id="full_name" name="full_name"
         required minlength="3" placeholder="e.g., Layan Al-Otaibi">

  <label for="username">Username</label>
  <input class="input" type="text" id="username" name="username"
         required minlength="3" placeholder="Choose a username">

  <label for="password">Password</label>
  <div style="position:relative">
    <input class="input" type="password" id="password" name="password"
           required minlength="6" placeholder="Min 6 characters">
    <button type="button" id="togglePw" class="btn secondary"
            style="position:absolute; right:6px; top:6px; padding:6px 10px;">
      Show
    </button>
  </div>

  <label for="role">Role</label>
  <select class="input" id="role" name="role" required>
    <option value="pharmacist">pharmacist</option>
  </select>

  <div id="regError" class="error-box" style="display:none; margin-top:10px;"></div>
  <div id="regOk" class="ok-box" style="display:none; margin-top:10px;"></div>

  <div class="actions" style="margin-top:14px;">
    <button class="btn" id="regBtn" type="submit">Create account</button>
    <a class="btn secondary" href="<?= url_page(PAGE_LOGIN) ?>">Back to login</a>
  </div>
</form>

<script>
// لو المستخدم مسجل دخول رجعه للقائمة
(() => {
  try {
    if (localStorage.getItem('user')) {
      location.href = '<?= url_page(PAGE_LOGIN) ?>?ok=1&action=registered';
    }
  } catch(e){}
})();

// إظهار/إخفاء كلمة المرور
document.getElementById('togglePw').addEventListener('click', () => {
  const pw  = document.getElementById('password');
  const btn = document.getElementById('togglePw');
  const isText = pw.type === 'text';
  pw.type = isText ? 'password' : 'text';
  btn.textContent = isText ? 'Show' : 'Hide';
});

// إرسال النموذج
document.getElementById('regForm').addEventListener('submit', async (ev) => {
  ev.preventDefault();

  const full_name = document.getElementById('full_name').value.trim();
  const username  = document.getElementById('username').value.trim();
  const password  = document.getElementById('password').value;
  const role      = document.getElementById('role').value;

  const err = document.getElementById('regError');
  const ok  = document.getElementById('regOk');
  err.style.display='none'; err.textContent='';
  ok.style.display='none';  ok.textContent='';

  // أول شيء: كل الحقول لازم تكون معبّية
  if (!full_name || !username || !password) {
    err.textContent = 'Fill all fields.';
    err.style.display = 'block';
    return;
  }

  // تحقق الطول للاسم واليوزر (على الأقل 3 حرو)
  if (full_name.length < 3 || username.length < 3) {
    err.textContent = 'Full name and username must be at least 3 characters.';
    err.style.display = 'block';
    return;
  }

  // تحقق طول الباسورد
  if (password.length < 6) {
    err.textContent = 'Password must be at least 6 chars.';
    err.style.display = 'block';
    return;
  }

  document.getElementById('regBtn').disabled = true;

  try {
    const res = await fetch(`<?= BASE_PATH . API_AUTH ?>?action=register`, {
      method: 'POST',
      headers: { 'Content-Type':'application/json' },
      body: JSON.stringify({ full_name, username, password, role })
    });
    const data = await res.json().catch(()=>({}));

    if (!res.ok || !data?.success) {
      err.textContent = (data && data.error) ? data.error : 'Registration failed.';
      err.style.display = 'block';
      document.getElementById('regBtn').disabled = false;
      return;
    }

    ok.textContent = 'Account created. Redirecting to login...';
    ok.style.display = 'block';
    setTimeout(() => location.href = '<?= url_page(PAGE_LOGIN) ?>', 1000);

  } catch (e) {
    err.textContent = 'Network/Server error.';
    err.style.display = 'block';
    document.getElementById('regBtn').disabled = false;
  }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>