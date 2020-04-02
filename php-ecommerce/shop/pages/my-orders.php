<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  if (!$loggedInUser) {
    echo "<script>location.href='".ROOT_URL."auth?page=login&msg=login_for_checkout';</script>";
    exit;
  }

  global $loggedInUser;

  $userId = $loggedInUser->id;
  $orderMgr = new OrderManager();

  $status = 'payed';
  $payedOrders = $orderMgr->getOrdersOfUser($userId, $status);

  $status = 'delayed';
  $delayedOrders = $orderMgr->getOrdersOfUser($userId, $status);

  $status = 'shipped';
  $shippedOrders = $orderMgr->getOrdersOfUser($userId, $status);

  $status = 'pending';
  $pendingOrders = $orderMgr->getOrdersOfUser($userId, $status);

  $status = 'canceled';
  $canceledOrders = $orderMgr->getOrdersOfUser($userId, $status);

  $count = 0;
?>

<h1 cass="mb-4">I miei ordini</h1>

<?php if (count($payedOrders) > 0) :  ?>
  <h4 class="mb-3">Ordini Pagati</h4>
  <table class="table table-bordered">
  <thead>
    <tr>
      <th class="big-screen">#</th>
      <th>Num.Ordine</th>
      <th>Data Pagamento</th>
      <th>Link</th>
      <th class="text-center">PDF</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($payedOrders as $order) : $count++; ?>
  
    <tr class="text-primary">
      <td class="big-screen"><?php echo $count; ?></td>
      <td><?php echo esc_html($order['order_id']); ?></td>
      <td><?php echo esc_html($order['created_date']); ?></td>
      <td>
        <a class="underline" href="<?php echo ROOT_URL . 'shop?page=view-order&id=' . esc_html($order['order_id']); ?>">Vedi &raquo;</a>
      </td>
      <td class="text-center">
        <a target="_blank" href="<?php echo ROOT_URL . 'shop/invoices/print-invoice.php?orderId=' . esc_html($order['order_id']); ?>" title="stampa PDF" class="btn btn-lg btn-link p-0"><i class="fas fa-file-pdf"></i></a>
      </td>
    </tr>
  <?php endforeach; $count=0; ?>
  </tbody>
</table>
<?php else: ?>
  <p>Non hai alcun ordine pagato.</p>
<?php endif; ?>

<hr>

<?php if (count($delayedOrders) > 0) :  ?>
  <h4 class="mb-3">Ordini Pagamento Postumo</h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th class="big-screen">#</th>
        <th>Num.Ordine</th>
        <th>Data</th>
        <th>Link</th>
        <th class="text-center">PDF</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($delayedOrders as $order) : $count++; ?>
        <tr class="text-primary">
          <td class="big-screen"><?php echo $count; ?></td>
          <td><?php echo esc_html($order['order_id']); ?></td>
          <td><?php echo esc_html($order['created_date']); ?></td>
          <td>
            <a class="underline" href="<?php echo ROOT_URL . 'shop?page=view-order&id=' . esc_html($order['order_id']); ?>">Vedi &raquo;</a>
          </td>
          <td class="text-center">
            <a target="_blank" href="<?php echo ROOT_URL . 'shop/invoices/print-invoice.php?orderId=' . esc_html($order['order_id']); ?>" title="stampa PDF" class="btn btn-lg btn-link p-0"><i class="fas fa-file-pdf"></i></a>
          </td>
        </tr>
      <?php endforeach; $count=0; ?>
    </tbody>
</table>
<?php else: ?>
  <p>Non hai alcun ordine pagamento postumo.</p>
<?php endif; ?>

<hr>

<?php if (count($pendingOrders) > 0) :  ?>
  <h4 class="mb-3">Ordini in attesa</h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th class="big-screen">#</th>
        <th>Num.Ordine</th>
        <th>Data Invio</th>
        <th>Link</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($pendingOrders as $order) : $count++; ?>
      
        <tr class="text-muted">
          <td class="big-screen"><?php echo $count; ?></td>
          <td><?php echo esc_html($order['order_id']); ?></td>
          <td><?php echo esc_html($order['created_date']); ?></td>
          <td>
            <a class="underline" href="<?php echo ROOT_URL . 'shop?page=view-order&id=' . esc_html($order['order_id']); ?>">Vedi &raquo;</a>
          </td>
        </tr>
      <?php endforeach; $count=0; ?>
    </tbody>
  </table>
<?php endif; ?>

<hr>

<?php if (count($canceledOrders) > 0) :  ?>
  <h4 class="mb-3">Ordini cancellati</h4>
  <table class="table table-bordered">
   <thead>
      <tr>
        <th class="big-screen">#</th>
        <th>Num.Ordine</th>
        <th>Data Invio</th>
        <th>Link</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($canceledOrders as $order) : $count++; ?>
      
        <tr class="text-danger">
          <td class="big-screen"><?php echo $count; ?></td>
          <td><?php echo esc_html($order['order_id']); ?></td>
          <td><?php echo esc_html($order['created_date']); ?></td>
          <td>
            <a class="underline" href="<?php echo ROOT_URL . 'shop?page=view-order&id=' . esc_html($order['order_id']); ?>">Vedi &raquo;</a>
          </td>
        </tr>
      <?php endforeach; $count=0; ?>
    </tbody>
  </table>
<?php endif; ?>

<hr>

<?php if (count($shippedOrders) > 0) : ?>
  <h4 class="mb-3">Ordini Spediti</h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th class="big-screen">#</th>
        <th>Num.Ordine</th>
        <th>Data Invio</th>
        <th class="big-screen">Data Spedizione</th>
        <th>Link</th>
        <th class="text-center">PDF</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($shippedOrders as $order) : $count++; ?>
        <tr class="text-success">
          <td class="big-screen"><?php echo $count; ?></td>
          <td><?php echo esc_html($order['order_id']); ?></td>
          <td><?php echo esc_html($order['created_date']); ?></td>
          <td class="big-screen"><?php  echo esc_html($order['shipped_date']); ?></td>
          <td>
            <a class="underline" href="<?php echo ROOT_URL . 'shop?page=view-order&id=' . esc_html($order['order_id']); ?>">Vedi &raquo;</a>
          </td>
          <td class="text-center">
            <a target="_blank" href="<?php echo ROOT_URL . 'shop/invoices/print-invoice.php?orderId=' . esc_html($order['order_id']); ?>" title="stampa PDF" class="btn btn-lg btn-link p-0"><i class="fas fa-file-pdf"></i></a>
          </td>
        </tr>
      <?php endforeach; $count=0; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>Non hai alcun ordine spedito.</p>
<?php endif; ?>

<script>
 $(document).ready(function() {
    $('table.table').DataTable({
      bLengthChange: false,
      pageLength: 5
    });
} );
</script>
