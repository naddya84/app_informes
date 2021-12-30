var my_drop;
var fotos_informe = [];
Dropzone.options.myDropzone = {
  paramName: "file", // The name that will be used to transfer the file
  maxFilesize: 20, // MB
  maxFiles: 1,
  acceptedFiles: "image/*",
  forceChunking: true,
  resizeQuality: 1,
  resizeWidth: 1500,
  dictDefaultMessage: "<center><div class='left margen_sms_dropzone'>Sube la foto de tu firma</div><img src='img/ico_subir_foto_doc.png' class='left'></center><br>",
  dictFallbackMessage: "Tu navegador no soporta la subida de archivos",
  dictFileTooBig: "El archivo que intentas subir pesa mucho {{filesize}}, límite {{maxFilesize}} ",
  dictInvalidFileType: "Solo se puede subir una imágen",
  dictRemoveFile: "<div class='font_borrar_img'>Borrar</div>",
  addRemoveLinks: true,
  init: function () {
    my_drop = this;
    this.on("success", function (file, response) {
      try{
        response = JSON.parse( response );
        if( response.success ){          
          file_upload = file;
          fotos_informe.push( file.name );     
        } else {
          mostrar_alerta("No se pudo subir las fotos, guarde los cambios y actualice la pagina: "+response.reason);
        }
      } catch ( error ){
        mostrar_alerta("No se pudo subir las fotos, guarde los cambios y actualice la pagina");
      } 
    });       
    this.on("removedfile", function(file) {         
      delete_foto(file.name, fotos_informe);
    });
  }
};

function iniciar(){  
  $("#div_cargando").fadeOut();
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
  
  $("#btn_finalizar").click(function(){
    if(validar_datos()){ 
      $("#div_cargando").fadeIn();
      estado = "finalizado";
      registrar_informe(estado);
    }
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
  if ($.trim($("#avance_informe").val()) < 0 || $.trim($("#avance_informe").val()) > 100){
      mostrar_alerta("Por favor ingrese un porcentaje de avance entre 0 - 100");  
      return false;
  }
  if ($.trim($("#avance_informe").val()) === '' || !(/^\d*$/.test($("#avance_informe").val()))) {
      mostrar_alerta("Por favor ingrese el % de avance del informe, solo números");    
      return false;
  }
  if (!$("input[name='envio']:checked").val()) {  
      mostrar_alerta("Debe seleccionar una opción de la sección tipo de envio");        
      return false;
  }
  if ($.trim($("#sistema_modulo").val()) === '') {
      mostrar_alerta("Ingresa el nombre del sistema o modulo, por favor");
      return false;
  }
  if (!$("input[name='multa']:checked").val()) {  
      mostrar_alerta("En multa, debe seleccionar una opción");        
      return false;
  } 
  if ( $.trim($("#fecha_limite").val()) == ""){
      mostrar_alerta("Debes ingresar la fecha limite de envio del informe");
      return false;
  }
  if (!es_email_valido($.trim($("#email").val()))) {  
      mostrar_alerta("Ingresa una dirección de correo electrónica válida");        
      return false;
  }
  if ( $.trim($("#institucion").val()) == ""){
      mostrar_alerta("Debes seleccionar una institucion, por favor");
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
    avance_informe: $("#avance_informe").val(),
    sistema_modulo: $("#sistema_modulo").val(),
    multa: $("input[name='multa']:checked").val(),
    fecha_limite: $("#fecha_limite").val(),
    email: $.trim( $("#email").val() ),
    id_institucion: $("#institucion").val() ,
    fotos_informe: fotos_informe,
    estado: estado
  };
  if($("#id_informe").val() > 0){
    data.id_informe = $("#id_informe").val();
  }
  fetch('services/set_informe.php',  {
    method: 'POST',
    credentials: 'same-origin',
    body: JSON.stringify(data), 
    headers:{ 'Content-Type': 'application/json'}
  })
  .then(function(response) {return response.json();})
  .then(function(response) {                  
    if( response.success ){  
       window.location.href = "home_gerente.php";  
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

function delete_file(name_file, array) {
  var url = 'services/deletesupload.php';
  var data = {name_delete: name_file};

  fetch(url, {
    method: 'POST', // or 'PUT'
    body: JSON.stringify(data), // data can be `string` or {object}!
    headers: {
      'Content-Type': 'application/json'
    }
  }).then(res => res.json())
  .catch(error => console.error('Error:', error))
  .then( function (response) {    

    var index = array.indexOf(name_file);
    if (index > -1) {
      array.splice(index, 1);
    }    

  });
}