var pagina_actual = 0;

function iniciar(){ 
  $("#div_cargando").fadeOut();
    
  $("#fecha_inicio").datepicker({
    changeMonth: true,
    changeYear: true,
    yearRange: "2018:2028",
    dateFormat: "yy-mm-dd",
    onClose: function (selectedDate) {
      $("#fecha_fin").datepicker("option", "minDate", selectedDate);
    }
  }); 
  
  $("#fecha_fin").datepicker({
    changeMonth: true,
    changeYear: true,
    yearRange: "2018:2028",
    dateFormat: "yy-mm-dd",
    onClose: function (selectedDate) {
      $("#fecha_inicio").datepicker("option", "maxDate", selectedDate);
    }
  }); 
  
  //Botones paginacion
  $('.btn_paginacion').click( function (){   
    $("#div_cargando").fadeIn();
    pagina_actual = $(this).data("pagina");
    
    var institucion = "";
    if( $("#id_institucion").val() != "" ){
      institucion = '&institucion='+$("#id_institucion").val();
    }      
    window.location.href = 'reporte_x_institucion.php?'+"pagina_actual="+pagina_actual+institucion+"&fecha_ini="+$("#fecha_inicio").val()+"&fecha_fin="+$("#fecha_fin").val();    
  }); 
  
  $(".btn_limpiar").click( function (){
    $("#fecha_fin").val("");
    $("#fecha_inicio").val("");
    $("#fecha_fin").datepicker("option", "minDate", "");
    $("#fecha_inicio").datepicker("option", "maxDate", "");
  });
  
 
  $("#btn_buscar").click( function () {
    if( validar_datos() ){ 
      window.location.href = 'reporte_x_institucion.php?'+'institucion='+$("#id_institucion").val()+"&fecha_ini="+$("#fecha_inicio").val()+"&fecha_fin="+$("#fecha_fin").val();    
    } 
  });
  
  $("#btn_generar_excel").click( function (){
    var data = {
      fecha_inicial: $("#fecha_inicio").val(),
      fecha_final: $("#fecha_fin").val(),
      id_institucion: $("#id_institucion").val()
    };
    window.open('services/reportes/reporte_x_institucion.php?data='+encodeURI(JSON.stringify(data)), '_blank');         
  });
}
//validacion de fechas
function validar_datos() {
  $("#div_cargando").fadeOut();
  if ($.trim($("#fecha_inicio").val()) === '' && $.trim($("#fecha_fin").val()) === '') {     
    return true;
  }
    
  if ($.trim($("#fecha_fin").val()) === '' && $.trim($("#fecha_inicio").val()) !== "") {
    mostrar_alerta("Debe ingresar la fecha final del informe, por favor");        
    return false;
  }
  if ($.trim($("#fecha_fin").val()) !== '' && $.trim($("#fecha_inicio").val()) === "") {
    mostrar_alerta("Debe ingresar la fecha inicial del informe, por favor");        
    return false;
  }
  return true;
}