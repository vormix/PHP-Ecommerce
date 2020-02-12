<?php
require_once '../inc/init.php'; 

global $loggedInUser;

if (!$loggedInUser) {
  $returnPage = isset($_GET['page']) ? esc($_GET['page']) : '';
  echo "<script>location.href='".ROOT_URL."auth?page=login'</script>";
  exit;
}

// if ($_GET['page'] == 'dashboard' && $loggedInUser->user_type == 'admin') {
//   echo "<script>location.href='".ROOT_URL."admin?page=dashboard'</script>";
//   exit;
// }

$page = 'profile';
if(isset($_GET['page'])) {
  $page = $_GET['page'];
}
?>
<?php include ROOT_PATH . 'public/template-parts/header.php'; ?>
<div class="container mt-5">
  <div class="row">
    <div class="col-md-9">
      <div class="main">
      <?php include "pages/$page.php"; ?>
      <?php include ROOT_PATH . 'inc/alert-message.php'; ?>
      </div>
    </div>
    <div class="col-md-3 big-screen">
      <?php include ROOT_PATH . 'public/template-parts/sidebar.php'; ?>
    </div>
  </div>

</div>
<?php include ROOT_PATH . 'public/template-parts/footer.php'; ?>