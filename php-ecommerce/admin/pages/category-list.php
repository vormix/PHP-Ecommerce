<?php

  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  global $loggedInUser;
  $cl = new CategoryManager();
  if (isset($_POST['delete'])) {

    $id = trim($_POST['id']);
    $cl->DeleteCategory($id);
    $alertMsg = 'deleted';
  }
  
  $categories=$cl->GetCategories();

?>
<a href="<?php echo ROOT_URL . 'admin?page=category'; ?>" class="btn btn-primary mb-3">Aggiungi Categoria</a>

<h1>Elenco Categorie</h1>

<?php if (count($categories) > 0) : ?>
<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">Nome</th>
      <th scope="col" >Azioni</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($categories as $category) : ?>
    <tr>
      <td><?php echo esc_html($category->name); ?></td>
      <td>
        <form method="post" class="inline" >
          <input type="hidden" name="id" value="<?php echo esc_html($category->id); ?>">
          <input name="delete" onclick="return confirm('Procedere ad eliminare?');" type="submit" class="btn btn-outline-danger btn-sm" value="Elimina">
        </form>
        <a class="btn btn-outline-secondary btn-sm" href="<?php echo ROOT_URL . 'admin?page=category'; ?>&id=<?php echo esc_html($category->id); ?>">Vedi</a>
      </td>
    </tr>
    <?php endforeach ; ?>
  </tbody>
</table>
<?php else : ?>
  <p>Nessuna Categoria presente...</p>
<?php endif ; ?>

