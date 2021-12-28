<?php 
session_name("LoyolaReportes");
session_start();

unset($_SESSION['usuario']);

header('X-Frame-Options: SAMEORIGIN'); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">  
    <link href="css/style.css?v=1" media="screen" rel="stylesheet" type="text/css" />

    <script src="js/libs/jquery-3.3.1.min.js"></script>    
    <script src="js/libs/js.cookie.js"></script>    
    <script src="bootstrap/js/bootstrap.min.js"></script>        
    <script src="js/login.js?v=1"></script>
    
    <script type="text/javascript">
      $(document).ready(function () {        
        iniciar();                                    
      });            
    </script>
    <title>Loyola Informes</title>
  </head>
  <body>

    <div class="container-fluid">    
      <div class="center">
        <div class="contenedor_loguin">
          <div class="contenedor_datos">
            <div class="tm_contenedor left">
               <img src="img/img_loguin1.jpg" class="img_loguin">
            </div>
            <div class="tm_contenedor_datos left">
              <div class="center">
                <img src="img/logo.png" class="css_logo">
              </div>
              <div class="font_texto">Loguin</div>
              <div class="contenedor_input">
                <img src="img/ico_user.png" class="left">
                <input id="usuario" type="text" placeholder="Usuario" class="datos_fom" autocomplete="off">
                <div class="linea"></div>
              </div>
              <div class="margen_top"></div>
              <div class="contenedor_input">
                <img src="img/ico_password.png" class="left">
                <input id="clave" type="password" placeholder="Contraseña" class="datos_fom" autocomplete="off">
                <div class="linea"></div>
              </div>
              <div class="margen_top"></div>
              <div class="center">
                <div id="btn_ingresar">INGRESAR</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--Ventana emergente-->
      <div id="fondo_pop" class="popup-overlay"></div>
      <div id="mensaje_form" class="popup" >
        <div class="content-popup">
          <div id="btn_cerrar"></div>
          <div>
            <div id="texto_mensaje"> </div>
          </div>
        </div>
      </div>
    </div>
    <div id="div_cargando" class="fondo_block">
      <img src="img/cargando.gif" class="img_cargando">
    </div>
  </body>
</html>