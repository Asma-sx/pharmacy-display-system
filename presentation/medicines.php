<?php 
require_once __DIR__ . '/../config/paths.php';

require_once __DIR__ . '/../business/session.php';
require_once __DIR__ . '/../business/services/MedicineService.php';
require_login_page();

include __DIR__ . '/includes/header.php';

$medicines = (new MedicineService())->getAll();
?>

<h2>Medicines</h2>

<div id="pageMessage"></div>
<div id="medList" class="med-grid"></div>

<script>
const medicinesData = <?php echo json_encode($medicines); ?>;

function showPageMessage(msg, isError = false){
  const el = document.getElementById('pageMessage');
  el.innerHTML = `<div class="${isError ? 'alert error' : 'alert success'}">${msg}</div>`;
  setTimeout(()=>{ el.innerHTML = ''; }, 3000);
}

function loadMedicines() {
  const container = document.getElementById("medList");

  if (!Array.isArray(medicinesData) || medicinesData.length === 0) {
    container.innerHTML = `<p>No medicines found.</p>`;
    return;
  }

  let html = "";
  let currentUser = {};
  try { currentUser = JSON.parse(localStorage.getItem('user') || '{}'); } catch(e){}

  medicinesData.forEach(med => {
    const canEdit = currentUser.role === 'admin';
    html += `
  <div class="med-card">
    <h3>${med.name}</h3>
    <p><strong>Brand:</strong> ${med.brand ?? "--"}</p>
    <p><strong>Category:</strong> ${med.category ?? "--"}</p>
    <p><strong>Price:</strong> ${med.price} SAR</p>
    <p><strong>Expiry:</strong> ${med.expiry_date}</p>

    <div class="card-actions">
      <a class="btn" href="<?= url_page(PAGE_VIEW_MEDICINE) ?>?id=${med.med_id}">View Details</a>
      ${canEdit ? `<a class="btn" href="<?= url_page(PAGE_EDIT_MEDICINE) ?>?id=${med.med_id}">Edit</a>` : ''}
      ${canEdit ? `<button class="btn danger" onclick="deleteMedicine(${med.med_id})">Delete</button>` : ''}
    </div>
  </div>
`;
  });

  container.innerHTML = html;
}

async function deleteMedicine(id){
  if(!confirm('Are you sure you want to delete this medicine?')) return;
  try{
    const res = await fetch(`<?= BASE_PATH . API_MEDICINES ?>?id=${id}`, { 
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' }
    });
    const text = await res.text();
    let data;
    try {
      data = JSON.parse(text);
    } catch(e) {
      console.error('Response text:', text);
      alert('Server error');
      return;
    }
    
    if(data.success){
      showPageMessage('Medicine deleted');
      setTimeout(() => location.reload(), 1500);
    } else {
      alert(data.error || 'Delete failed');
    }
  }catch(err){
    console.error(err);
    alert('Network/Server error');
  }
}

loadMedicines();

const params = new URLSearchParams(location.search);
if(params.get('action') === 'added') showPageMessage('Medicine added successfully');
if(params.get('action') === 'updated') showPageMessage('Medicine updated successfully');
if(params.get('action') === 'deleted') showPageMessage('Medicine deleted successfully');

</script>

<?php include __DIR__ . '/includes/footer.php'; ?>