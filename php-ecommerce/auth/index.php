<?php
require_once '../inc/init.php'; 

global $loggedInUser;

$page = isset($_GET["page"]) ? $_GET["page"] : 'login';

if ($loggedInUser && $page != 'logout') {
  Header('Location: ' . ROOT_URL);
  exit;
}

?>

<?php include 'template-parts/header.php'; ?>
<div class="container mt-5">
  <div class="row">
    <div class="col-md-6 ml-auto mr-auto login-box">
      <div class="main">
        <a class="back underline" href="<?php echo ROOT_URL; ?>">&laquo; Torna alla Home</a>
        <br>
        <?php include "pages/$page.php"; ?>
        <?php include ROOT_PATH . 'inc/alert-message.php'; ?>
      </div>
    </div>
  </div>
</div>

<?php include 'template-parts/footer.php'; ?>