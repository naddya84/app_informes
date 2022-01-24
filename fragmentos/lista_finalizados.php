<?php
require_once '../config/database.php';

session_name("LoyolaReportes");
session_start();

if (!isset($_SESSION['usuario'])) {  
  die("sin_sesion");
}

$usuario = $_SESSION['usuario'];

$texto="";
$where_texto = "";
if( isset( $_GET['texto_buscar'] ) ){
  $texto = $_GET['texto_buscar']; 
  $where_texto = " AND ( LOWER(codigo) LIKE LOWER('%$texto%') OR LOWER(detalle) LIKE LOWER('%$texto%') ) ";
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
        " WHERE inf.deleted_at IS NULL AND inf.estado='finalizado' ".
        " AND inf.id_usuario= $usuario->id")
         ->find_one();

$total_items = $total_items->total;

$informes = ORM::for_table('informe')
        ->raw_query(
        " SELECT inf_m.codigo, inf_m.detalle, inf.id ".
        " FROM informe inf".
        " LEFT JOIN informe_maestro inf_m ON ( inf_m.id = inf.id_informe_padre )".
        " WHERE inf.deleted_at IS NULL AND inf.estado='finalizado' ".
        " AND inf.id_usuario= $usuario->id".
        " ORDER BY inf.created_at desc ".
        " LIMIT ".($pagina_actual*$items_x_pagina).", $items_x_pagina")
        ->find_many();

?>

<div class="espacio"></div>
<?php
if (count($informes) <= 0) {
  echo "No hay informes finalizados";
} else {
?>
<div class="css_listado">
  <div class="row css_tabla">           
    <div class="col-lg">Nro.</div>     
    <div class="col-lg">Codigo</div>     
    <div class="col-lg">Detalle</div>
    <div class="col-lg">Fecha Límite</div>
    <div class="col-lg">Acciones</div>
  </div>

  <?php
  $index = 1;
  foreach ($informes as $informe) { 
    ?>
    <div class="row css_row">
      <div class="col-lg"><?= $index ++ ?></div>
      <div class="col-lg"><?= $informe->codigo ?></div>      
      <div class="col-lg"><?= $informe->detalle ?></div> 
      <div class="col-lg"><?= (new DateTime($informe->fecha_limite))->format("d-m-Y") ?></div>    
      <div class="col-lg"><a href="detalle_informe.php?id_informe=<?=$informe->id?>" class="btn_opciones">Ver Detalle</a></div>
    </div>  
  <?php } ?>                              

<div class="espacio"></div>
<?php } ?>
</div>
<?php include("../paginacion.php"); ?>       