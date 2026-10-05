<?php 
require_once __DIR__ . '/../config/paths.php';
include __DIR__ . '/includes/header.php'; 
?>

<h2>About Us</h2>

<div class="card" style="max-width:900px; margin:0 auto; text-align:left; font-size:16px; line-height:1.7;">

  <p>
    This web application was built as a simple and organized system to help users browse and
    manage pharmacy medicines in an easy and clear way. The main goal is to provide a clean 
    interface where the user can view each medicine with its details such as brand, category, 
    expiry date, ingredients, and warnings.
  </p>

  <p style="margin-top:15px;">
    The system also allows authorized users to add new medicines, update existing information, 
    and remove items when needed. The design focuses on being straightforward and user-friendly, 
    so anyone can use it without complexity.
  </p>

  <p style="margin-top:15px;">
    This project was created as part of our university coursework, combining both frontend and 
    backend development. The frontend handles the layout, pages, and user experience, while the 
    backend manages data storage, authentication, and the API that connects all system features 
    together.
  </p>

  <p style="margin-top:15px;">
    Our aim is to present a working demo that demonstrates how a small pharmacy management 
    interface can be built using modern web technologies in a clean and structured way.
  </p>

  <p style="margin-top:15px;">
 Feel free to contact US 
 Email: ttt@gmail.com , Number:0555555555
  </p>

  <div class="actions" style="margin-top:20px;">
    <a class="btn" href="<?= url_page(PAGE_HOME) ?>">Back to Home</a>
  </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>