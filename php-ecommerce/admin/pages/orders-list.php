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

  $status = 'delayed';
  $delayedOrders = $orderMgr->getAllOrders($status);

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
  <thead>
      <tr>
        <th class="big-screen">#</th>
        <th class="big-screen">Num.Ordine</th>
        <th>Data Pagamento</th>
        <th>Cliente</th>
        <th>Link</th>
        <th class="text-center">PDF</th>
        <th>Azioni</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($payedOrders as $order) : $count++; ?>
      <tr>
        <td class="big-screen"><?php echo $count; ?></td>
        <td class="big-screen"><?php echo esc_html($order['order_id']); ?></td>
        <td><?php echo esc_html($order['created_date']); ?></td>
        <td><?php echo esc_html($order['user_descr']); ?></td>
        <td>
          <a class="underline" href="<?php echo ROOT_URL . 'admin?page=process-order&id=' . esc_html($order['order_id']); ?>">Vedi &raquo;</a>
        </td>
        <td class="text-center">
          <a target="_blank" href="<?php echo ROOT_URL . 'shop/invoices/print-invoice.php?orderId=' . esc_html($order['order_id']); ?>" title="stampa PDF" class="btn btn-lg btn-link p-0"><i class="fas fa-file-pdf"></i></a>
        </td>
        <td>  
          <form method="post" action="<?php echo ROOT_URL . 'admin?page=process-order&id=' . esc_html($order['order_id']); ?>&ship_order=1" class="inline right">
            <input onclick="return confirm('Confermi spedizione ordine n. #<?php echo esc_html($order['order_id']); ?> ?');" name="ship_order" type="submit" class="btn btn-sm btn-primary m-0" value="Spedisci &raquo;">
          </form>
        </td>
      </tr>
    <?php endforeach; $count=0; ?>
    </tbody>
</table>
<?php else: ?>
  <p>Non ci sono ordini pagati.</p>
<?php endif; ?>

<hr>

<?php if (count($shippedOrders) > 0) : ?>
  <h4 class="mb-3">Ordini Spediti</h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th class="big-screen">#</th>
        <th class="big-screen">Num.Ordine</th>
        <th class="big-screen">Data Invio</th>
        <th>Data Spedizione</th>
        <th>Cliente</th>
        <th>Link</th>
        <th class="text-center">PDF</th>
      </tr>
    </thead>
    <tbody>
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
          <td class="text-center">
            <a target="_blank" href="<?php echo ROOT_URL . 'shop/invoices/print-invoice.php?orderId=' . esc_html($order['order_id']); ?>" title="stampa PDF" class="btn btn-lg btn-link p-0"><i class="fas fa-file-pdf"></i></a>
          </td>
        </tr>
      <?php endforeach; $count=0; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>Non ci sono ordini spediti.</p>
<?php endif; ?>

<hr>

<?php if (count($delayedOrders) > 0) : ?>
  <h4 class="mb-3">Ordini Pagamento Postumo</h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th class="big-screen">#</th>
        <th class="big-screen">Num.Ordine</th>
        <th>Data</th>
        <th>Cliente</th>
        <th>Link</th>
        <th class="text-center">PDF</th>
        <th>Azioni</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($delayedOrders as $order) : $count++; ?>
        <tr>
          <td class="big-screen"><?php echo $count; ?></td>
          <td class="big-screen"><?php echo esc_html($order['order_id']); ?></td>
          <td><?php echo esc_html($order['created_date']); ?></td>
          <td><?php echo esc_html($order['user_descr']); ?></td>
          <td>
            <a class="underline" href="<?php echo ROOT_URL . 'admin?page=process-order&id=' . esc_html($order['order_id']); ?>">Vedi &raquo;</a>
          </td>
          <td class="text-center">
            <a target="_blank" href="<?php echo ROOT_URL . 'shop/invoices/print-invoice.php?orderId=' . esc_html($order['order_id']); ?>" title="stampa PDF" class="btn btn-lg btn-link p-0"><i class="fas fa-file-pdf"></i></a>
          </td>
          <td>
            <button onclick="return confirm('Confermi ordine n. #<?php echo esc_html($order['order_id']); ?> pagato?');" name="pay_order" type="button" class="btn btn-sm btn-info m-0" >Pagato &raquo;</button>
          </td>
        </tr>
      <?php endforeach; $count=0; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>Non ci sono ordini con pagamento postumo.</p>
<?php endif; ?>

<hr>

<?php if (count($pendingOrders) > 0) :  ?>
  <h4 class="mb-3">Ordini in attesa</h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th class="big-screen">#</th>
        <th class="big-screen">Num.Ordine</th>
        <th>Data Invio</th>
        <th>Cliente</th>
        <th>Link</th>
      </tr>
    </thead>
    <tbody>
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
    </tbody>
  </table>
<?php else: ?>
  <p>Non ci sono ordini in attesa.</p>
<?php endif; ?>

<hr>

<?php if (count($canceledOrders) > 0) :  ?>
  <h4 class="mb-3">Ordini annullati</h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th class="big-screen">#</th>
        <th class="big-screen">Num.Ordine</th>
        <th>Data Invio</th>
        <th>Cliente</th>
        <th>Link</th>
        <th>Azioni</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach ($canceledOrders as $order) : $count++; ?>
      <tr class="text-danger">
        <td class="big-screen"><?php echo $count; ?></td>
        <td class="big-screen"><?php echo esc_html($order['order_id']); ?></td>
        <td><?php echo esc_html($order['created_date']); ?></td>
        <td><?php echo esc_html($order['user_descr']); ?></td>
        <td>
          <a class="underline" href="<?php echo ROOT_URL . 'admin?page=process-order&id=' . esc_html($order['order_id']); ?>">Vedi &raquo;</a>
        </td>
        <td> 
          <?php if (!$order['is_restored']) :  ?>
          <form method="POST" action="<?php echo ROOT_URL . 'admin?page=process-order&id=' . esc_html($order['order_id']); ?>&restore_order=1" class="inline right">
            <input onclick="return confirm('Confermi ripristino prodotti ordine n. #<?php echo esc_html($order['order_id']); ?> ?');" name="restore_order" type="submit" class="btn btn-sm btn-danger m-0" value="Ripristina &raquo;">
          </form>
          <?php else: ?>
            Ripristinato
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; $count=0; ?>
    </tbody>
</table>
<?php else: ?>
  <p>Non ci sono ordini annullati.</p>
<?php endif; ?>

<hr>

<script>
 $(document).ready(function() {
    $('table.table').DataTable({
      bLengthChange: false,
      pageLength: 5
    });
} );
</script>