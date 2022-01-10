<?php
require_once 'config/database.php';

session_name("LoyolaReportes");
session_start();

if (!isset($_SESSION['usuario'])) {
  header('Location: index.php');
  die();
}

$usuario = $_SESSION['usuario'];

$notifs = ORM::for_table('notificacion_dispositivo')
        ->where( "id_usuario", $usuario->id )        
        ->find_many();

?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
    <meta name="viewport" content="width=device-width, initial-scale=1" >

    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" >           
    <link href="css/jquery-ui.css" media="screen" rel="stylesheet" type="text/css" >
    <link href="css/style.css" media="screen" rel="stylesheet" type="text/css" >

    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

    
    <script src="js/libs/jquery-3.3.1.min.js"></script>
    <script src="js/libs/jquery-ui.js"></script>        
    <script src="bootstrap/js/bootstrap.min.js"></script>              
    
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="js/all_js.js"></script>
    <script src="js/lista_notificaciones.js"></script>    
    <script type="text/javascript">
      $(document).ready(function () {
        iniciar();
      });
    </script>
</head>
<body>
  <div class="container-fluid fondo_guindo_cabecera">
      <?php include("cabecera.php"); ?>
  </div>
  <div class="container-fluid fondo_tabla_guindo">
    <div class="linea_rosa"></div>
    <div class="container">      
      <div class="clearfix"></div>
      <?php
      if (count($notifs) <= 0) {
        echo "<div class='font_amarillo'>No hay dispositivos registrados en este momento </div>";
      } else {
        ?>
      <center><h5 class="font_amarillo">Equipos Registrados</h5></center>
        <div class="css_listado">
          <div class="row cabecera_tabla bg-warning">  
            <div class="col-md-1">Nro </div>   
            <div class="col-md-3">Equipo</div>
            <div class="col-md-1">Movil</div>
            <div class="col-md-1">Estado</div>            
          </div>
        <?php
        $index = 1;
        foreach ($notifs as $notif) {
          ?>
          <div class="row css_row_azul">
            <div class="col-md-1"><?=$index++?></div>
            <div class="col-md-3"><?=$notif->so." ".$notif->navegador?></div>
            <div class="col-md-1"><?=$notif->movil?'Si':'No'?></div>            
            <div class="col-md-1">
              <input type="checkbox" <?=$notif->habilitado?'checked':''?> 
                     data-id="<?=$notif->id?>"
                     data-toggle="toggle" 
                     data-on="Habilitado" 
                     data-off="Deshabilitado" class='btn_toggle'></div>  
          </div>
        <?php } ?> 
        </div>        
      <?php }
      ?>  
    </div>
    <div class="margen_2"></div>
    <br>
    <div class="center"><a " href="javascript:history.back()" class="btn css_btn">VOLVER</a></div>          
  </div>

  <?php include("pie_pagina.php"); ?>
  <!--Ventana emergente contacto -->
    <div id="fondo_pop" class="popup-overlay"></div>
    <div id="mensaje_form" class="popup" >
      <div class="content-popup">
        <div id="btn_cerrar"></div>
        <div>
          <div id="texto_mensaje"> </div>
        </div>
      </div>
    </div>
    <div id="div_cargando" class="fondo_block">
      <img src="img/cargando.gif" class="img_cargando">
    </div>
</body>
</html>