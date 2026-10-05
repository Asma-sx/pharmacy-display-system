<?php
require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../business/session.php';
$BASE = BASE_PATH;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Pharmacy System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?= $BASE ?>/presentation/css/styles.css?v=20251108">
  <style>
    /* Toast */
    .toast {
      position: fixed;
      top: 14px;
      right: 14px;
      background: #16a34a;
      color: #fff;
      padding: 10px 14px;
      border-radius: 8px;
      box-shadow: 0 4px 16px rgba(0,0,0,.18);
      font-size: 15px;
      z-index: 9999;
      opacity: 0; transform: translateY(-8px);
      transition: opacity .2s ease, transform .2s ease;
    }
    .toast.show { opacity: 1; transform: translateY(0); }
    .toast.error { background:#b91c1c; }
  </style>
</head>
<body>

  <?php include __DIR__ . '/navbar.php'; ?>
  <div class="container">

  <script>
    // Keep the browser in sync with the server session
    (function(){
      const serverUser = <?= json_encode(current_user()) ?>;
      try {
        if (serverUser) localStorage.setItem('user', JSON.stringify(serverUser));
        else localStorage.removeItem('user');
      } catch(e){}
      const _fetch = window.fetch.bind(window);
      window.fetch = async (...args) => {
        const res = await _fetch(...args);
        if (res.status === 401) {
          try { localStorage.removeItem('user'); } catch(e){}
          location.href = '<?= PAGE_LOGIN ?>';
        }
        return res;
      };
      window.logoutUser = async () => {
        try { await _fetch('<?= BASE_PATH . API_AUTH ?>?action=logout', { method: 'POST' }); } catch(e){}
        try { localStorage.removeItem('user'); } catch(e){}
        location.href = '<?= PAGE_LOGIN ?>';
      };
    })();
    // Toast helper
    function showToast(msg, isError=false){
      const t = document.createElement('div');
      t.className = 'toast' + (isError ? ' error' : '');
      t.textContent = msg;
      document.body.appendChild(t);
      requestAnimationFrame(()=> t.classList.add('show'));
      setTimeout(()=>{
        t.classList.remove('show');
        setTimeout(()=> t.remove(), 200);
      }, 1800);
    }

    // اقرأ بارامترات (?ok=1&action=...)
    (function(){
      try{
        const q = new URLSearchParams(location.search);
        if(q.get('ok') === '1'){
          const a = (q.get('action') || '').toLowerCase();
          let msg = 'Success.';
          if(a === 'login')   msg = 'Logged in successfully.';
          if(a === 'added')   msg = 'Medicine added successfully.';
          if(a === 'deleted') msg = 'Medicine deleted.';
          if(a === 'registered') msg = 'Account created successfully.';
          showToast(msg, false);

          // نظّف البارامترات من العنوان بدون إعادة تحميل
          q.delete('ok'); q.delete('action');
          const clean = location.pathname + (q.toString() ? ('?' + q.toString()) : '');
          window.history.replaceState({}, '', clean);
        }
      }catch(e){}
    })();

    // Pass API_PATH to JavaScript - استخدم BASE_PATH
    window.API_PATH = "<?= BASE_PATH . API_MEDICINES ?>";
    window.BASE_PATH = "<?= BASE_PATH ?>";
  </script>