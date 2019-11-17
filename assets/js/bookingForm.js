const $ = require('jquery');

$(document).ready(function() {

  var jsBook = document.querySelector('#js-book');
  var onlinePayment = jsBook.dataset.onlinePayment;

  // On cache le form stripe
  $('div.stripe').hide();

  if (onlinePayment == true) {

    // On cache le bouton de confirmation
    $('#button_confirm').hide();

    // S'il choisit une des méthodes de paiement
    $('.payment_choose').click(function() {

      // On affiche le bouton de confirmation
      $('#button_confirm').show();

      // On enleve les precedents success
      $('.payment_choose').removeClass('btn-success');

      // On ajoute success sur le bouton cliqué
      $(this).addClass('btn-success');

      // Si le choix est stripe, on affiche le form
      if ($(this).attr('id') == 'payment_active') {
          $('.form-group .stripe').show();
      } else {
          $('.form-group .stripe').hide();
      }
    });
  } else {
    $('.payment_choose').hide();
    $('#or').hide();
  }
});