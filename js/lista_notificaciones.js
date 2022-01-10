function iniciar(){
  //set_actividad( {actividad:"Visitar Adm Notificaciones"} );    
  
  console.log("init");
  
  $("#div_cargando").fadeOut();  
  
  $('#btn_cerrar').click(function () {
    cerrar_alerta();
  });
  
  $(".btn_toggle").change(function() {
    console.log('cambiar a: '+$(this).data("id")+" to "+ $(this).prop('checked'));
    $("#div_cargando").fadeIn();
    update_notificacion( $(this).data("id"), $(this).prop('checked') );
  })
}

function update_notificacion(id, habilitado){      
  var data = {
    id : id,
    habilitado: habilitado    
  };    
     
  fetch("services/set_notificacion_disp.php", {
    method: 'POST',
    credentials: 'same-origin',      
    body: JSON.stringify(data),
    headers: {
      'Content-Type': 'application/json'
    }
  })
  .then(function (response) { return response.json(); })
  .then(function (response) {                
    if (response.success) {                          
      location.reload();
    } else {        
      mostrar_alerta("Ocurrio un error al actualizar el estado "+response.reason );        
    }
  })
  .catch(function (error) {
    console.error(error);      
  });
}