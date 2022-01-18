<?php
require_once '../config/database.php';

session_name("LoyolaReportes");
session_start();

if (!isset($_SESSION['usuario'])) {  
  die("sin_sesion");
}

$usuario = $_SESSION['usuario'];

if ($usuario->rol != "jefe") {  
  die("sin_sesion");
}

$texto="";
$where_texto = "";
if( isset( $_GET['buscar_texto'] ) ){
  $texto = $_GET['buscar_texto']; 
  $where_texto = " AND ( LOWER(inf_m.codigo) LIKE LOWER('%$texto%') OR LOWER(inf_m.detalle) LIKE LOWER('%$texto%') ) ";
}
$pagina_actual = 0;
if( isset($_GET["pagina_actual"]) ){
  $pagina_actual = $_GET["pagina_actual"];
}
$items_x_pagina = 5;

$total_items = ORM::for_table('informe')
        ->raw_query(
        " SELECT count(inf.id) total ".
        " FROM informe inf".
        " LEFT JOIN informe_maestro inf_m ON ( inf_m.id = inf.id_informe_padre )".
        " WHERE inf.deleted_at IS NULL AND inf.estado='en_proceso'". 
        " AND inf.id_usuario= $usuario->id")
         ->find_one();

$total_items = $total_items->total;

$informes = ORM::for_table('informe')
        ->raw_query(
        " SELECT inf_m.codigo, inf_m.detalle, inf.id, inf.fecha_limite, inf.created_at, inf.avance ".
        " FROM informe inf".
        " LEFT JOIN informe_maestro inf_m ON ( inf_m.id = inf.id_informe_padre )".
        " WHERE inf.deleted_at IS NULL AND inf.estado='en_proceso'".
        " AND inf.id_usuario= $usuario->id ".
        " ORDER BY inf.created_at desc ".
        " LIMIT ".($pagina_actual*$items_x_pagina).", $items_x_pagina")
        ->find_many();

?>
<div class="buscador row">
  <div class="col">
    <label class="left">Buscar: </label>
    <div class="left"><input id="buscar_texto" type="text" value='<?=isset( $_GET['buscar_texto'] )?$_GET['buscar_texto']:''?>'/></div>
    <div id="btn_buscar" class="left cursor"><img src="img/ico_buscar.png" title="Busqueda por código, periodo"/></div>
  </div>
</div>
<?php
if (count($informes) <= 0) {
  echo "No hay informes pendientes";
} else { 
?>
<div class="css_listado">
  <div class="row css_row_alerta">           
    <div class="col-lg">Nro.</div>     
    <div class="col-lg">Codigo</div>     
    <div class="col-lg">Detalle</div>
    <div class="col-lg">Tiempo Restante</div>   
    <div class="col-lg">Fecha Límite</div>
    <div class="col-lg">Avance</div>
    <div class="col-lg">Acciones</div>
  </div>

  <?php
  $index = 1;
  foreach ($informes as $informe) { 
    
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
      $tiempo_restante = "Tiene $dias $horas $minutos para enviar";
    } else {
      $tiempo_restante = "Fuera de Tiempo";
    }
    
    if($tiempo_restante == "Fuera de Tiempo"){ 
      echo '<div class="row css_row fila_alerta_roja">';
    }
    elseif($dias > 1 && $dias <= 2){
      echo '<div class="row css_row fila_alerta_naranja">';
    }
    elseif( $dias <= 1 &&(($horas > 1 && $horas < 24) || ($horas >= 1 && ($minutos >10 && $minutos <=60 ))) ){
      echo '<div class="row css_row fila_alerta_lila">';
    }
    elseif($dias >= 3 && $dias < 6){
      echo '<div class="row css_row fila_alerta_amarillo">';
    }  else {
      echo '<div class="row css_row fila_alerta_blanco">';
    }
    ?>
      <div class="col-lg"><?= $index ++ ?></div>
      <div class="col-lg"><?= $informe->codigo ?></div>      
      <div class="col-lg"><?= $informe->detalle ?></div> 
      <div class="col-lg"><?=$tiempo_restante?></div>   
      <div class="col-lg"><?= (new DateTime($informe->fecha_limite))->format("d-m-Y") ?></div>   
      <div class="col-lg"><?=$informe->avance."%"?></div>
      <div class="col-lg"><a href="informe.php?id_informe=<?=$informe->id?>" class="btn_opciones">Editar informe</a></div>
    </div>  
  <?php } ?>                              

<div class="espacio"></div>
<?php } ?>
<?php include("../paginacion.php"); ?>          
