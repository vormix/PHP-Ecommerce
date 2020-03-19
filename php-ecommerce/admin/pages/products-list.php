<?php
// Prevent from direct access
if (! defined('ROOT_URL')) {
  die;
}

global $alertMsg;
$mgr = new ProductManager();

if (isset($_POST['delete'])) {

  $id = trim($_POST['id']);
  $mgr->DeleteProduct($id);
  $alertMsg = 'deleted';
}

$products = $mgr->getAll();
?>

<a href="<?php echo ROOT_URL . 'admin?page=product'; ?>" class="btn btn-primary mb-3">Aggiungi Prodotto</a>

<h1>Elenco Prodotti</h1>

<?php if (count($products) > 0) : ?>
<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">Nome</th>
      <th scope="col" class="big-screen">Descrizione</th>
      <th scope="col">Prezzo</th>
      <th scope="col" class="right">Azioni</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($products as $product) : ?>
    <tr>
      <td><?php echo esc_html($product->name); ?></td>
      <td class="big-screen"><?php echo esc_html(substr($product->description, 0, 30)); ?></td>
      <td>€ <?php echo esc_html($product->price); ?></td>
      <td>
        <form method="post" class="right">
          <input type="hidden" name="id" value="<?php echo esc_html($product->id); ?>">
          <input name="delete" onclick="return confirm('Procedere ad eliminare?');" type="submit" class="btn btn-outline-danger btn-sm" value="Elimina">
        </form>
        <a class="right btn btn-outline-secondary btn-sm" href="<?php echo ROOT_URL . 'admin?page=product'; ?>&id=<?php echo esc_html($product->id); ?>">Vedi</a>
      </td>
    </tr>
    <?php endforeach ; ?>
  </tbody>
</table>
<?php else : ?>
  <p>Nessun Prodotto presente...</p>
<?php endif ; ?>
