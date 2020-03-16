<?php
require_once '../inc/init.php'; 
$page = isset($_GET["page"]) ? $_GET["page"] : 'homepage';
?>
<?php include ROOT_PATH . 'public/template-parts/header.php'; ?>
<div class="main-content container mt-5">
  <div class="row">
    <div class="col-lg-9">
      <div class="main">
      <?php include "pages/$page.php"; ?>
      </div>
    </div>
    <div class="col-lg-3 big-screen">
      <?php include ROOT_PATH . 'public/template-parts/sidebar.php'; ?>
    </div>
  </div>

</div>
<?php include ROOT_PATH . 'public/template-parts/footer.php'; ?>