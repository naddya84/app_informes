var pagina_actual = 0;  
var busqueda = "";  

function iniciar(lista){
  //set_actividad( {actividad:"Home Gerente"} );   
  
  if( lista == "finalizada"){
    cargar_finalizados();
  } else{
    cargar_pendientes();
  }
  
}

function cargar_pendientes(){ 
  fetch('lista_pendientes_gerente.php?pagina_actual='+pagina_actual+busqueda,  {
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
    $("#tab_finalizados").removeClass("tab_home_sel");
            
    if( response != "sin_sesion" ){
      $("#div_contenido").html(response);      
      //Botones paginacion
      $('.btn_paginacion').click( function (){   
          $("#div_cargando").fadeIn();
          pagina_actual = $(this).data("pagina")
          cargar_pendientes();
      });            
      
      
      $('#chk_todos').change(function() {
        if($(this).is(":checked")) {
         $(".chk_item").prop('checked', true);
        } else {
          $(".chk_item").prop('checked', false);
        }
      });     
      
      //Busquedas y orden
      $("#btn_buscar").click( function () {                
        window.location.href = "home_inspector.php?lista=pendientes&texto_buscar="+$("#texto_buscar_r").val();
      });

      $(".btn_columna_list").click( function (){
        busqueda = "&texto_buscar="+$("#texto_buscar").val();            
        columna_orden = "&columna_orden="+$(this).data("columna");              
        orden = "&orden="+$(this).data("orden");        
        
        $("#div_cargando").fadeIn();
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
