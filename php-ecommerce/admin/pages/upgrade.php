<?php
// Prevent from direct access
if (! defined('ROOT_URL')) {
  die;
}
$mgr = new UpgradeManager();
$scripts = [];

if (isset($_POST['upgrade'])) 
{
  $results = $mgr->ExecuteScripts();
} 
else 
{
  $scripts = $mgr->GetScriptsToExecute();
}

$dbVersion = $mgr->GetDbVersion();
?>
<h1 class="mb-3">Aggiornamento Database:</h1>
<p>Il database è alla versione: <strong><?php echo esc_html($dbVersion) ?></strong>.</p>

<?php if ($scripts) : ?>
  <p>Clicca su "Aggiorna il database" per eseguire gli script</p>
  <p class="lead">Lista degli script disponibili:</p>
  <ul class="list-group mt-3">
    <?php foreach ($scripts as $script) : ?>
      <li class="list-group-item"><?php echo esc_html($script) ?></li>
    <?php endforeach ?>
  </ul>
  <form method="post" class=" mt-5">
    <input onclick="return confirm('Conferma Aggiornamento?');" name="upgrade" type="submit" class="btn btn-primary" value="Aggiorna il database.">
  </form>
<?php else : ?>
<?php endif ?>

<?php if (isset($_POST["upgrade"])) : ?>
  <table class="table table-responsive">
    <thead>
      <tr><th>Script</th><th>Esito</th><th>Messaggio</th></tr>
    </thead>
    <tbody>
      <?php foreach ($results as $result) : ?>
        <?php 
        $file = esc_html($result["file"]);
        $cssClass = $result["result"] == 1 ? 'success' : 'danger'; 
        $res = $result["result"] == 1 ? 'OK' : 'Errore';
        $message = isset($result["message"]) ? esc_html($result["message"]) : "";
        ?>
        <tr class="text-<?php echo $cssClass ?>">
          <td><strong><?php echo $file ?></strong></td>
          <td><?php echo $res ?></td>
          <td><?php echo $message ?></td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
<?php endif ?>
