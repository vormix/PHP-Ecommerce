<?php
// Prevent from direct access
if (! defined('ROOT_URL')) {
  die;
}

$id = 0;
$email = new Email(0, '', '');
$emailMgr = new EmailManager();
$customers = $emailMgr->getCustomers();

if (isset($_GET['id'])){

  $id = (int) $_GET['id'];
  $email = $emailMgr->GetEmail($id);
}

if (isset($_POST['save']) || isset($_POST['send'])) {

  $email->subject = esc_html($_POST['subject']); 
  $email->message = htmlentities($_POST['message']);
}

if (isset($_POST['save'])) {
  if ($id == 0) {
    $id = $emailMgr->save($email);
  } else {
    $rowsUpdated = $emailMgr->update($email, $id);
  }

  $to = esc_html($_POST['to']);
  $emailMgr->saveRecipients($id, $to);

  echo "<script>location.href='".ROOT_URL."admin?page=emails-list&msg=updated';</script>";
  exit;
}



if (isset($_POST['send'])) {
  
  $br = "\r\n";
  $to = esc_html($_POST['to']);
  $subject = $email->subject;
  $txt = $email->message ;

  $headers = "From: ".SITE_NAME . $br ;
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

  mail($to,$subject,$txt,$headers);

  echo 'email sent to : "' . $to . '"';
}


 
?>

<a href="<?php echo ROOT_URL . 'admin?page=emails-list'; ?>" class="back underline">&laquo; Lista Emails</a>

<h1>Gestione Newsletter</h1>
<p>Compila un messaggio promozionale da inviare ai clienti:</p>

<form method="post" class="mt-2">
  <div class="form-group">
    <label for="subject"><strong>Oggetto</strong></label>
    <input name="subject" id="subject" type="text" class="form-control" placeholder="Oggetto del messaggio.." value="<?php echo esc_html($email->subject); ?>">
  </div>

  <div class="form-group">

    <label for="toDiv"><strong>Destinatari</strong></label>

    <div class="row mb-3">
      <div class="col-12">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="sendToAll" id="sendToAll" value="1">
          <label class="form-check-label" for="sendToAll">
            <span id="lblSelect">Seleziona tutti</span>
          </label>
        </div>
      </div>
    </div>

    <div class="row chooseTo">
      <div class="col-md-10">
        <select id="selectTo" type="text" class="form-control form-control-sm" >
          <option value="0"> - Scegli un contatto - </option>
            <?php if (count($customers) > 0) : ?>
              <?php foreach ($customers as $customer) : ?>
                <option data-id="<?php echo esc_html($customer->id); ?>" value="<?php echo esc_html($customer->email); ?>"><?php echo esc_html($customer->name) . " (" . esc_html($customer->email) . ")"; ?></option>
              <?php endforeach ; ?>
            <?php endif ; ?>
        </select>
      </div>
      <div class="col-md-2">
        <button id="addRecipient" name="addRecipient" type="button" class="right btn btn-sm btn-secondary">Aggiungi</button>
      </div>
      <div class="col-12 mt-3">
        <div id="toDiv" type="text" class="form-control height-auto" placeholder="Inserisci almeno un destinatario...">
          <?php if (count($email->recipients) > 0) : ?>
            <?php foreach ($email->recipients as $recipient) : ?>  
              <span data-email="<?php echo esc_html($recipient->email); ?>" style="font-size:14px;" class="badge badge-light mail-to">
                <?php echo esc_html($recipient->email); ?>
                <span style="font-size:14px;" class="badge badge-danger pointer rounded-circle mail-remove" title="rimuovi">
                  &times;
                </span>
              </span>
            <?php endforeach ; ?>
          <?php else : ?>
            <span class="text-muted">Aggiungi almeno un destinatario...</span>
          <?php endif ; ?>
        </div>
        <input id="to" name="to" type="hidden">
      </div>
    </div>

  </div>

  <div class="form-group">
    <label for="message"><strong>Messaggio</strong></label>
    <textarea rows="10" name="message" id="message" type="text" class="form-control"><?php echo html_entity_decode($email->message);?></textarea>
  </div>

  <div class="form-group text-right">
    <input name="save" type="submit" class="btn btn-secondary mt-3" value="Salva Bozza">
    <input name="send" type="submit" class="btn btn-primary mt-3" value="Invia Email &raquo;">
  </div>

</form>

<script>

var $document = $(document);
var $message = $('#message');
var $addRecipient = $('#addRecipient');
var $to = $('#to');
var $toDiv = $('#toDiv');
var $sendToAll = $('#sendToAll');
var $chooseTo = $('.chooseTo');
var $selectTo = $('#selectTo');
var $lblSelect = $('#lblSelect');

var receipts = [];

$document.ready(function() {

    $addRecipient.on('click', addRecipient);
    $sendToAll.on('click', addOrRemoveAllRecipients);
    $document.on('click', '.mail-remove', removeMail);

    $message.summernote({
      placeholder: 'Scrivi qualcosa ai tuoi clienti...',
      tabsize: 2,
      height: 250
    });      
});

function removeMail(e){

  var $target = $(e.target);
  var $label = $target.closest('.mail-to');
  var email = $label.attr('data-email');

  receipts = receipts.filter(elem => elem != email);
  $to.val(receipts.join(';'));

  $label.fadeOut('fast', function() {
      $(this).remove();
  });
  if (receipts.length == 0) {
    $toDiv.html('<span class="text-muted">Aggiungi almeno un destinatario...</span>');
  }
}

function addOrRemoveAllRecipients (e) {
  if (!$sendToAll.is(':checked')) {
    removeAllRecipients();
    $lblSelect.text('Seleziona tutti');
  } else {
    addAllRecipients();
    $lblSelect.text('Deseleziona tutti');
  }
}

function removeAllRecipients() {
  receipts = [];
  $to.val('');
  $toDiv.html('<span class="text-muted">Aggiungi almeno un destinatario...</span>');
}

function addAllRecipients(){

  receipts = [];
  var options = $selectTo.find('option');
  $.each(options, (i, option) => {
    var email = $(option).val();
    if (email != "0"){
      receipts.push(email);
    }
  });
  printEmails();
}

function printEmails() {
  $toDiv.html(receipts.map(email => `
    <span data-email="${email}" style="font-size:14px;" class="badge badge-light mail-to">
      ${email}
      <span style="font-size:14px;" class="badge badge-danger pointer rounded-circle mail-remove" title="rimuovi">
        &times;
      </span>
    </span>`).join(''));

  $to.val(receipts.join(';'));
}

function addRecipient (e) {
  var recipient = $selectTo.val();
  if (recipient == "0" || receipts.includes(recipient)) return;

  receipts.push(recipient);
  printEmails();
  $to.val(receipts.join(';'));
}

</script>