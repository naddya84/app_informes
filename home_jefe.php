<?php 
require_once 'config/database.php';
session_name("LoyolaReportes");
session_start();

if (!isset($_SESSION['usuario'])) {
  header('Location: index.php');
  die();
}
$usuario = $_SESSION['usuario'];
$lista = "pendiente";
if( isset( $_GET['lista'] ) ){
  $lista = $_GET['lista'];
}
$alerta = "";
if(isset( $_GET['alerta'])){
  $alerta = $_GET['alerta'];
}
?>
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
    <script src="js/home_jefe.js?v=1.1"></script>
    
    <script type="text/javascript">
      $(document).ready(function () {
        iniciar('<?=$lista?>');
      });
    </script>
    <title>Loyola Informes</title>
  </head>
  <body>
    <div class="container-fluid">
      <?php include 'cabecera.php';?>
      <div class="css_linea"></div>
      <div class="fondo_central">
        <div class="espacio"></div>
        <div class="alertas cursor" id="abrir_alertas"><img src="img/ico_alerta.png" class="css_alerta"> Alertas</div>
        <div class="row tm_opciones">                
          <div id="tab_pendientes" class="col tab_home">PENDIENTES</div>   
          <div id="tab_finalizados" class="col tab_home">FINALIZADOS</div>   
        </div>
        <div class="fondo_tab">
          <div id="div_contenido"> </div>  
        </div>
        <div id="div_alerta">
        </div>
        <div class="espacio"></div>
      </div>
    </div>
    <?php  
    if($alerta != ""){
      include 'pop_alerta.php';
    } ?>
    <?php include 'pie_pagina.php'; ?>
    <div id="div_cargando" class="fondo_block">
      <img src="img/cargando.gif" class="img_cargando">
    </div>
  </body>
</html>
