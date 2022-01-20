
function iniciar(){  
  $("#div_cargando").fadeOut();
  $(".css_inicio").removeClass("seleccionado");
  $("#n_informes").addClass("seleccionado");
  $('.form_datetime').datetimepicker({
    weekStart: 1,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
	forceParse: 0,
    showMeridian: 1,
    pickerPosition: "bottom-left"
  });
  
  $("#btn_guardar").click(function(){
    if(validar_datos()){ 
      $("#div_cargando").fadeIn();
      estado = "pendiente"; 
      registrar_informe(estado);
    }
  });
  
  $("#btn_cerrar").click(function(){
    cerrar_alerta();
  });
}
function validar_datos(){
  if ($.trim($("#nombre").val()) === '') {
      mostrar_alerta("Ingresa el nombre del informe, por favor");
      return false;
  }
  if ($.trim($("#codigo").val()) === '') {
      mostrar_alerta("Ingresa el código del informe, por favor");
      return false;
  }
  if (!$("input[name='envio']:checked").val()) {  
      mostrar_alerta("Debe seleccionar una opción de la sección tipo de envio");        
      return false;
  }
  if (!$("input[name='multa']:checked").val()) {  
      mostrar_alerta("En multa, debe seleccionar una opción");        
      return false;
  } 
  /*if ( $.trim($("#fecha_limite").val()) == ""){
      mostrar_alerta("Debes ingresar la fecha limite de envio del informe");
      return false;
  }*/
  if( $.trim($("#email").val()) != ""){
    if (!es_email_valido($.trim($("#email").val()))) {  
        mostrar_alerta("Ingresa una dirección de correo electrónica válida");        
        return false;
    }
  }
  if ( $("#id_usuario").val() == "") {  
      mostrar_alerta("Seleccione al responsable 1, por favor");        
      return false;
  }
  if ( $("#id_usuario").val() == $("#id_usuario_2").val()) { console.log($("#id_usuario").val() +" di_2: "+ $("#id_usuario_2").val());  
      mostrar_alerta("El responsable 2 no puede ser el mismo que el responsable 1");        
      return false;
  } 
  return true;
}

function es_email_valido(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function registrar_informe(estado){      
  
  var data = {
    nombre: $.trim( $("#nombre").val() ),
    codigo: $.trim( $("#codigo").val() ),
    periodo: $("#periodo").val(),
    tipo: $("input[name='envio']:checked").val(),
    sistema_modulo: $("#sistema_modulo").val(),
    multa: $("input[name='multa']:checked").val(),
    plazo_envio: $("#plazo_envio").val(),
    email: $.trim( $("#email").val() ),
    id_institucion: $("#institucion").val(),
    archivos_electronicos:$("#archivos_electronicos").val(),
    informacion_remitida: $("#informacion_remitida").val(),
    seccion: $("#seccion").val(),
    normativa: $("#normativa").val(),
    articulo: $("#articulo").val()
  };
  if($("#id_informe").val() > 0){  
    data.id = $("#id_informe").val();
  } 
  fetch('services/set_informe_maestro.php',  {
    method: 'POST',
    credentials: 'same-origin',
    body: JSON.stringify(data), 
    headers:{ 'Content-Type': 'application/json'}
  })
  .then(function(response) {return response.json();})
  .then(function(response) {                  
    if( response.success ){
      window.location.href = "home_"+$("#rol_usuario").val()+".php";  
    } else {                       
      $("#div_cargando").fadeOut();
      mostrar_alerta(response.reason);
    }
  })
  .catch( function(error){      
    $("#div_cargando").fadeOut();
    console.error(error);
    mostrar_alerta("No se pudo conectar al servicio correctamente");      
  });
}

function cerrar_alerta(){
  $('#mensaje_form').fadeOut('slow');
  $("#popup").fadeOut();
  $('.popup-overlay').fadeOut('slow');
}

function mostrar_alerta(mensaje){
  $('#texto_mensaje').html(mensaje);
  $('#mensaje_form').fadeIn('slow');
  $('#fondo_pop').fadeIn();
}