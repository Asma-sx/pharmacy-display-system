<?php 
require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../business/session.php';
require_login_page();
include __DIR__ . '/includes/header.php'; 
?>

<h2>Medicine Details</h2>

<div id="detailsBox" class="details-container">Loading…</div>

<script>
async function loadDetails() {
  const id = new URLSearchParams(window.location.search).get('id');
  const box = document.getElementById('detailsBox');

  if (!id) { 
    box.innerHTML = "<div class='error-box center'>No medicine ID.</div>"; 
    return; 
  }

  try {
    console.log('API_PATH:', window.API_PATH);
    console.log('Fetching:', `${window.API_PATH}?id=${id}`);
    const res = await fetch(`${window.API_PATH}?id=${id}`);
    console.log('Response status:', res.status);
    const med = await res.json();
    console.log('Medicine data:', med);

    if (!med || !med.med_id) {
      box.innerHTML = "<div class='error-box center'>Medicine not found.</div>";
      return;
    }

    box.innerHTML = `
      <div class="details-card">
        <div class="details-info">
          <h3>${med.name}</h3>

          <p><strong>Brand:</strong> ${med.brand ?? "—"}</p>
          <p><strong>Category:</strong> ${med.category ?? "—"}</p>
          <p><strong>Price:</strong> ${med.price ?? 0} SAR</p>
          <p><strong>Quantity:</strong> ${med.quantity ?? 0}</p>
          <p><strong>Expiry:</strong> ${med.expiry_date ?? "—"}</p>

          <h4>Description</h4>
          <p>${med.description ?? "—"}</p>

          <h4>Active Ingredients</h4>
          <p>${med.active_ingredients ?? "—"}</p>

          <h4>Warnings</h4>
          <p>${med.warnings ?? "—"}</p>

          <div class="actions" style="margin-top:15px;">
            <a class="btn secondary" href="<?= url_page(PAGE_MEDICINES) ?>">Back</a>
            <button class="btn" id="btnEdit" style="display:none">Edit</button>
            <button class="btn danger" id="btnDelete" style="display:none">Delete</button>
          </div>
        </div>
      </div>
    `;

    // صلاحيات الأدمن فقط
    const medId = med.med_id;
    try {
      const u = JSON.parse(localStorage.getItem('user') || '{}');
      if (u && u.role === 'admin') {
        const btnE = document.getElementById('btnEdit');
        const btnD = document.getElementById('btnDelete');
        btnE.style.display = '';
        btnD.style.display = '';
        btnE.onclick = () => location.href = `<?= url_page(PAGE_EDIT_MEDICINE) ?>?id=${medId}`;
        btnD.onclick = () => deleteMed(medId);
      }
    } catch (e) {}

  } catch (e) {
    console.error('Error loading medicine:', e);
    box.innerHTML = "<div class='error-box center'>Failed to load details.</div>";
  }
}

async function deleteMed(id){
  if(!confirm("Delete this medicine?")) return;
  try {
    const res = await fetch(`${window.API_PATH}?id=${id}`, { method: "DELETE" });
    const data = await res.json();
    if(res.ok && data?.success){
      showToast("Medicine deleted successfully.");
      setTimeout(() => {
        window.location.href = "<?= url_page(PAGE_MEDICINES) ?>?ok=1&action=deleted";
      }, 800);
    } else {
      showToast(data?.error || "Delete failed", true);
    }
  } catch(e){
    showToast("Network error", true);
  }
}

loadDetails();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>