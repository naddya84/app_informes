function iniciar(){
  $("#div_cargando").fadeOut();      
  $('#chk_todos').change(function() {
        if($(this).is(":checked")) {
         $(".chk_item").prop('checked', true);
        } else {
          $(".chk_item").prop('checked', false);
        }
      }); 
  $("#btn_informes_gestion").click( function (){
    var items = $(".chk_item:checked").length;
    if( items > 0 ){
      var texto = " informes seleccionado"
      if( items > 1 ){
        texto = " informes seleccionados"
      }
      var mensaje = "<br>Seguro que desea <b class='font_rojo'>crear</b> "+items+texto;
      mostrar_confirmacion( mensaje );

      $("#btn_aceptar_confirmar").unbind();
      $("#btn_aceptar_confirmar").click( function (){
        cerrar_confirmacion();
        $("#div_cargando").fadeIn();    
        crear_items("informe")
      });    
      $("#btn_cancelar_confirmar").click( function (){
        cerrar_confirmacion();
      });    
      $("#btn_cerrar").click( function (){
        cerrar_alerta();
      });    
    } else {
      mostrar_alerta("No selecciono ningun informe");
    }
  });
}
function crear_items(entidad){
  
  $("#div_cargando").fadeIn();
  var data = [];
  $.each( $(".chk_item:checked"), function(index, value) {        
    data.push($(value).data("id"));
  });    
  
  var send_data = {
    ids: data,
    entity: entidad
  };
  
  fetch('services/set_informes_x_gestion.php',  {
    method: 'POST',
    credentials: 'same-origin',
    body: JSON.stringify(send_data),
    headers:{
      'Content-Type': 'application/json'
    }
  })
  .then(function(response) {      
    return response.json();
  })
  .then(function(response) {              
    $("#div_cargando").fadeOut();
    if( response.success ){
      mostrar_alerta( "Los informes se crearon de forma exitosa" );  
    } else {
      mostrar_alerta( response.reason );
    }
  })
  .catch( function(error){        
    $("#div_cargando").fadeOut();
    console.error(error);        
    mostrar_alerta( "Existio un error al crear los items" );
  });    
}
function mostrar_confirmacion(mensaje){
  $('#texto_mensaje_confirmar').html(mensaje);
  $('#pop_confirmar').fadeIn();
  $('#fondo_pop_c').fadeIn();
}

function cerrar_confirmacion(){
  $('#pop_confirmar').fadeOut();
  $("#popup").fadeOut();
  $('.popup-overlay').fadeOut();
}
