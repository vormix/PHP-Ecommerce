// A reference to Stripe.js
var stripe;

var orderData = {};

$(document).ready(function(){

  // Disable the button until we have Stripe set up on the page
  $(".stripe-button").attr('disabled', 'disabled');
  
  fetch( rootUrl + "shop/payments/stripe/stripe-key.php")
    .then(function(result) {
      return result.json();
    })
    .then(function(data) {
      console.log(data); 
      return setupElements(data);
    })
    .then(function({ stripe, card, clientSecret }) {
      $(".stripe-button").removeAttr('disabled');
  
      var $form = $("#stripe-payment-form");
      $form.on("submit", function(event) {
        event.preventDefault();
        pay(stripe, card, clientSecret);
      });
    });
});


var setupElements = function(data) {
  stripe = Stripe(data.publishableKey);
  /* ------- Set up Stripe Elements to use in checkout form ------- */
  var elements = stripe.elements();
  var style = {
    base: {
      color: "#32325d",
      fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
      fontSmoothing: "antialiased",
      fontSize: "16px",
      "::placeholder": {
        color: "#aab7c4"
      }
    },
    invalid: {
      color: "#fa755a",
      iconColor: "#fa755a"
    }
  };

  var card = elements.create("card", { style: style, hidePostalCode:true });
  card.mount("#card-element");

  return {
    stripe: stripe,
    card: card,
    clientSecret: data.clientSecret
  };
};

/*
 * Collect card details and pays for the order
 */
var pay = function(stripe, card) {
  changeLoadingState(true);

  stripe
    .createPaymentMethod("card", card)
    .then(function(result) {
      if (result.error) {
        showError(result.error.message);
      } else {
        orderData.paymentMethodId = result.paymentMethod.id;

        return fetch(rootUrl + "shop/payments/stripe/pay.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(orderData)
        });
      }
    })
    .then(function(result) {
      return result.json();
      // return result.text();

    })
    .then(function(result) {
      console.log(result); 
      changeLoadingState(false);
      location.href = rootUrl + 'shop?page=checkout&success='+result.success+'&orderId=' + result.orderId;
    });
};

/* ------- Post-payment helpers ------- */

/* Shows a success / error message when the payment is complete */
// var orderComplete = function(clientSecret) {
//   stripe.retrievePaymentIntent(clientSecret).then(function(result) {
//     var paymentIntent = result.paymentIntent;
//     var paymentIntentJson = JSON.stringify(paymentIntent, null, 2);

//     $(".sr-payment-form").hide();
//     $("pre").text(paymentIntentJson);

//     $(".sr-result").show();
//     setTimeout(function() {
//       $(".sr-result").addClass("expand");
//     }, 200);

//     changeLoadingState(false);
//   });
// };

var showError = function(errorMsgText) {
  changeLoadingState(false);
  var $errorMsg = $(".sr-field-error");
  $errorMsg.text( errorMsgText);
  setTimeout(function() {
    errorMsg.text("");
  }, 4000);
};

// Show a spinner on payment submission
var changeLoadingState = function(isLoading) {
  if (isLoading) {
    $(".stripe-button").disabled = true;
    // $(".stripe-button .spinner").show();
    // $(".stripe-button .button-text").hide();
  } else {
    $(".stripe-button").disabled = false;
    // $(".stripe-button .spinner").hide();
    // $(".stripe-button .button-text").show;
  }
};