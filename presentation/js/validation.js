const BASE_API = window.API_PATH || (window.BASE_PATH + "/business/controllers/medicines.php");

function isEmpty(v){ return v===undefined || v===null || String(v).trim()===""; }
function toast(m){ alert(m); }

function toISODateString(value){
  const try1 = new Date(value);
  if(!isNaN(try1.getTime())){
    const y = try1.getFullYear();
    const m = String(try1.getMonth()+1).padStart(2,'0');
    const d = String(try1.getDate()).padStart(2,'0');
    return `${y}-${m}-${d}`;
  }
  return value;
}

function validateMedicine(){
  const f = document.getElementById("addForm");
  if(!f) return false;
  if(isEmpty(f.name.value)){ toast("Name is required"); f.name.focus(); return false; }
  if(isEmpty(f.expiry_date.value)){ toast("Expiry date is required"); f.expiry_date.focus(); return false; }

  const expStr = toISODateString(f.expiry_date.value);
  const exp = new Date(expStr);
  const today = new Date(); today.setHours(0,0,0,0);
  if(isNaN(exp.getTime()) || exp <= today){
    toast("Expiry date must be in the future (YYYY-MM-DD)");
    f.expiry_date.focus(); return false;
  }

  if(!isEmpty(f.price.value) && Number(f.price.value) < 0){ toast("Price cannot be negative"); return false; }
  if(!isEmpty(f.quantity.value) && Number(f.quantity.value) < 0){ toast("Quantity cannot be negative"); return false; }
  return true;
}

function submitMedicine(e){
  e.preventDefault();
  const f = document.getElementById("addForm");
  if(!validateMedicine()) return false;

  const payload = {
    name: f.name.value.trim(),
    brand: f.brand.value.trim(),
    category: f.category.value.trim(),
    description: f.description.value.trim(),
    active_ingredients: f.active_ingredients.value.trim(),
    warnings: f.warnings.value.trim(),
    price: f.price.value ? Number(f.price.value) : null,
    quantity: f.quantity.value ? Number(f.quantity.value) : 0,
    expiry_date: toISODateString(f.expiry_date.value), // <-- المهم
    image_url: f.image_url.value.trim(),
    added_by: Number(f.added_by?.value || 1),
  };

  return fetch(BASE_API, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  })
  .then(async (res) => {
    const text = await res.text();
    let data; try { data = JSON.parse(text); } catch { data = { raw:text }; }

    if(res.ok && !data?.error){
      window.location.href = (window.BASE_PATH || "/PharmacyDisplaySystem") + "/presentation/confirm.php?ok=1&action=add%20medicine";
    } else {
      const msg = (data && (data.error || data.detail))
                  ? `${data.error || ""}${data.detail ? " | " + data.detail : ""}`
                  : `Request failed (${res.status})`;
      toast(msg);
    }
  })
  .catch(err => toast("Network error: " + err.message));
}