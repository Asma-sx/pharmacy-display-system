<?php
require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../business/session.php';
require_once __DIR__ . '/../business/services/MedicineService.php';
require_admin_page();
include __DIR__ . '/includes/header.php';

$id = $_GET['id'] ?? null;
$medicine = null;

if($id) {
  $medicine = (new MedicineService())->getById((int)$id);
}
?>

<script>
try{
  const u = JSON.parse(localStorage.getItem('user') || '{}');
  if(!u.username) location.href='<?= url_page(PAGE_LOGIN) ?>';
  if(u.role !== 'admin') location.href='<?= url_page(PAGE_MEDICINES) ?>';
}catch(e){}
</script>

<h2>Edit Medicine</h2>

<form id="editForm" onsubmit="return submitEdit(event);" style="max-width:520px; margin:0 auto; text-align:left;">
  <label>Name</label>
  <input class="input" type="text" name="name" id="name" required>
  <div class="error" id="error-name"></div>

  <label>Brand</label>
  <input class="input" type="text" name="brand" id="brand">
  <div class="error" id="error-brand"></div>

  <label>Category</label>
  <input class="input" type="text" name="category" id="category">
  <div class="error" id="error-category"></div>

  <label>Description</label>
  <textarea class="input" name="description" id="description"></textarea>
  <div class="error" id="error-description"></div>

  <label>Active Ingredients</label>
  <textarea class="input" name="active_ingredients" id="active_ingredients"></textarea>
  <div class="error" id="error-active_ingredients"></div>

  <label>Warnings</label>
  <textarea class="input" name="warnings" id="warnings"></textarea>
  <div class="error" id="error-warnings"></div>

  <label>Price (SAR)</label>
  <input class="input" type="text" name="price" id="price" placeholder="0.00" pattern="^[0-9]*\.?[0-9]{0,2}$">
  <div class="error" id="error-price"></div>

  <label>Quantity</label>
  <input class="input" type="number" name="quantity" id="quantity">
  <div class="error" id="error-quantity"></div>

  <label>Expiry Date</label>
  <input class="input" type="date" name="expiry_date" id="expiry_date" required>
  <div class="error" id="error-expiry_date"></div>

  <div class="actions" style="margin-top:12px;">
    <button class="btn" type="submit">Save</button>
    <a class="btn secondary" href="<?= url_page(PAGE_MEDICINES) ?>">Cancel</a>
  </div>
</form>

<script>
const id = new URLSearchParams(location.search).get('id');

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

function clearErrors(){
  ['name','brand','category','description','active_ingredients','warnings','price','quantity','expiry_date'].forEach(k=>{
    const el = document.getElementById('error-' + k);
    if(el) el.textContent = '';
  });
}

function showErrors(errors){
  clearErrors();
  if(!errors) return;
  for(const key in errors){
    const el = document.getElementById('error-' + key);
    if(el) el.textContent = errors[key];
  }
}

async function loadData(){
  if(!id){ alert('No id'); location.href='<?= url_page(PAGE_MEDICINES) ?>'; return; }
  
  const medData = <?php echo json_encode($medicine); ?>;
  
  if(!medData || !medData.med_id){
    alert('Medicine not found');
    location.href='<?= url_page(PAGE_MEDICINES) ?>';
    return;
  }

  let expiry = medData.expiry_date ?? '';
  if(expiry && expiry.length > 10) expiry = expiry.split(' ')[0];

  const map = {
    name: medData.name,
    brand: medData.brand,
    category: medData.category,
    description: medData.description,
    active_ingredients: medData.active_ingredients,
    warnings: medData.warnings,
    price: medData.price,
    quantity: medData.quantity,
    expiry_date: expiry
  };

  for (const k in map){
    const el = document.getElementById(k);
    if(el) el.value = map[k] ?? '';
  }
}
loadData();

async function submitEdit(e){
  e.preventDefault();
  clearErrors();

  const fd = new FormData(e.target);
  const payload = Object.fromEntries(fd.entries());

  if(!payload.name || !payload.expiry_date){
    showErrors({ name: 'Name is required', expiry_date: 'Expiry date required' });
    return false;
  }

  if(payload.price && isNaN(parseFloat(payload.price))) {
    showErrors({ price: 'Price must be a number' });
    document.getElementById('price').focus();
    return false;
  }

  if(payload.quantity && isNaN(parseInt(payload.quantity))) {
    showErrors({ quantity: 'Quantity must be a number' });
    document.getElementById('quantity').focus();
    return false;
  }

  try{
    const res = await fetch('<?= BASE_PATH ?>/business/controllers/medicines.php?id=' + id, {
      method: 'PUT',
      headers: { 'Content-Type':'application/json' },
      body: JSON.stringify(payload)
    });

    const data = await res.json().catch(()=>({}));

    if(!data.success){
      if(data.errors) {
        showErrors(data.errors);
      } else {
        alert(data.error || 'Update failed');
      }
      return false;
    }

    location.href = '<?= url_page(PAGE_VIEW_MEDICINE) ?>?id=' + id + '&ok=1&action=updated';
  }catch(err){
    console.error(err);
    alert('Network/Server error');
  }

  return false;
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>