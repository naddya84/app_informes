<?php
require_once 'config/database.php';

session_name("LoyolaReportes");
session_start();

if (!isset($_SESSION['usuario'])) {
  header('Location: index.php');
  die();
}

$usuario = $_SESSION['usuario'];

if( !$usuario->rol == "administrador" ){
  header('Location: index.php');
  die();
}

$informes = ORM::for_table('informe_maestro')
        ->raw_query(
        " SELECT * from informe_maestro ".
        " WHERE tipo_periodo='semestral' OR tipo_periodo='trimestral' OR tipo_periodo='anual'".
        " AND deleted_at IS NULL".        
        " ORDER BY  created_at asc ")
        ->find_many();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">          
    <link href="css/jquery-ui.css" media="screen" rel="stylesheet" type="text/css" >
    <link href="css/style.css" media="screen" rel="stylesheet" type="text/css" />

    <script src="js/libs/jquery-3.3.1.min.js"></script>
    <script src="js/libs/jquery-ui.js"></script>        
    <script src="bootstrap/js/bootstrap.min.js"></script> 
    <script src="js/administrar_informes.js?v=1"></script>
     <script src="js/alls.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        iniciar();
      });
    </script>
</head>
<body>
  <div class="container-fluid">
      <?php include("cabecera.php"); ?>
      <div class="fondo_central">
        <div class="css_linea"></div>
        <div class="contenedor_datos_reporte">
          
          <div class="margen"></div>
           <?php
        if (count($informes) <= 0) {
          echo "<div class='color_plomo'>No hay informes registrados en este momento</div>";
        } else {  ?>
          <div class="espacio"></div>
          <div class="center"><h5>Selección de Informes Nueva Gestión</h5></div>
          <div class="margen"></div>
          <div class="row css_tabla"> 
            <div class="col-lg-1 tm_check"> <input id="chk_todos" type="checkbox"/> </div>
            <div class="col-lg">Código</div>
            <div class="col-lg">detalle</div>
            <div class="col-lg">Periodo</div>
            <div class="col-lg">Fecha Envio</div>
            <div class="col-lg">Responsables</div>
          </div>

          <?php
          foreach ($informes as $informe) {
            $responsable_2=""; $responsable = ""; 
            if($informe->id_usuario != null){
              $responsable = ORM::for_table("usuario")->select("rol_detalle")->find_one( $informe->id_usuario );
              $responsable = $responsable->rol_detalle;
            }
            if($informe->id_usuario_2 != null){
              $responsable_2 = ORM::for_table("usuario")->select("rol_detalle")->find_one( $informe->id_usuario_2 );
              $responsable_2 = " - ".$responsable_2->rol_detalle;
            }
            ?>
            <div class="row css_row">
              <div class="col-lg-1 tm_check"><input class="chk_item" type="checkbox" data-id="<?= $informe->id ?>"></div>
              <div class="col-lg"><?=$informe->codigo ?></div>
              <div class="col-lg"><?=$informe->detalle ?></div>
              <div class="col-lg"><?=$informe->tipo_periodo ?></div> 
              <div class="col-lg"><?=$informe->plazo_envio ?></div> 
              <div class="col-lg"><?=$responsable.$responsable_2 ?></div> 
            </div>
           <?php  } ?>                  
          <div class="espacio"></div>
          <div class="center"><div class="btn css_btn" id="btn_informes_gestion">Cargar Informes</div></div>
          <div class="espacio"></div>
          <?php } ?>
        </div>  
      </div>
    </div>
    <?php include("pie_pagina.php"); ?>
    <!--Ventana emergente para de confirmacion alertas -->        
    <div id="fondo_pop_c" class="popup-overlay"></div>
      <div id="pop_confirmar" class="popup" >
      <div class="content-popup">
        <div id="btn_cerrar_confirmar"></div>
        <div>
          <div id="texto_mensaje_confirmar"> </div>
        </div>
        <br>
          <div class="row">
            <div class="col-2 align-self-start"></div>
            <div class="col align-self-center">
              <div id="btn_aceptar_confirmar" class="btn btn-warning left">Aceptar</div>
              <div id="btn_cancelar_confirmar" class="btn btn-dark left" style="margin-left: 4%">Cancelar</div>
            </div>
          </div>
      </div>
    </div>
    <div id="fondo_pop" class="popup-overlay"></div>
    <div id="mensaje_form" class="popup" >
      <div class="content-popup">
        <div id="btn_cerrar" class="btn_cerrar_pop"></div>
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