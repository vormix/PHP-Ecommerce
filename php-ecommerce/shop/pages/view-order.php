<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  if (!$loggedInUser) {
    echo "<script>location.href='".ROOT_URL."auth?page=login';</script>";
    exit;
  }


  if (!isset($_GET['id'])){
    echo "<script>location.href='".ROOT_URL."admin?page=orders-list&msg=not_found';</script>";
    exit;
  }

  $orderId = esc($_GET['id']);

  $orderMgr = new OrderManager();
  $orderItems = $orderMgr->getOrderItems($orderId);
  $orderTotal = $orderMgr->getOrderTotal($orderId)[0];

  $status = $orderItems[0]['order_status'];

  //var_dump($orderTotal);die;
  if (count($orderItems) == 0) {
    echo "<script>location.href='".ROOT_URL."admin?page=orders-list&msg=order_empty';</script>";
    exit;
  }
  $count = 0;
?>

<a href="<?php echo ROOT_URL . 'shop?page=my-orders'; ?>" class="back underline">&laquo; I miei Ordini</a>

<h1 cass="mb-4">Ordine #<?php echo esc_html($orderId); ?></h1>

  <table class="table table-bordered">
    <tr>
      <th class="big-screen">#</th>
      <th>Prodotto</th>
      <th>Quantità</th>
      <th class="big-screen">Prezzo Unitario</th>
      <th>Prezzo</th>
    </tr>
  <?php foreach ($orderItems as $item) : $count++; ?>
  
    <tr>
      <td class="big-screen"><?php echo $count; ?></td>
      <td class="big-screen"><?php echo esc_html($item['product_name']); ?></td>
      <td><?php echo esc_html($item['quantity']); ?></td>
      <td><?php echo esc_html($item['single_price']); ?> €</td>
      <td><?php echo esc_html($item['total_price']); ?> €</td>
    </tr>
  <?php endforeach; $count=0; ?>
  <tr> 
    <th colspan="100%">
      <h4 class="inline">Totale <?php echo $orderTotal['total']; ?> €</h4>
      <h4 class="inline right"><span class="badge badge-secondary badge-pill">Ordine <?php echo $status == 'pending' ? 'In Lavorazione' : 'Spedito'; ?></span></h4>
    </th>
  </tr>
</table>

