<?php
require_once 'config/database.php';

session_name("LoyolaReportes");
session_start();

if (!isset($_SESSION['usuario'])) {
  header('Location: index.php');
  die();
}

$usuario = $_SESSION['usuario'];

$where_institucion = ""; $id_institucion = ""; 
if( isset( $_GET['institucion'] ) ){
  $id_institucion = $_GET['institucion']; 
  $where_institucion = " AND id_institucion = $id_institucion";
}
$instituciones = ORM::for_table('institucion')->find_many();

$pagina_actual = 0;
if( isset($_GET["pagina_actual"]) ){
  $pagina_actual = $_GET["pagina_actual"];
}

$items_x_pagina = 5;

$tiempo_inicial = new DateTime();  
$tiempo_inicial->modify("- 30 day");  
$fecha_inicial = $tiempo_inicial->format("Y-m-d");  
$fecha_final = date('Y-m-d', time()); 


$where_fecha = "";
if( isset( $_GET['fecha_ini'] ) ){
  $fecha_inicial = $_GET['fecha_ini'];  
  $fecha_final = $_GET['fecha_fin'];     
  if( $fecha_inicial != "" && $fecha_final != "" ){    
    $where_fecha = " AND created_at between '$fecha_inicial' AND '$fecha_final 23:59:59' ";
  }
}
 $total_items = ORM::for_table('informe')
        ->raw_query(
        " SELECT count(id) total from informe ".
        " WHERE deleted_at IS NULL".
        $where_institucion.
        $where_fecha )
        ->find_one();

$total_items = $total_items->total;

$informes = ORM::for_table('informe')
        ->raw_query(
        " SELECT * from informe ".
        " WHERE deleted_at IS NULL".
        $where_institucion.
        $where_fecha.        
        " ORDER BY  created_at asc ".
        " LIMIT ".($pagina_actual*$items_x_pagina).", $items_x_pagina")
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
    <script src="js/reporte_x_institucion.js?v=1"></script>
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
          <div class="espacio"></div>
          <div class="center"><h4 class="color_plomo">Reporte x Institución</h4></div>
          <a href id="btn_generar_excel"><img src="img/ico_excel.png" class="left"/><span class="color_verde" >Generar Excel</span></a>
          <div class="espacio"></div>
          <div class="row">
            <div class="col-md">
              <span class="left">Institución:</span>
              <select id="id_institucion" class="form-control">
              <?php foreach ($instituciones as $institucion){ ?>
                <option value="<?=$institucion->id?>" <?=$institucion->id==$id_institucion?"selected":""?>><?=$institucion->nombre?></option>
              <?php } ?>
              </select>
            </div>
            <div class="col-md">
              <span class="left">Fecha Inicial:  </span>
              <input id="fecha_inicio" type="text" class="form-control left" readonly <?= isset($_GET['fecha_ini']) ? "value='" .$_GET['fecha_ini']. "'" : "value='" .$fecha_inicial . "'"?>/>
            </div>
            <div class="col-md">
              <span class="left">Fecha Final:  </span>
              <input id="fecha_fin" type="text" class="form-control  left" readonly <?= isset($_GET['fecha_fin']) ? "value='" .$_GET['fecha_fin']. "'" : "value='" .$fecha_final . "'"?>/>
            </div>
            <div class="col-md-2">
               <span class="btn_limpiar css_borrar_f" title="Borrar Fechas"><img src="img/ico_borrar_fecha.png" ></span>
               <img src="img/ico_buscar.png" id="btn_buscar">
            </div>
          </div>
          <div class="margen"></div>
           <?php
        if (count($informes) <= 0) {
          echo "<div class='color_plomo'>No hay informes x institución en este momento</div>";
        } else {
          ?>
            <?php 
            
              $mostrados = $total_items;
              if( $total_items > $items_x_pagina ){
                $mostrados = (($pagina_actual+1)*$items_x_pagina);
              } 

              if( $mostrados > $total_items ) {
                $mostrados = $total_items;
              } ?>
            <div class="color_verde">Listando <?=$mostrados." de ".$total_items?> </div>
       
          <div class="espacio"></div>
          <div class="row css_tabla"> 
            <div class="col-lg-1">Nº</div>
            <div class="col-lg">Código</div>
            <div class="col-lg">detalle</div>
            <div class="col-lg">Sistema-Modulo</div>
            <div class="col-lg">Responsable</div>
            <div class="col-lg">Fecha Límite</div>
            <div class="col-lg">Tiempo Restante</div>
            <div class="col-lg">Avance</div>
            <div class="col-lg">Estado</div>
            <div class="col-lg"></div>
          </div>

          <?php
          $index = 1 + ($items_x_pagina *$pagina_actual);
          foreach ($informes as $informe) {
            $tiempo_restante = "";
            $responsable = ORM::for_table("usuario")->select("fullname")->find_one( $informe->id_usuario );
            
            $limite = new DateTime($informe->fecha_limite);
            $d1= new DateTime(); 
            $d2= $limite;

            $dias = $horas = $minutos = "";
            if( $d1 < $d2 ){ 
              $interval= $d1->diff($d2);    
            if( $interval->days > 0 ){
              $dias = $interval->days." dias ";
            }            
            if( $interval->h > 0 ){
              $horas = $interval->h." hrs.";      
            }    
            if( $interval->i > 0 ){
              $minutos = $interval->i." min.";      
            }    
            $restante = "Tiene $dias $horas $minutos para enviar";
            } else {
              $restante = "Fuera de Tiempo";
            }
            ?>
            <div class="row <?=$restante=="Fuera de Tiempo"?'css_advertencia':'css_row'?>">
              <div class="col-lg-1"><?=$index ++ ?></div>
              <div class="col-lg"><?=$informe->codigo ?></div>
              <div class="col-lg"><?=$informe->detalle ?></div>
              <div class="col-lg"><?=$informe->sistema_modulo ?></div>  
              <div class="col-lg"><?= $responsable->fullname ?></div>  
              <div class="col-lg"><?= (new DateTime($informe->created_at ))->format("d-m-Y") ?></div>
              <div class="col-lg"><?= $restante ?></div>
              <div class="col-lg"><?=$informe->avance." %"?></div>
              <div class="col-lg"><?=$informe->estado?></div>
              <div class="col-lg"><a target='_blank' href='detalle_informe.php?id_informe=<?=$informe->id?>' class="btn_opciones">Ver Informe</a></div>  
            </div>
          <?php } ?>                  
          <div class="espacio"></div>
          <?php } ?>
        </div>
      
      <?php include("paginacion.php");  ?>    
      </div>
    </div>
    <?php include("pie_pagina.php"); ?>
    <div class="menu_overlay"></div>
    <div id="div_cargando" class="fondo_block">
      <img src="img/cargando.gif" class="img_cargando">
    </div>
  </body>  
</html>
