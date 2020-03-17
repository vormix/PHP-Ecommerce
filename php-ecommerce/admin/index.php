<?php
require_once '../inc/init.php'; 
 
global $loggedInUser;

if (!$loggedInUser) {
  echo "<script>location.href='".ROOT_URL."auth?page=login';</script>";
  exit;
}

if ($loggedInUser->user_type != 'admin') {
  echo "<script>location.href='".ROOT_URL."user?page=dashboard&msg=forbidden';</script>";
  exit;
}

$page = isset($_GET["page"]) ? $_GET["page"] : 'dashboard';
?>
<?php include ROOT_PATH . 'public/template-parts/header.php'; ?>
<div class="main-content container mt-5 newclass">
  <div class="row">
    <div class="col-lg-9">
      <div class="main">
        <!-- <?php if ($page != 'dashboard' AND $page != 'process-order') : ?>
          <a class="back underline" href="<?php echo ROOT_URL; ?>admin?page=dashboard">&laquo; Torna al cruscotto</a>
          <br>
        <?php endif; ?> -->
        <?php include ROOT_PATH . 'inc/alert-message.php'; ?>
        <?php include "pages/$page.php"; ?>
        <?php include ROOT_PATH . 'inc/alert-message.php'; ?>
      </div>
    </div>
    <div class="col-lg-3 big-screen">
      <?php include ROOT_PATH . 'public/template-parts/sidebar.php'; ?>
    </div>
  </div>

</div>
<?php include ROOT_PATH . 'public/template-parts/footer.php'; ?>