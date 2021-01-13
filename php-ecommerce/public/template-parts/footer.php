

<?php
$cm = new CartManager();
$cartId = $cm->getCurrentCartId();
$cart_total = $cm->getCartTotal($cartId); 
?>

<footer class="bg-dark">
  <hr>
  <p class="container text-light">Copyright &copy; 2019</p>
</footer>

  <script src="https://bootswatch.com/_vendor/jquery/dist/jquery.min.js"></script>
  <script src="https://bootswatch.com/_vendor/popper.js/dist/umd/popper.min.js"></script>
  <script src="https://bootswatch.com/_vendor/bootstrap/dist/js/bootstrap.min.js"></script>

  <script src="<?php echo ROOT_URL; ?>assets/js/main.js"></script>
  <script>
  $(document).ready(function(){
    $('.js-totCartItems').html('<?php echo $cart_total['num_products'] ?>');
  });
  </script>
</body>
</html>