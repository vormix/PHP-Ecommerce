
$(document).ready(function(){
  setTimeout(() => {
    $('.alert-dismissible').slideUp();
  }, 5000);

  $('.live-search').hide();
  $('#search').on('keyup', searchProducts);
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


function searchProducts(e) {
  if (e.target.value.length < 3) {
    $('.live-search').hide();
    return;
  }  
  $('.live-search').show();

  $.getJSON( rootUrl + 'api/shop/search-products.php?search='+e.target.value, response => {
    populateSearchResults(response.data);
  });
}

function populateSearchResults(products){
  var $searchDiv = $('.live-search').find('.results');
  $searchDiv.html('<h6 class="border-bottom border-gray pb-2 mb-0">Risultati della ricerca...</h6>');
  $.each(products, (i, product) => {

    var imgUrl = product.image_id == 0 ? rootUrl + 'images/noimage.jpg' : `${rootUrl}images/${product.id}/${product.image_id}.jpg`;

    $searchDiv.append(`
    <div class="media pt-3">
      <img class="bd-placeholder-img mr-2 rounded" width="32" height="32"src="${imgUrl}">
      <div class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
        <div class="d-flex justify-content-between align-items-center w-100">
          <strong class="text-gray-dark">${product.name}</strong>
          <a style="cursor:pointer;" href="${rootUrl}shop?page=view-product&id=${product.id}">Vedi</a>
        </div>
        <span class="d-block">${product.category}</span>
        <span class="d-block">€ ${product.price}</span>
      </div>
    </div>
    `);
  })
}


function countdown(elem) {

  var $target = $(elem);

  var countDownDate = new Date($target.attr('data-fine-sconto')).getTime();

  var x = setInterval(function() {

    var now = new Date().getTime();
    var distance = countDownDate - now;

    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    // Display the result in the element with id="demo"
    $target.text( days + "gg " + hours + "h "
    + minutes + "m " + seconds + "s ");

    // If the count down is finished, write some text
    if (distance < 0) {
      clearInterval(x);
      $target.text("EXPIRED");
    }
  }, 1000);

}