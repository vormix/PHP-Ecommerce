<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  $cartMgr = new CartManager();
  $cartId = $cartMgr->getCurrentCartId();
  $totCartItems = $cartMgr->getCartTotal($cartId)[0]['num_products'];
  $totCartItems = !$totCartItems ? 0 : $totCartItems;
?>
  <footer class="navbar-fixed bottom">
    <hr>
    <div class="container mt-5 mb-5">    
      <p><?php echo 'Copyright &copy; ' . date('Y').' - '. SITE_NAME; ?></p>
    </div>
  </footer>

  
  <script src="https://bootswatch.com/_vendor/popper.js/dist/umd/popper.min.js"></script>
  <script src="https://bootswatch.com/_vendor/bootstrap/dist/js/bootstrap.min.js"></script>
  <script src="<?php echo ROOT_URL; ?>assets/js/main.js"></script>
  <script>
  $(document).ready(function(){
    var totCartItems = '<?php echo $totCartItems; ?>';
    $('.js-totCartItems').html(totCartItems);
  });
  </script>
</body>

</html>