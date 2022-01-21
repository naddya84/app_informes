
function cerrar_alerta(){
  $('#mensaje_form').fadeOut('slow');
  $("#popup").fadeOut();
  $('.popup-overlay').fadeOut('slow');
}

function mostrar_alerta(mensaje){
  $('#texto_mensaje').html(mensaje);
  $('#mensaje_form').fadeIn('slow');
  $('#popup').fadeIn();
}

