
function iniciar(){          
  $("#div_cargando").fadeOut(); 
  $("#scroll_fotos").mCustomScrollbar({theme: "dark"});
  
  $("#btn_volver").click( function () {    
    window.location.href = "home_"+$("#rol_usuario").val()+".php";
  });
          
}
