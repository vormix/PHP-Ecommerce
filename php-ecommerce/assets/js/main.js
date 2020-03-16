
$(document).ready(function(){
  setTimeout(() => {
    $('.alert-dismissible').slideUp();
  }, 5000);

});

function displayMessage(message) {
  var heading = message.result == 'success' ? 'OK' : 'Errore';
  var htmlMsg = `
  <div class="alert alert-dismissible alert-${message.result}">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4 class="alert-heading">${heading}</h4>
    <p class="mb-0">${message.message}</p>
  </div>
  `;
  $('.main-content').prepend(htmlMsg);
}
