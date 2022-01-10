function iniciar(){
  $("#div_cargando").fadeOut();  
   //Botones paginacion
  $('.btn_paginacion').click( function (){        
    $("#div_cargando").fadeIn();
     valor_historial = $("#historial").val();
     window.location.href = "reporte_alertas.php?pagina_actual="+( $(this).data("pagina") )+"&historial="+valor_historial;        
  });
  
  $('.ver_alerta').click( function(){
    alerta_vista( $(this).data("id_item"), $(this).data("url"));
  }); 
}

function alerta_vista( id_alerta, url ){
  var data = {
    id: id_alerta,
    visto: 1
  };
  
  fetch("services/set_alerta.php", {
      method: 'POST',
      credentials: 'same-origin',      
      body: JSON.stringify(data),
      headers: {
        'Content-Type': 'application/json'
      }
    })
    .then(function (response) {
      return response.json();
    })
    .then(function (response) {
      console.log(response); 
      window.location.href = url;
    })
    .catch(function (error) {
      console.error(error);
      $("#div_cargando").fadeOut();        
      mostrar_alerta("No se pudo cambiar el estado de la notificacion");
    });
  
}
