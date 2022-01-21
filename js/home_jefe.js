var pagina_actual = 0;  
var busqueda = "";

function iniciar(lista){
  //set_actividad( {actividad:"Home Jefe"} );   
  if( lista == "finalizada"){
    cargar_finalizados();
  } else{
    cargar_pendientes();
  }
  $("#tab_pendientes").click( function (){
    $("#div_cargando").show();
    pagina_actual = 0;
    cargar_pendientes();
  });
  
  $("#tab_finalizados").click( function (){ 
    $("#div_cargando").show();
    pagina_actual = 0;
    cargar_finalizados();
  }); 
  $("#tab_en_proceso").click( function (){ 
    $("#div_cargando").show();
    pagina_actual = 0;
    cargar_en_proceso();
  });
  $(".overlay_alerta").click( function (){ 
    $("#mensaje").fadeOut();
    $("#fondo_pop").fadeOut();
  });
  
  $("#abrir_alertas").click( function (){ 
    cargar_alertas();
  });
  $("#btn_compartir").click( function (){ 
    $("#div_email").fadeIn();
    $("#btn_compartir").hide();
  });
  $("#btn_enviar_email").click( function (){ 
    enviar_email();
  });
}

function cargar_pendientes(){ 
  fetch('fragmentos/lista_pendientes_jefe.php?pagina_actual='+pagina_actual+busqueda,  {
    method: 'GET',
    credentials: 'same-origin',    
    mode: 'no-cors',
    headers:{
      'Content-Type': 'text/html'
    }
  })      
  .then((res) => {return res.text();})
  .then(function(response) {  
    $("#tab_pendientes").addClass("tab_home_sel");
    $("#tab_en_proceso").removeClass("tab_home_sel");
    $("#tab_finalizados").removeClass("tab_home_sel");
            
    if( response != "sin_sesion" ){
      $("#div_contenido").html(response);      
               
      $("#btn_buscar").click( function () { 
        busqueda = "&buscar_texto="+$("#buscar_texto").val()+"&id_institucion="+$("#institucion").val();            
        cargar_pendientes();
      });
      $('.btn_paginacion').click( function (){   
        $("#div_cargando").fadeIn();
        pagina_actual = $(this).data("pagina");
        // busqueda = "&buscar_texto="+$("#texto_buscar").val()+"&id_institucion="+$("#institucion").val();
        busqueda = "&buscar_texto="+$("#texto_buscar").val();
        cargar_pendientes();
      });  
       
      $("#institucion").change( function () {    
        busqueda = "&id_institucion="+$("#institucion").val();            
        cargar_pendientes();
      });
      $("#div_cargando").fadeOut();      
    } else {
      //No tiene sesion mandamos al inicio
      window.location.href = "index.php";
    }            
  })
  .catch( function(error){        
    console.error(error);        
    mostrar_alerta("No se pudo acceder a la lista de informes pendientes");
  });
}
function cargar_en_proceso(){ 
  fetch('fragmentos/lista_en_proceso_jefe.php?pagina_actual='+pagina_actual+busqueda,  {
    method: 'GET',
    credentials: 'same-origin',    
    mode: 'no-cors',
    headers:{
      'Content-Type': 'text/html'
    }
  })      
  .then((res) => {return res.text();})
  .then(function(response) {  
    $("#tab_en_proceso").addClass("tab_home_sel");
    $("#tab_pendientes").removeClass("tab_home_sel");
    $("#tab_finalizados").removeClass("tab_home_sel");
            
    if( response != "sin_sesion" ){
      $("#div_contenido").html(response);      
               
      $('.btn_paginacion').click( function (){   
        $("#div_cargando").fadeIn();
        pagina_actual = $(this).data("pagina");
        busqueda = "&buscar_texto="+$("#texto_buscar").val();
        cargar_en_proceso();
      });  
      $("#div_cargando").fadeOut();      
    } else {
      //No tiene sesion mandamos al inicio
      window.location.href = "index.php";
    }            
  })
  .catch( function(error){        
    console.error(error);        
    mostrar_alerta("No se pudo acceder a la lista de informes en proceso");
  });
}
function cargar_finalizados(){ 
  fetch('fragmentos/lista_finalizados_jefe.php?pagina_actual='+pagina_actual+busqueda,  {
    method: 'GET',
    credentials: 'same-origin',    
    mode: 'no-cors',
    headers:{
      'Content-Type': 'text/html'
    }
  })      
  .then((res) => {return res.text();})
  .then(function(response) {  
    $("#tab_pendientes").removeClass("tab_home_sel");
    $("#tab_en_proceso").removeClass("tab_home_sel");
    $("#tab_finalizados").addClass("tab_home_sel");
    
            
    if( response != "sin_sesion" ){
      $("#div_contenido").html(response);      
      //Botones paginacion
      $('.btn_paginacion').click( function (){   
        $("#div_cargando").fadeIn();
        pagina_actual = $(this).data("pagina")
        busqueda = "&buscar_texto="+$("#texto_buscar_r").val()+"&id_institucion="+$("#institucion").val();
        cargar_finalizados();
      });            
      
      $("#institucion").change( function () {    
        busqueda = "&id_institucion="+$("#institucion").val();            
        cargar_finalizados();
      });
      //Busquedas y orden
      $("#btn_buscar").click( function () {    
        busqueda = "&texto_buscar="+$("#texto_buscar_r").val()+"&id_institucion="+$("#institucion").val();
        cargar_finalizados();
      });
            
      $("#div_cargando").fadeOut();      
    } else {
      //No tiene sesion mandamos al inicio
      window.location.href = "index.php";
    }            
  })
  .catch( function(error){        
    console.error(error);        
    mostrar_alerta("No se pudo acceder a la lista de informes finalizados");
  });
}
function cargar_alertas(){ 
  fetch('pop_alerta.php?session=iniciar',  {
    method: 'GET',
    credentials: 'same-origin',    
    mode: 'no-cors',
    headers:{
      'Content-Type': 'text/html'
    }
  })      
  .then((res) => {return res.text();})
  .then(function(response) {  
    $("#div_alerta").html(response);      
    $("#div_cargando").fadeOut();
    
    $(".overlay_alerta").click( function (){ 
      $("#mensaje").fadeOut();
      $("#fondo_pop").fadeOut();
    });
    
    $("#btn_compartir").click( function (){ 
      $("#div_email").fadeIn();
      $("#btn_compartir").hide();
    });

    $("#btn_enviar_email").click( function (){ 
      $("#div_cargando").fadeIn();
      enviar_email();
    });
  })
  .catch( function(error){        
    console.error(error);        
    mostrar_alerta("No se pudo acceder a las alertas");
  });
}

function enviar_email(){
  var data = {
    email: $.trim($("#email").val()),
    id_usuario: $("#id_usuario").val()
  }
  
  fetch('services/set_enviar_alertas.php',  {
    method: 'POST',
    credentials: 'same-origin',
    body: JSON.stringify(data), 
    headers:{ 'Content-Type': 'application/json'}
  })      
  
  .then(function(response) {return response.json();})
  .then(function(response) {                  
    if( response.success ){  
      $("#email").val("");
      $("#mensaje").fadeOut();
      $("#fondo_pop").fadeOut();
      $("#div_cargando").fadeOut();
      mostrar_alerta("El email se envio de forma exitosa!!!");
      
      $("#btn_cerrar").click( function () {               
        cerrar_alerta();
      });
    } else {                       
      $("#div_cargando").fadeOut();
      mostrar_alerta(response.reason);
    }
  })
  .catch( function(error){        
    console.error(error);        
    mostrar_alerta("No se pudo enviar el email");
  });
}

