<?php 
require_once __DIR__ . '/../config/paths.php';

require_once __DIR__ . '/../business/session.php';
require_admin_page();

include __DIR__ . '/includes/header.php';
?>

<script>
try{
  const u = JSON.parse(localStorage.getItem('user') || '{}');
  if (!u.username) location.href='<?= url_page(PAGE_LOGIN) ?>';
  if (u.role !== 'admin') location.href='<?= url_page(PAGE_MEDICINES) ?>';
}catch(e){}
</script>

<h2>Add Medicine</h2>

<form id="addForm" onsubmit="return submitMedicine(event);">
  <label>Name</label>
  <input type="text" name="name" id="name" required>

  <label>Brand</label>
  <input type="text" name="brand" id="brand">

  <label>Category</label>
  <input type="text" name="category" id="category">

  <label>Description</label>
  <textarea name="description" id="description"></textarea>

  <label>Active Ingredients</label>
  <textarea name="active_ingredients" id="active_ingredients"></textarea>

  <label>Warnings</label>
  <textarea name="warnings" id="warnings"></textarea>

  <label>Price</label>
  <input type="text" name="price" id="price" placeholder="0.00" pattern="^[0-9]*\.?[0-9]{0,2}$">

  <label>Quantity</label>
  <input type="number" name="quantity" id="quantity" min="0" value="0">

  <label>Expiry Date</label>
  <input type="date" name="expiry_date" id="expiry_date" required>

  <input type="hidden" name="image_url">
  <input type="hidden" name="added_by" id="added_by">

<script>
  try {
    const u = JSON.parse(localStorage.getItem('user') || '{}');
    document.getElementById('added_by').value = u.user_id || '';
  } catch(e){}

  document.getElementById('price').addEventListener('keypress', function(e) {
    const char = String.fromCharCode(e.which);
    if (!/[0-9.]/.test(char)) {
      e.preventDefault();
    }
  });
  
  document.getElementById('quantity').addEventListener('keypress', function(e) {
    const char = String.fromCharCode(e.which);
    if (!/[0-9]/.test(char)) {
      e.preventDefault();
    }
  });
</script>

  <div class="actions">
    <button class="btn" type="submit">Add</button>
    <a class="btn secondary" href="<?= url_page(PAGE_MEDICINES) ?>">Cancel</a>
  </div>
</form>

<script>
async function submitMedicine(ev){
  ev.preventDefault();
  const fd = new FormData(ev.target);
  const payload = Object.fromEntries(fd.entries());

  if(!payload.name || !payload.expiry_date){
    alert('Name and Expiry Date are required.');
    return false;
  }

  if(!payload.added_by) {
    alert('You must be logged in to add medicines');
    return false;
  }

  if(payload.price && isNaN(parseFloat(payload.price))) {
    alert('Price must be a number');
    document.getElementById('price').focus();
    return false;
  }

  if(payload.quantity && isNaN(parseInt(payload.quantity))) {
    alert('Quantity must be a number');
    document.getElementById('quantity').focus();
    return false;
  }

  try{
    const res = await fetch('<?= BASE_PATH . API_MEDICINES ?>', {
      method: 'POST',
      headers: { 'Content-Type':'application/json' },
      body: JSON.stringify(payload)
    });

    const text = await res.text();
    let data;
    try {
      data = JSON.parse(text);
    } catch(e) {
      console.error('Response text:', text);
      alert('Server error: Invalid response');
      return false;
    }

    if(!data?.success){
      alert(JSON.stringify(data.errors) || data.error || 'Create failed');
      return false;
    }

    location.href = '<?= url_page(PAGE_MEDICINES) ?>?ok=1&action=add%20medicine';

  } catch(e){
    console.error('Error:', e);
    alert('Server error: ' + e.message);
  }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>