<?php 
require_once 'config/database.php';
session_name("LoyolaReportes");
session_start();

if (!isset($_SESSION['usuario'])) {
  header('Location: index.php');
  die();
}
$usuario = $_SESSION['usuario'];

$pagina_actual = 0;
if( isset($_GET["pagina_actual"]) ){
  $pagina_actual = $_GET["pagina_actual"];
}
$items_x_pagina = 5;

$total_items = ORM::for_table('alerta')
        ->where('id_usuario_alerta', $usuario->id)
        ->where('visto', 0)
        ->count();

$items = ORM::for_table('alerta')
         ->where('id_usuario_alerta', $usuario->id)
         ->where('visto', 0)
         ->limit( $items_x_pagina )
         ->offset( $pagina_actual*$items_x_pagina )
         ->order_by_desc("id")
         ->find_many();

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
    <script src="js/notificaciones.js?v=1"></script>
    
    <script type="text/javascript">
      $(document).ready(function () {
        iniciar();
      });
    </script>
    <title>Loyola Informes</title>
  </head>
  <body>
    <div class="container-fluid">
      <?php include 'cabecera.php';?>
      <div class="css_linea"></div>
      <div class="fondo_central">
        <div class="container">
        <div class="espacio"></div>
        <div class="center"><h4>Notificaciones</h4></div>  
        <div class="row css_tabla">  
          <div class="col-lg-6">Evento</div>
          <div class="col-lg-2">Usuario</div>
          <div class="col-lg-2">Fecha</div>
          <div class="col-lg-1">Acción</div>                
        </div>
        <?php
        foreach ($items as $item) {            
          $usuario = ORM::for_table("usuario")->find_one( $item->id_usuario );
        ?>
          <div class="row css_row <?=$item->visto==0?'fila_no_vista':'fila_vista'?>">
            <div class="col-lg-6"><?= $item->descripcion ?></div>
            <div class="col-lg-2"><?= $usuario->fullname ?></div>
            <div class="col-lg-2"><?= (new DateTime($item->created_at ))->format("d-m-Y H:i:s") ?></div>
            <div class="col-lg-1"><div data-url="<?= $item->url ?>" data-id_item="<?=$item->id?>" class="pointer ver_alerta font_dato">Ver</div></div>
          </div>
        <?php } ?>  
        <?php include("paginacion.php"); ?>
      </div>
      </div>
        <?php include 'pie_pagina.php'; ?>
    </div>
    <div id="div_cargando" class="fondo_block">
      <img src="img/cargando.gif" class="img_cargando">
    </div>
  </body>
</html>