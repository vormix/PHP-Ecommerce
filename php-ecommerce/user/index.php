<?php
$page = isset($_GET["page"]) ? $_GET["page"] : 'dashboard';
?>
<?php include '../inc/init.php' ?>

<?php include ROOT_PATH . 'public/template-parts/header.php' ?>

<div id="main" class="container" style="margin-top:100px;">
  <div class="row">

    <div class="col-9"> 
    <?php include ROOT_PATH . 'user/pages/' . $page . '.php' ?>
    </div>

    <?php include ROOT_PATH . 'public/template-parts/sidebar.php' ?>
 
</div>

<?php include ROOT_PATH . 'public/template-parts/footer.php' ?>
