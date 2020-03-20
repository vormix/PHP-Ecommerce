<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  $error = false;
  global $loggedInUser;
  global $alertMsg;

  $userId = $loggedInUser->id;
  $orderMgr = new OrderManager();

  if (!$loggedInUser) {
    echo "<script>location.href='".ROOT_URL."auth?page=login';</script>";
    exit;
  }

  $status = 'payed';
  $payedOrders = $orderMgr->getAllOrders($status);
  $status = 'shipped';
  $shippedOrders = $orderMgr->getAllOrders($status);

  $status = 'pending';
  $pendingOrders = $orderMgr->getAllOrders($status);
  $status = 'canceled';
  $canceledOrders = $orderMgr->getAllOrders($status);

  $count = 0;
?>

<h1 cass="mb-4">Tutti gli ordini</h1>

<?php if (count($payedOrders) > 0) :  ?>
  <h4 class="mb-3">Ordini Pagati</h4>
  <table class="table table-bordered">
    <tr>
      <th class="big-screen">#</th>
      <th class="big-screen">Num.Ordine</th>
      <th>Data Pagamento</th>
      <th>Cliente</th>
      <th>Azioni</th>
    </tr>
  <?php foreach ($payedOrders as $order) : $count++; ?>
  
    <tr>
      <td class="big-screen"><?php echo $count; ?></td>
      <td class="big-screen"><?php echo esc_html($order['order_id']); ?></td>
      <td><?php echo esc_html($order['created_date']); ?></td>
      <td><?php echo esc_html($order['user_descr']); ?></td>
      <td>
        <a class="btn btn-primary btn-sm" href="<?php echo ROOT_URL . 'admin?page=process-order&id=' . esc_html($order['order_id']); ?>">Lavora &raquo;</a>
      </td>
    </tr>
  <?php endforeach; $count=0; ?>
</table>
<?php else: ?>
  <p>Non ci sono ordini pagati.</p>
<?php endif; ?>

<hr>

<?php if (count($shippedOrders) > 0) : ?>
  <h4 class="mb-3">Ordini Spediti</h4>
  <table class="table table-bordered">
    <tr>
      <th class="big-screen">#</th>
      <th class="big-screen">Num.Ordine</th>
      <th class="big-screen">Data Invio</th>
      <th>Data Spedizione</th>
      <th>Cliente</th>
      <th>Azioni</th>
    </tr>
  <?php foreach ($shippedOrders as $order) : $count++; ?>
    <tr>
      <td class="big-screen"><?php echo $count; ?></td>
      <td class="big-screen"><?php echo esc_html($order['order_id']); ?></td>
      <td class="big-screen"><?php echo esc_html($order['created_date']); ?></td>
      <td><?php  echo esc_html($order['shipped_date']); ?></td>
      <td><?php echo esc_html($order['user_descr']); ?></td>
      <td>
        <a class="underline" href="<?php echo ROOT_URL . 'admin?page=process-order&id=' . esc_html($order['order_id']); ?>">Vedi &raquo;</a>
      </td>
    </tr>
  <?php endforeach; $count=0; ?>
  </table>
<?php else: ?>
  <p>Non ci sono ordini spediti.</p>
<?php endif; ?>

<hr>

<?php if (count($pendingOrders) > 0) :  ?>
  <h4 class="mb-3">Ordini in attesa</h4>
  <table class="table table-bordered">
    <tr>
      <th class="big-screen">#</th>
      <th class="big-screen">Num.Ordine</th>
      <th>Data Invio</th>
      <th>Cliente</th>
      <th>Azioni</th>
    </tr>
  <?php foreach ($pendingOrders as $order) : $count++; ?>
  
    <tr class="text-secondary">
      <td class="big-screen"><?php echo $count; ?></td>
      <td class="big-screen"><?php echo esc_html($order['order_id']); ?></td>
      <td><?php echo esc_html($order['created_date']); ?></td>
      <td><?php echo esc_html($order['user_descr']); ?></td>
      <td>
      <a class="underline" href="<?php echo ROOT_URL . 'admin?page=process-order&id=' . esc_html($order['order_id']); ?>">Vedi &raquo;</a>
      </td>
    </tr>
  <?php endforeach; $count=0; ?>
</table>
<?php else: ?>
  <p>Non ci sono ordini in attesa.</p>
<?php endif; ?>

<hr>

<?php if (count($canceledOrders) > 0) :  ?>
  <h4 class="mb-3">Ordini annullati</h4>
  <table class="table table-bordered">
    <tr>
      <th class="big-screen">#</th>
      <th class="big-screen">Num.Ordine</th>
      <th>Data Invio</th>
      <th>Cliente</th>
      <th>Azioni</th>
    </tr>
  <?php foreach ($canceledOrders as $order) : $count++; ?>
  
    <tr class="text-danger">
      <td class="big-screen"><?php echo $count; ?></td>
      <td class="big-screen"><?php echo esc_html($order['order_id']); ?></td>
      <td><?php echo esc_html($order['created_date']); ?></td>
      <td><?php echo esc_html($order['user_descr']); ?></td>
      <td>
      <a class="underline" href="<?php echo ROOT_URL . 'admin?page=process-order&id=' . esc_html($order['order_id']); ?>">Vedi &raquo;</a>
      </td>
    </tr>
  <?php endforeach; $count=0; ?>
</table>
<?php else: ?>
  <p>Non ci sono ordini annullati.</p>
<?php endif; ?>

<hr>